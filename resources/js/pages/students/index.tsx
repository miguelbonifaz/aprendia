import { Head, Link } from '@inertiajs/react';
import { ArrowRight, UsersRound } from 'lucide-react';
import { StudentCard } from '@/components/students/student-card';
import { StudentFormDialog } from '@/components/students/student-form-dialog';
import { Button } from '@/components/ui/button';
import { index as chat } from '@/routes/chat';
import { index as students } from '@/routes/students';
import type { Student } from '@/types';

export default function StudentsIndex({
    students: studentList,
    selectedStudent,
}: {
    students: Student[];
    selectedStudent: Student | null;
}) {
    return (
        <>
            <Head title="Alumnos" />

            <div className="flex flex-1 flex-col gap-6 p-4 md:p-8">
                <header className="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                    <div className="grid gap-1">
                        <h1 className="text-2xl font-semibold tracking-tight">
                            Alumnos
                        </h1>
                        <p className="text-sm text-muted-foreground">
                            Registra a tus hijos y elige con quién deseas
                            trabajar.
                        </p>
                    </div>
                    <StudentFormDialog />
                </header>

                {selectedStudent && (
                    <section className="flex flex-col justify-between gap-4 rounded-xl border bg-muted/40 p-4 sm:flex-row sm:items-center">
                        <div>
                            <p className="text-xs font-medium tracking-wide text-muted-foreground uppercase">
                                Alumno seleccionado
                            </p>
                            <p className="font-semibold">
                                {selectedStudent.name}
                            </p>
                        </div>
                        <Button asChild>
                            <Link href={chat()}>
                                Continuar al chat <ArrowRight />
                            </Link>
                        </Button>
                    </section>
                )}

                {studentList.length === 0 ? (
                    <section className="flex min-h-80 flex-col items-center justify-center gap-4 rounded-xl border border-dashed p-8 text-center">
                        <div className="flex size-14 items-center justify-center rounded-full bg-muted">
                            <UsersRound className="size-6 text-muted-foreground" />
                        </div>
                        <div className="grid max-w-sm gap-1">
                            <h2 className="font-semibold">
                                Aún no tienes alumnos registrados
                            </h2>
                            <p className="text-sm text-muted-foreground">
                                Agrega el primer alumno para comenzar a preparar
                                actividades personalizadas.
                            </p>
                        </div>
                        <StudentFormDialog />
                    </section>
                ) : (
                    <section className="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        {studentList.map((student) => (
                            <StudentCard
                                key={student.id}
                                student={student}
                                selected={selectedStudent?.id === student.id}
                            />
                        ))}
                    </section>
                )}
            </div>
        </>
    );
}

StudentsIndex.layout = {
    breadcrumbs: [{ title: 'Alumnos', href: students() }],
};
