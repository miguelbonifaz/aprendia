import { BookOpen, Target, UserRound } from 'lucide-react';
import type { PlayableActivity } from '@/types';

type Props = {
    activity: PlayableActivity;
};

export function ActivityHeader({ activity }: Props) {
    return (
        <header className="grid gap-4 rounded-3xl border bg-card/95 p-4 shadow-sm backdrop-blur sm:p-5 lg:grid-cols-[minmax(0,1.25fr)_minmax(18rem,0.75fr)] lg:items-center lg:gap-6 lg:px-6 lg:py-4">
            <div className="grid min-w-0 gap-3">
                <div className="flex items-center justify-between gap-3">
                    <div className="flex items-center gap-2 text-sm font-semibold text-primary">
                        <span className="flex size-8 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm">
                            <BookOpen className="size-4" />
                        </span>
                        Hoy vamos a practicar
                    </div>
                    <div className="flex items-center gap-2 rounded-full border bg-background/80 px-3 py-1.5 text-sm font-medium">
                        <UserRound className="size-4 text-primary" />
                        <span className="max-w-32 truncate sm:max-w-52">
                            {activity.student_name}
                        </span>
                    </div>
                </div>
                <div className="grid gap-1">
                    <h1 className="text-xl leading-tight font-bold tracking-tight text-balance sm:text-2xl">
                        {activity.title}
                    </h1>
                    <p className="text-sm leading-relaxed text-pretty text-muted-foreground">
                        {activity.instructions}
                    </p>
                </div>
            </div>

            <div className="flex items-start gap-3 rounded-2xl bg-muted/70 px-4 py-3">
                <Target className="mt-0.5 size-5 shrink-0 text-primary" />
                <div className="grid gap-0.5">
                    <p className="text-sm font-semibold">Lo que aprenderás</p>
                    <p className="text-sm leading-relaxed text-pretty text-muted-foreground">
                        {activity.learning_objective}
                    </p>
                </div>
            </div>
        </header>
    );
}
