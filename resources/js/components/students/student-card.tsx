import { Form } from '@inertiajs/react';
import { Check, UserRound } from 'lucide-react';
import StudentSelectionController from '@/actions/App/Http/Controllers/StudentSelectionController';
import { StudentFormDialog } from '@/components/students/student-form-dialog';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import type { Student } from '@/types';

function formatBirthDate(date: string): string {
    return new Intl.DateTimeFormat('es-EC', { dateStyle: 'long' }).format(
        new Date(`${date}T00:00:00`),
    );
}

export function StudentCard({
    student,
    selected,
}: {
    student: Student;
    selected: boolean;
}) {
    return (
        <Card
            className={
                selected ? 'border-primary ring-2 ring-primary/15' : undefined
            }
        >
            <CardHeader className="flex-row items-start justify-between gap-4">
                <div className="flex min-w-0 items-center gap-3">
                    <div className="flex size-11 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                        <UserRound className="size-5" />
                    </div>
                    <div className="min-w-0">
                        <CardTitle className="truncate text-lg">
                            {student.name}
                        </CardTitle>
                        <p className="text-sm text-muted-foreground">
                            {student.age} {student.age === 1 ? 'año' : 'años'}
                        </p>
                    </div>
                </div>
                {selected && (
                    <Badge>
                        <Check /> Seleccionado
                    </Badge>
                )}
            </CardHeader>

            <CardContent>
                <p className="text-sm text-muted-foreground">
                    Nació el {formatBirthDate(student.birth_date)}
                </p>
            </CardContent>

            <CardFooter className="flex-wrap justify-between gap-3">
                <StudentFormDialog student={student} />
                <Form {...StudentSelectionController.form(student.id)}>
                    {({ processing }) => (
                        <Button disabled={processing}>
                            {processing
                                ? 'Continuando…'
                                : selected
                                  ? 'Ir al chat'
                                  : 'Seleccionar'}
                        </Button>
                    )}
                </Form>
            </CardFooter>
        </Card>
    );
}
