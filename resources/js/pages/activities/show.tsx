import { Head } from '@inertiajs/react';
import { ActivityHeader } from '@/components/activity/activity-header';
import { ActivityQuestion } from '@/components/activity/activity-question';
import { ActivityResult } from '@/components/activity/activity-result';
import { useActivityPlayer } from '@/hooks/use-activity-player';
import type { PlayableActivity } from '@/types';

type Props = {
    activity: PlayableActivity;
};

export default function ActivityShow({ activity }: Props) {
    const player = useActivityPlayer(activity);

    return (
        <>
            <Head title={activity.title} />
            <main className="min-h-svh bg-linear-to-b from-primary/5 via-background to-background px-4 py-4 sm:px-6 lg:h-svh lg:overflow-hidden lg:px-8 lg:py-5">
                <div className="mx-auto grid w-full max-w-7xl gap-4 lg:h-full lg:grid-rows-[auto_minmax(0,1fr)]">
                    <ActivityHeader activity={activity} />
                    {player.result ? (
                        <ActivityResult
                            result={player.result}
                            studentName={activity.student_name}
                            onRestart={player.restart}
                        />
                    ) : (
                        <ActivityQuestion
                            key={player.currentItem.id}
                            item={player.currentItem}
                            position={player.currentIndex + 1}
                            total={activity.items.length}
                            selectedOptionId={player.selectedOptionId}
                            feedback={player.feedback}
                            selecting={player.selecting}
                            finishing={player.finishing}
                            error={player.error}
                            onSelect={player.selectOption}
                            onContinue={player.advance}
                        />
                    )}
                </div>
            </main>
        </>
    );
}
