import { ArrowRight, LoaderCircle } from 'lucide-react';
import { useEffect, useRef } from 'react';
import { ActivityMediaPanel } from '@/components/activity/activity-media-panel';
import { ActivityOptionList } from '@/components/activity/activity-option-list';
import { ActivityProgress } from '@/components/activity/activity-progress';
import { ActivityResponseFeedback } from '@/components/activity/activity-response-feedback';
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
    const hasMedia = Boolean(item.image_url || item.audio_url);

    useEffect(() => {
        heading.current?.focus();
    }, [item.id]);

    return (
        <section
            className={cn(
                'grid min-h-0 gap-4 rounded-3xl border bg-card p-4 shadow-sm sm:p-5 lg:h-full lg:gap-5',
                hasMedia &&
                    'lg:grid-cols-[minmax(0,0.82fr)_minmax(22rem,1.18fr)]',
            )}
        >
            <article className="order-2 grid min-h-0 content-start gap-4 lg:order-1 lg:overflow-y-auto lg:pr-1">
                <ActivityProgress position={position} total={total} />

                <h2
                    ref={heading}
                    tabIndex={-1}
                    className="text-2xl leading-tight font-bold tracking-tight text-balance outline-none lg:text-3xl"
                >
                    {item.prompt}
                </h2>

                <div className="grid gap-2.5">
                    <p className="text-sm font-semibold text-muted-foreground">
                        Elige una respuesta
                    </p>
                    <ActivityOptionList
                        options={item.options}
                        selectedOptionId={selectedOptionId}
                        feedback={feedback}
                        selecting={selecting}
                        onSelect={onSelect}
                    />
                </div>

                <ActivityResponseFeedback feedback={feedback} error={error} />

                <Button
                    type="button"
                    size="lg"
                    className="w-full transition-transform active:scale-[0.98] sm:ml-auto sm:w-auto"
                    disabled={!feedback || selecting || finishing}
                    onClick={onContinue}
                >
                    {finishing && <LoaderCircle className="animate-spin" />}
                    {position === total
                        ? 'Ver resultado'
                        : 'Siguiente pregunta'}
                    {!finishing && <ArrowRight />}
                </Button>
            </article>

            {hasMedia && (
                <div className="order-1 min-h-0 lg:order-2">
                    <ActivityMediaPanel item={item} />
                </div>
            )}
        </section>
    );
}
