import { Form } from '@inertiajs/react';
import { Pencil, Plus } from 'lucide-react';
import { useState } from 'react';
import StudentController from '@/actions/App/Http/Controllers/StudentController';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { Student } from '@/types';

function todayInputValue(): string {
    const today = new Date();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    return `${today.getFullYear()}-${month}-${day}`;
}

function calculateAge(birthDate: string): number | null {
    const [year, month, day] = birthDate.split('-').map(Number);

    if (!year || !month || !day) {
        return null;
    }

    const today = new Date();
    let age = today.getFullYear() - year;

    if (
        today.getMonth() + 1 < month ||
        (today.getMonth() + 1 === month && today.getDate() < day)
    ) {
        age -= 1;
    }

    return age >= 0 ? age : null;
}

export function StudentFormDialog({ student }: { student?: Student }) {
    const [open, setOpen] = useState(false);
    const [birthDate, setBirthDate] = useState(student?.birth_date ?? '');
    const age = calculateAge(birthDate);
    const form = student
        ? StudentController.update.form(student.id)
        : StudentController.store.form();

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button
                    variant={student ? 'outline' : 'default'}
                    size={student ? 'sm' : 'default'}
                >
                    {student ? <Pencil /> : <Plus />}
                    {student ? 'Editar' : 'Agregar alumno'}
                </Button>
            </DialogTrigger>

            <DialogContent>
                <DialogHeader>
                    <DialogTitle>
                        {student ? 'Editar alumno' : 'Registrar alumno'}
                    </DialogTitle>
                    <DialogDescription>
                        Ingresa el nombre y la fecha de nacimiento. La edad se
                        calculará automáticamente.
                    </DialogDescription>
                </DialogHeader>

                <Form
                    {...form}
                    options={{ preserveScroll: true }}
                    resetOnSuccess={!student}
                    onSuccess={() => {
                        setOpen(false);

                        if (!student) {
                            setBirthDate('');
                        }
                    }}
                    className="grid gap-5"
                >
                    {({ errors, processing }) => (
                        <>
                            <div className="grid gap-2">
                                <Label
                                    htmlFor={`student-name-${student?.id ?? 'new'}`}
                                >
                                    Nombre
                                </Label>
                                <Input
                                    id={`student-name-${student?.id ?? 'new'}`}
                                    name="name"
                                    defaultValue={student?.name}
                                    placeholder="Nombre completo"
                                    autoComplete="off"
                                    required
                                />
                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label
                                    htmlFor={`birth-date-${student?.id ?? 'new'}`}
                                >
                                    Fecha de nacimiento
                                </Label>
                                <Input
                                    id={`birth-date-${student?.id ?? 'new'}`}
                                    name="birth_date"
                                    type="date"
                                    value={birthDate}
                                    max={todayInputValue()}
                                    onChange={(event) =>
                                        setBirthDate(event.target.value)
                                    }
                                    required
                                />
                                <InputError message={errors.birth_date} />
                                {age !== null && (
                                    <p className="text-sm font-medium text-foreground">
                                        Edad: {age} {age === 1 ? 'año' : 'años'}
                                    </p>
                                )}
                            </div>

                            <DialogFooter>
                                <DialogClose asChild>
                                    <Button type="button" variant="outline">
                                        Cancelar
                                    </Button>
                                </DialogClose>
                                <Button disabled={processing}>
                                    {processing
                                        ? 'Guardando…'
                                        : student
                                          ? 'Guardar cambios'
                                          : 'Registrar alumno'}
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
