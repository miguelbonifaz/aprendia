import type { HttpResponse } from '@inertiajs/core';
import { useHttp } from '@inertiajs/react';
import { useState } from 'react';
import { destroy } from '@/routes/chat/conversation';
import { store } from '@/routes/chat/messages';
import type { ChatMessage } from '@/types';

type MessageForm = { message: string };
type MessageResponse = { message: ChatMessage };
type ResetResponse = { messages: ChatMessage[] };

const genericError = 'No pude completar la solicitud. Inténtalo nuevamente.';

function responseMessage(response: HttpResponse): string {
    if (response.status === 429) {
        return 'Has enviado varios mensajes. Espera un momento e inténtalo nuevamente.';
    }

    try {
        const data = JSON.parse(response.data) as { message?: unknown };

        return typeof data.message === 'string' ? data.message : genericError;
    } catch {
        return genericError;
    }
}

export function useChat(initialMessages: ChatMessage[]) {
    const [messages, setMessages] = useState(initialMessages);
    const [requestError, setRequestError] = useState<string | null>(null);
    const [resetError, setResetError] = useState<string | null>(null);
    const messageRequest = useHttp<MessageForm, MessageResponse>(store(), {
        message: '',
    });
    const resetRequest = useHttp<Record<string, never>, ResetResponse>(
        destroy(),
        {},
    );

    async function sendMessage(): Promise<void> {
        const originalMessage = messageRequest.data.message;
        const content = originalMessage.trim();

        if (!content || messageRequest.processing) {
            return;
        }

        setRequestError(null);
        messageRequest.clearErrors();
        messageRequest.transform(() => ({ message: content }));
        messageRequest.setData('message', '');
        setMessages((current) => [...current, { role: 'user', content }]);

        let failureMessage = genericError;

        try {
            const response = await messageRequest.submit({
                onError: (errors) => {
                    failureMessage = String(errors.message ?? genericError);
                },
                onHttpException: (httpResponse) => {
                    failureMessage = responseMessage(httpResponse);
                },
                onNetworkError: () => {
                    failureMessage = genericError;
                },
            });

            if (!response) {
                throw new Error(failureMessage);
            }

            setMessages((current) => [...current, response.message]);
        } catch {
            setMessages((current) => current.slice(0, -1));
            messageRequest.setData('message', originalMessage);
            setRequestError(failureMessage);
        }
    }

    async function clearConversation(): Promise<boolean> {
        let failureMessage = genericError;

        setResetError(null);

        try {
            const response = await resetRequest.submit({
                onHttpException: (httpResponse) => {
                    failureMessage = responseMessage(httpResponse);
                },
            });

            setMessages(response.messages);
            messageRequest.resetAndClearErrors();
            setRequestError(null);

            return true;
        } catch {
            setResetError(failureMessage);

            return false;
        }
    }

    return {
        messages,
        message: messageRequest.data.message,
        setMessage: (message: string) => {
            messageRequest.setData('message', message);
            setRequestError(null);
        },
        sendMessage,
        clearConversation,
        sending: messageRequest.processing,
        resetting: resetRequest.processing,
        error: requestError,
        resetError,
    };
}
