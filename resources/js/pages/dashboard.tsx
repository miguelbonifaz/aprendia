import { Head, Link } from '@inertiajs/react';
import { BarChart3, BookOpenCheck, Sparkles, UsersRound } from 'lucide-react';
import { DashboardStudentCard } from '@/components/dashboard/dashboard-student-card';
import { StudentFormDialog } from '@/components/students/student-form-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { dashboard } from '@/routes';
import { index as chat } from '@/routes/chat';
import type { Student } from '@/types';

type DashboardProps = {
    students: Student[];
    selectedStudent: Student | null;
};

export default function Dashboard({
    students,
    selectedStudent,
}: DashboardProps) {
    return (
        <>
            <Head title="Panel principal" />

            <div className="flex flex-1 flex-col gap-8 p-4 md:p-8">
                <header className="flex flex-col justify-between gap-5 lg:flex-row lg:items-start">
                    <div className="grid max-w-xl gap-2">
                        <p className="text-sm font-medium text-primary">
                            Panel del representante
                        </p>
                        <h1 className="text-3xl font-semibold tracking-tight">
                            ¿Con quién trabajaremos hoy?
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Elige un alumno para comenzar una nueva actividad o
                            registra uno nuevo.
                        </p>
                    </div>
                    <div className="flex flex-wrap items-start gap-3">
                        <StudentFormDialog />
                        {selectedStudent ? (
                            <Button asChild size="lg">
                                <Link href={chat()}>
                                    <Sparkles /> Crear actividad
                                </Link>
                            </Button>
                        ) : (
                            <div className="grid gap-1">
                                <Button size="lg" disabled>
                                    <Sparkles /> Crear actividad
                                </Button>
                                <p className="text-xs text-muted-foreground">
                                    Primero selecciona un alumno.
                                </p>
                            </div>
                        )}
                    </div>
                </header>

                {selectedStudent && (
                    <section className="flex items-center gap-3 rounded-xl border border-primary/25 bg-primary/5 p-4">
                        <BookOpenCheck className="size-5 shrink-0 text-primary" />
                        <div>
                            <p className="text-xs font-medium tracking-wide text-muted-foreground uppercase">
                                Alumno seleccionado
                            </p>
                            <p className="font-semibold">
                                {selectedStudent.name} · {selectedStudent.age}{' '}
                                {selectedStudent.age === 1 ? 'año' : 'años'}
                            </p>
                        </div>
                    </section>
                )}

                <section className="grid gap-4">
                    <div>
                        <h2 className="text-lg font-semibold">Tus alumnos</h2>
                        <p className="text-sm text-muted-foreground">
                            Pulsa una tarjeta para ir directamente al chat.
                        </p>
                    </div>

                    {students.length === 0 ? (
                        <div className="flex min-h-64 flex-col items-center justify-center gap-4 rounded-xl border border-dashed p-8 text-center">
                            <div className="flex size-14 items-center justify-center rounded-full bg-muted">
                                <UsersRound className="size-6 text-muted-foreground" />
                            </div>
                            <div className="grid max-w-sm gap-1">
                                <h3 className="font-semibold">
                                    Aún no tienes alumnos registrados
                                </h3>
                                <p className="text-sm text-muted-foreground">
                                    Agrega el primer alumno para comenzar una
                                    actividad personalizada.
                                </p>
                            </div>
                            <StudentFormDialog />
                        </div>
                    ) : (
                        <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                            {students.map((student) => (
                                <DashboardStudentCard
                                    key={student.id}
                                    student={student}
                                    selected={
                                        selectedStudent?.id === student.id
                                    }
                                />
                            ))}
                        </div>
                    )}
                </section>

                <Card className="border-dashed shadow-none">
                    <CardHeader>
                        <div className="flex items-center gap-3">
                            <BarChart3 className="size-5 text-muted-foreground" />
                            <CardTitle>Actividades y resultados</CardTitle>
                        </div>
                    </CardHeader>
                    <CardContent>
                        <p className="text-sm text-muted-foreground">
                            Próximamente podrás consultar aquí las actividades
                            realizadas y el progreso de cada alumno.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </>
    );
}

Dashboard.layout = {
    breadcrumbs: [{ title: 'Panel principal', href: dashboard() }],
};
