import type { HttpResponse } from '@inertiajs/core';
import { useHttp } from '@inertiajs/react';
import { useState } from 'react';
import { store as evaluateAnswer } from '@/routes/activities/answers';
import { store as evaluateResult } from '@/routes/activities/result';
import type {
    ActivityAnswerResponse,
    ActivityResultResponse,
    PlayableActivity,
    PlayableActivityResult,
    RecognizeAndSelectFeedback,
} from '@/types';

type AnswerForm = { item_id: string; selected_option_id: string };
type ResultForm = { answers: Record<string, string> };

const genericError = 'No pudimos revisar la respuesta. Inténtalo nuevamente.';

function responseError(response: HttpResponse): string {
    if (response.status === 429) {
        return 'Espera un momento antes de volver a intentarlo.';
    }

    return genericError;
}

export function useActivityPlayer(activity: PlayableActivity) {
    const [currentIndex, setCurrentIndex] = useState(0);
    const [answers, setAnswers] = useState<Record<string, string>>({});
    const [feedback, setFeedback] = useState<RecognizeAndSelectFeedback | null>(
        null,
    );
    const [result, setResult] = useState<PlayableActivityResult | null>(null);
    const [error, setError] = useState<string | null>(null);
    const answerRequest = useHttp<AnswerForm, ActivityAnswerResponse>(
        evaluateAnswer(activity.public_id),
        { item_id: '', selected_option_id: '' },
    );
    const resultRequest = useHttp<ResultForm, ActivityResultResponse>(
        evaluateResult(activity.public_id),
        { answers: {} },
    );
    const currentItem = activity.items[currentIndex];

    async function selectOption(optionId: string): Promise<void> {
        if (answerRequest.processing || feedback?.is_correct) {
            return;
        }

        const previousAnswer = answers[currentItem.id];
        const submission = {
            item_id: currentItem.id,
            selected_option_id: optionId,
        };

        setError(null);
        setFeedback(null);
        setAnswers((current) => ({ ...current, [currentItem.id]: optionId }));
        answerRequest.transform(() => submission);

        try {
            const response = await answerRequest.submit({
                onError: () => setError(genericError),
                onHttpException: (httpResponse) =>
                    setError(responseError(httpResponse)),
                onNetworkError: () => setError(genericError),
            });

            if (!response) {
                throw new Error(genericError);
            }

            setFeedback(response.feedback);
        } catch {
            setAnswers((current) => {
                const restored = { ...current };

                if (previousAnswer) {
                    restored[currentItem.id] = previousAnswer;
                } else {
                    delete restored[currentItem.id];
                }

                return restored;
            });
            setError((current) => current ?? genericError);
        }
    }

    async function advance(): Promise<void> {
        if (!feedback || resultRequest.processing) {
            return;
        }

        if (currentIndex < activity.items.length - 1) {
            setCurrentIndex((index) => index + 1);
            setFeedback(null);
            setError(null);

            return;
        }

        resultRequest.transform(() => ({ answers }));
        setError(null);

        try {
            const response = await resultRequest.submit({
                onError: () => setError(genericError),
                onHttpException: (httpResponse) =>
                    setError(responseError(httpResponse)),
                onNetworkError: () => setError(genericError),
            });

            if (!response) {
                throw new Error(genericError);
            }

            setResult(response.result);
        } catch {
            setError((current) => current ?? genericError);
        }
    }

    function restart(): void {
        setCurrentIndex(0);
        setAnswers({});
        setFeedback(null);
        setResult(null);
        setError(null);
        answerRequest.resetAndClearErrors();
        resultRequest.resetAndClearErrors();
    }

    return {
        currentItem,
        currentIndex,
        selectedOptionId: answers[currentItem.id],
        feedback,
        result,
        error,
        selecting: answerRequest.processing,
        finishing: resultRequest.processing,
        selectOption,
        advance,
        restart,
    };
}
