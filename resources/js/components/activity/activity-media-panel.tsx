import { ActivityPronunciationButton } from '@/components/activity/activity-pronunciation-button';
import type { PlayableActivityItem } from '@/types';

type Props = {
    item: PlayableActivityItem;
};

export function ActivityMediaPanel({ item }: Props) {
    return (
        <aside className="grid min-h-0 content-center gap-3 lg:grid-rows-[minmax(0,1fr)_auto]">
            {item.image_url && (
                <div className="flex min-h-0 items-center justify-center overflow-hidden rounded-2xl border bg-muted/30 p-2 sm:p-3">
                    <img
                        src={item.image_url}
                        alt={item.image_alt_text ?? ''}
                        className="block h-auto max-h-[58dvh] w-full object-contain lg:h-full lg:max-h-full"
                        decoding="async"
                    />
                </div>
            )}

            {item.audio_url && (
                <ActivityPronunciationButton audioUrl={item.audio_url} />
            )}
        </aside>
    );
}
