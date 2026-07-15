import { Head } from '@inertiajs/react';
import { CheckCircle2, Sparkles } from 'lucide-react';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';

type Props = {
    activity: {
        title: string;
        instructions: string;
        learning_objective: string;
    };
};

export default function ActivityShow({ activity }: Props) {
    return (
        <>
            <Head title={activity.title} />
            <main className="flex min-h-svh items-center justify-center bg-muted/30 p-4 sm:p-8">
                <Card className="w-full max-w-2xl border-primary/20 shadow-lg">
                    <CardHeader className="gap-5 text-center">
                        <div className="mx-auto flex size-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <Sparkles className="size-7" />
                        </div>
                        <div className="grid gap-2">
                            <p className="text-sm font-medium text-primary">
                                Actividad creada
                            </p>
                            <CardTitle className="text-2xl sm:text-3xl">
                                {activity.title}
                            </CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent className="grid gap-5">
                        <section className="grid gap-2 rounded-xl bg-muted p-4">
                            <h2 className="font-semibold">Objetivo</h2>
                            <p className="text-sm leading-relaxed text-muted-foreground">
                                {activity.learning_objective}
                            </p>
                        </section>
                        <section className="grid gap-2">
                            <h2 className="font-semibold">Instrucciones</h2>
                            <p className="text-sm leading-relaxed text-muted-foreground">
                                {activity.instructions}
                            </p>
                        </section>
                        <div className="flex items-center gap-2 rounded-xl border border-primary/20 bg-primary/5 p-4 text-sm text-primary">
                            <CheckCircle2 className="size-5 shrink-0" />
                            La actividad está lista para compartir.
                        </div>
                    </CardContent>
                </Card>
            </main>
        </>
    );
}
