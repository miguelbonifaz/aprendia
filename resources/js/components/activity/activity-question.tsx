import {
    ArrowRight,
    CheckCircle2,
    CircleHelp,
    LoaderCircle,
    XCircle,
} from 'lucide-react';
import { useEffect, useRef } from 'react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';
import type { PlayableActivityItem, RecognizeAndSelectFeedback } from '@/types';

type Props = {
    item: PlayableActivityItem;
    position: number;
    total: number;
    selectedOptionId?: string;
    feedback: RecognizeAndSelectFeedback | null;
    selecting: boolean;
    finishing: boolean;
    error: string | null;
    onSelect: (optionId: string) => void;
    onContinue: () => void;
};

export function ActivityQuestion({
    item,
    position,
    total,
    selectedOptionId,
    feedback,
    selecting,
    finishing,
    error,
    onSelect,
    onContinue,
}: Props) {
    const heading = useRef<HTMLHeadingElement>(null);

    useEffect(() => {
        heading.current?.focus();
    }, [item.id]);

    return (
        <section className="grid gap-5 rounded-3xl border bg-card p-5 shadow-sm sm:p-7">
            <div className="grid gap-3">
                <div className="flex items-center justify-between text-sm font-medium">
                    <span className="text-primary">
                        Pregunta {position} de {total}
                    </span>
                    <span className="text-muted-foreground">
                        {Math.round((position / total) * 100)}%
                    </span>
                </div>
                <div
                    className="h-2 overflow-hidden rounded-full bg-muted"
                    role="progressbar"
                    aria-label="Progreso de la actividad"
                    aria-valuemin={0}
                    aria-valuemax={total}
                    aria-valuenow={position}
                >
                    <div
                        className="h-full rounded-full bg-primary transition-[width] duration-300"
                        style={{ width: `${(position / total) * 100}%` }}
                    />
                </div>
            </div>

            <h2
                ref={heading}
                tabIndex={-1}
                className="text-xl leading-snug font-bold outline-none sm:text-2xl"
            >
                {item.prompt}
            </h2>

            <div className="grid gap-3" aria-label="Opciones de respuesta">
                {item.options.map((option, index) => {
                    const isSelected = selectedOptionId === option.id;
                    const isCorrect = isSelected && feedback?.is_correct;
                    const isIncorrect =
                        isSelected && feedback && !feedback.is_correct;

                    return (
                        <button
                            key={option.id}
                            type="button"
                            aria-pressed={isSelected}
                            disabled={
                                selecting || Boolean(feedback?.is_correct)
                            }
                            onClick={() => onSelect(option.id)}
                            className={cn(
                                'flex min-h-16 w-full items-center gap-3 rounded-2xl border-2 bg-background p-3 text-left font-medium transition focus-visible:ring-3 focus-visible:ring-ring/40 focus-visible:outline-none sm:p-4',
                                'hover:border-primary/50 hover:bg-primary/5',
                                isSelected && 'border-primary bg-primary/5',
                                isCorrect &&
                                    'border-emerald-500 bg-emerald-500/10',
                                isIncorrect &&
                                    'border-destructive bg-destructive/10',
                                selecting && 'cursor-wait',
                            )}
                        >
                            <span className="flex size-9 shrink-0 items-center justify-center rounded-xl bg-muted text-sm font-bold">
                                {String.fromCharCode(65 + index)}
                            </span>
                            <span className="flex-1">{option.text}</span>
                            {isCorrect && (
                                <CheckCircle2 className="size-6 text-emerald-600" />
                            )}
                            {isIncorrect && (
                                <XCircle className="size-6 text-destructive" />
                            )}
                            {isSelected && selecting && (
                                <LoaderCircle className="size-5 animate-spin text-primary" />
                            )}
                        </button>
                    );
                })}
            </div>

            {feedback && (
                <div
                    className={cn(
                        'grid gap-2 rounded-2xl border p-4',
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

            <Button
                type="button"
                size="lg"
                className="w-full sm:ml-auto sm:w-auto"
                disabled={!feedback || selecting || finishing}
                onClick={onContinue}
            >
                {finishing && <LoaderCircle className="animate-spin" />}
                {position === total ? 'Ver resultado' : 'Siguiente pregunta'}
                {!finishing && <ArrowRight />}
            </Button>
        </section>
    );
}
