import { Form } from '@inertiajs/react';
import { Check, UserRound } from 'lucide-react';
import StudentSelectionController from '@/actions/App/Http/Controllers/StudentSelectionController';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import type { Student } from '@/types';

export function DashboardStudentCard({
    student,
    selected,
}: {
    student: Student;
    selected: boolean;
}) {
    return (
        <Form {...StudentSelectionController.form(student.id)}>
            {({ processing }) => (
                <Button
                    type="submit"
                    variant="outline"
                    disabled={processing}
                    aria-label={`Trabajar con ${student.name}`}
                    className={`h-auto w-full justify-start rounded-xl p-0 text-left whitespace-normal ${
                        selected
                            ? 'border-primary ring-2 ring-primary/15'
                            : 'hover:border-primary/50'
                    }`}
                >
                    <span className="flex w-full items-center gap-4 p-5">
                        <span className="flex size-12 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <UserRound className="size-5" />
                        </span>
                        <span className="min-w-0 flex-1">
                            <span className="block truncate text-base font-semibold">
                                {student.name}
                            </span>
                            <span className="block text-sm font-normal text-muted-foreground">
                                {student.age}{' '}
                                {student.age === 1 ? 'año' : 'años'}
                            </span>
                        </span>
                        {selected && (
                            <Badge className="shrink-0">
                                <Check /> Seleccionado
                            </Badge>
                        )}
                    </span>
                </Button>
            )}
        </Form>
    );
}
