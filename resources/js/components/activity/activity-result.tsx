import { RotateCcw, Star, Trophy } from 'lucide-react';
import { Button } from '@/components/ui/button';
import type { PlayableActivityResult } from '@/types';

type Props = {
    result: PlayableActivityResult;
    studentName: string;
    onRestart: () => void;
};

export function ActivityResult({ result, studentName, onRestart }: Props) {
    return (
        <section
            className="grid justify-items-center gap-6 rounded-3xl border bg-card p-6 text-center shadow-sm sm:p-10"
            aria-live="polite"
        >
            <div className="flex size-16 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-600">
                <Trophy className="size-9" />
            </div>
            <div className="grid gap-2">
                <p className="font-semibold text-primary">
                    ¡Actividad terminada!
                </p>
                <h2 className="text-2xl font-bold tracking-tight sm:text-3xl">
                    Muy buen trabajo, {studentName}
                </h2>
            </div>
            <div className="flex size-36 flex-col items-center justify-center rounded-full border-8 border-primary/15 bg-primary/5">
                <span className="text-4xl font-black text-primary">
                    {result.score}%
                </span>
                <span className="text-xs font-semibold text-muted-foreground">
                    resultado
                </span>
            </div>
            <div className="flex items-center gap-2 rounded-2xl bg-muted px-5 py-3 font-medium">
                <Star className="size-5 fill-amber-400 text-amber-500" />
                {result.summary}
            </div>
            <Button type="button" size="lg" onClick={onRestart}>
                <RotateCcw />
                Repetir actividad
            </Button>
        </section>
    );
}
