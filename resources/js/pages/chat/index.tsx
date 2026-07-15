import { Head } from '@inertiajs/react';
import { useState } from 'react';
import { ChatComposer } from '@/components/chat/chat-composer';
import { ChatHeader } from '@/components/chat/chat-header';
import { ChatMessages } from '@/components/chat/chat-messages';
import { useChat } from '@/hooks/use-chat';
import { index as chat } from '@/routes/chat';
import type { ChatMessage, Student } from '@/types';

type Props = {
    student: Student;
    messages: ChatMessage[];
};

export default function ChatIndex({
    student,
    messages: initialMessages,
}: Props) {
    const [dialogOpen, setDialogOpen] = useState(false);
    const conversation = useChat(initialMessages);

    async function startNewConversation() {
        if (await conversation.clearConversation()) {
            setDialogOpen(false);
        }
    }

    return (
        <>
            <Head title={`Chat con ${student.name}`} />

            <div className="flex h-[calc(100svh-4rem)] min-h-0 flex-1 flex-col overflow-hidden bg-background">
                <ChatHeader
                    student={student}
                    hasMessages={conversation.messages.length > 0}
                    dialogOpen={dialogOpen}
                    onDialogOpenChange={setDialogOpen}
                    onNewConversation={startNewConversation}
                    resetting={conversation.resetting}
                    sending={conversation.sending}
                    resetError={conversation.resetError}
                />
                <ChatMessages
                    messages={conversation.messages}
                    studentName={student.name}
                    responding={conversation.sending}
                />
                <ChatComposer
                    message={conversation.message}
                    onMessageChange={conversation.setMessage}
                    onSubmit={conversation.sendMessage}
                    processing={conversation.sending || conversation.resetting}
                    error={conversation.error}
                />
            </div>
        </>
    );
}

ChatIndex.layout = {
    breadcrumbs: [{ title: 'Chat', href: chat() }],
};
