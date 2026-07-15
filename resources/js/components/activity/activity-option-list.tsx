import { CheckCircle2, LoaderCircle, XCircle } from 'lucide-react';
import { cn } from '@/lib/utils';
import type { PlayableActivityItem, RecognizeAndSelectFeedback } from '@/types';

type Props = {
    options: PlayableActivityItem['options'];
    selectedOptionId?: string;
    feedback: RecognizeAndSelectFeedback | null;
    selecting: boolean;
    onSelect: (optionId: string) => void;
};

export function ActivityOptionList({
    options,
    selectedOptionId,
    feedback,
    selecting,
    onSelect,
}: Props) {
    return (
        <div className="grid gap-2.5" aria-label="Opciones de respuesta">
            {options.map((option, index) => {
                const isSelected = selectedOptionId === option.id;
                const isCorrect = isSelected && feedback?.is_correct;
                const isIncorrect =
                    isSelected && feedback && !feedback.is_correct;

                return (
                    <button
                        key={option.id}
                        type="button"
                        aria-pressed={isSelected}
                        disabled={selecting || Boolean(feedback?.is_correct)}
                        onClick={() => onSelect(option.id)}
                        className={cn(
                            'flex min-h-16 w-full items-center gap-3 rounded-2xl border-2 bg-background px-3 py-3 text-left text-lg font-semibold transition duration-200 focus-visible:ring-3 focus-visible:ring-ring/40 focus-visible:outline-none active:scale-[0.99]',
                            'hover:border-primary/50 hover:bg-primary/5',
                            isSelected && 'border-primary bg-primary/5',
                            isCorrect && 'border-emerald-500 bg-emerald-500/10',
                            isIncorrect &&
                                'border-destructive bg-destructive/10',
                            selecting && 'cursor-wait',
                        )}
                    >
                        <span className="flex size-10 shrink-0 items-center justify-center rounded-xl bg-muted text-base font-bold">
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
    );
}
