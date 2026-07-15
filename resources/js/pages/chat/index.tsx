import { Head, Link } from '@inertiajs/react';
import { ArrowLeft, MessageCircle, UserRound } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { index as chat } from '@/routes/chat';
import { index as students } from '@/routes/students';
import type { Student } from '@/types';

export default function ChatIndex({ student }: { student: Student }) {
    return (
        <>
            <Head title={`Chat con ${student.name}`} />

            <div className="flex flex-1 items-center justify-center p-4 md:p-8">
                <Card className="w-full max-w-2xl">
                    <CardHeader className="items-center text-center">
                        <div className="flex size-14 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <UserRound className="size-6" />
                        </div>
                        <CardTitle className="text-xl">
                            Trabajando con {student.name}
                        </CardTitle>
                        <p className="text-sm text-muted-foreground">
                            {student.age} {student.age === 1 ? 'año' : 'años'}
                        </p>
                    </CardHeader>

                    <CardContent className="grid gap-6">
                        <div className="flex min-h-48 flex-col items-center justify-center gap-3 rounded-xl border border-dashed bg-muted/30 p-6 text-center">
                            <MessageCircle className="size-8 text-muted-foreground" />
                            <div className="grid max-w-md gap-1">
                                <h2 className="font-semibold">
                                    El espacio de conversación está listo
                                </h2>
                                <p className="text-sm text-muted-foreground">
                                    Aquí podrás explicar qué necesita aprender o
                                    practicar {student.name}. La conversación se
                                    incorporará en la siguiente etapa.
                                </p>
                            </div>
                        </div>

                        <Button
                            asChild
                            variant="outline"
                            className="justify-self-start"
                        >
                            <Link href={students()}>
                                <ArrowLeft /> Cambiar alumno
                            </Link>
                        </Button>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

ChatIndex.layout = {
    breadcrumbs: [{ title: 'Chat', href: chat() }],
};
