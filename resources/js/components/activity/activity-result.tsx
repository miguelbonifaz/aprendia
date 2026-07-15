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
            className="grid min-h-0 gap-8 rounded-3xl border bg-card p-6 shadow-sm sm:p-8 lg:h-full lg:grid-cols-[minmax(0,1fr)_minmax(18rem,0.55fr)] lg:items-center lg:px-12"
            aria-live="polite"
        >
            <div className="grid justify-items-start gap-5 text-left">
                <div className="flex size-14 items-center justify-center rounded-2xl bg-amber-500/15 text-amber-600">
                    <Trophy className="size-8" />
                </div>
                <div className="grid gap-2">
                    <p className="font-semibold text-primary">
                        Actividad terminada
                    </p>
                    <h2 className="text-3xl leading-tight font-bold tracking-tight text-balance sm:text-4xl">
                        Muy buen trabajo, {studentName}
                    </h2>
                </div>
                <div className="flex items-start gap-3 rounded-2xl bg-muted px-5 py-4 font-medium">
                    <Star className="mt-0.5 size-5 shrink-0 fill-amber-400 text-amber-500" />
                    <span>{result.summary}</span>
                </div>
                <Button
                    type="button"
                    size="lg"
                    className="transition-transform active:scale-[0.98]"
                    onClick={onRestart}
                >
                    <RotateCcw />
                    Repetir actividad
                </Button>
            </div>

            <div className="grid place-items-center rounded-[2rem] bg-primary/5 p-8">
                <div className="flex size-44 flex-col items-center justify-center rounded-full border-10 border-primary/15 bg-background shadow-sm">
                    <span className="text-5xl font-black tracking-tight text-primary tabular-nums">
                        {result.score}%
                    </span>
                    <span className="text-sm font-semibold text-muted-foreground">
                        resultado
                    </span>
                </div>
            </div>
        </section>
    );
}
