import { BookOpen, Target, UserRound } from 'lucide-react';
import { SpeechButton } from '@/components/activity/speech-button';
import type { PlayableActivity } from '@/types';

type Props = {
    activity: PlayableActivity;
};

export function ActivityHeader({ activity }: Props) {
    const narration = [
        activity.title,
        activity.instructions,
        `Objetivo: ${activity.learning_objective}`,
    ].join('. ');

    return (
        <header className="grid gap-5">
            <div className="flex items-center justify-between gap-3">
                <div className="flex items-center gap-2 text-sm font-semibold text-primary">
                    <span className="flex size-9 items-center justify-center rounded-xl bg-primary text-primary-foreground shadow-sm">
                        <BookOpen className="size-5" />
                    </span>
                    Actividad de aprendizaje
                </div>
                <div className="flex items-center gap-2 rounded-full border bg-card px-3 py-2 text-sm font-medium shadow-sm">
                    <UserRound className="size-4 text-primary" />
                    <span className="max-w-36 truncate sm:max-w-60">
                        {activity.student_name}
                    </span>
                </div>
            </div>

            <div className="grid gap-4 rounded-3xl border bg-card p-5 shadow-sm sm:p-7">
                <div className="grid gap-2">
                    <p className="text-sm font-semibold text-primary">
                        Prepárate para practicar
                    </p>
                    <h1 className="text-2xl leading-tight font-bold tracking-tight sm:text-3xl">
                        {activity.title}
                    </h1>
                    <p className="leading-relaxed text-muted-foreground">
                        {activity.instructions}
                    </p>
                    <SpeechButton
                        text={narration}
                        label="Escuchar actividad"
                        className="mt-2"
                    />
                </div>
                <div className="flex items-start gap-3 rounded-2xl bg-muted/70 p-4">
                    <Target className="mt-0.5 size-5 shrink-0 text-primary" />
                    <div className="grid gap-1">
                        <p className="text-sm font-semibold">Objetivo</p>
                        <p className="text-sm leading-relaxed text-muted-foreground">
                            {activity.learning_objective}
                        </p>
                    </div>
                </div>
            </div>
        </header>
    );
}
