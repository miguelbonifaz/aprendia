import { CheckCircle2, CircleHelp } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { RecognizeAndSelectFeedback } from '@/types';

type Props = {
    feedback: RecognizeAndSelectFeedback | null;
    error: string | null;
};

export function ActivityResponseFeedback({ feedback, error }: Props) {
    return (
        <>
            {feedback && (
                <div
                    className={cn(
                        'grid gap-1.5 rounded-2xl border px-4 py-3',
                        feedback.is_correct
                            ? 'border-emerald-500/30 bg-emerald-500/10'
                            : 'border-amber-500/30 bg-amber-500/10',
                    )}
                    role="status"
                    aria-live="polite"
                >
                    <div className="flex items-start gap-2 font-semibold">
                        {feedback.is_correct ? (
                            <CheckCircle2 className="mt-0.5 size-5 shrink-0 text-emerald-600" />
                        ) : (
                            <CircleHelp className="mt-0.5 size-5 shrink-0 text-amber-600" />
                        )}
                        <span>{feedback.message}</span>
                    </div>
                    {feedback.hint && (
                        <p className="pl-7 text-sm text-muted-foreground">
                            Pista: {feedback.hint}
                        </p>
                    )}
                    {!feedback.is_correct && (
                        <p className="pl-7 text-sm text-muted-foreground">
                            Puedes elegir otra respuesta o continuar.
                        </p>
                    )}
                </div>
            )}

            {error && (
                <p
                    className="text-sm font-medium text-destructive"
                    role="alert"
                >
                    {error}
                </p>
            )}
        </>
    );
}
