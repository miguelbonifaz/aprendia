import { MessageSquarePlus } from 'lucide-react';
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
import { Spinner } from '@/components/ui/spinner';

type Props = {
    open: boolean;
    onOpenChange: (open: boolean) => void;
    onConfirm: () => void;
    processing: boolean;
    disabled: boolean;
    error: string | null;
};

export function NewConversationDialog({
    open,
    onOpenChange,
    onConfirm,
    processing,
    disabled,
    error,
}: Props) {
    return (
        <Dialog open={open} onOpenChange={onOpenChange}>
            <DialogTrigger asChild>
                <Button variant="outline" size="sm" disabled={disabled}>
                    <MessageSquarePlus />
                    <span className="hidden sm:inline">Nueva conversación</span>
                    <span className="sm:hidden">Nueva</span>
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>¿Comenzar una conversación nueva?</DialogTitle>
                    <DialogDescription>
                        Se eliminarán los mensajes actuales de este alumno. Esta
                        acción no afecta las conversaciones de otros alumnos.
                    </DialogDescription>
                </DialogHeader>
                {error && (
                    <p className="text-sm text-destructive" role="alert">
                        {error}
                    </p>
                )}
                <DialogFooter>
                    <DialogClose asChild>
                        <Button variant="outline" disabled={processing}>
                            Cancelar
                        </Button>
                    </DialogClose>
                    <Button onClick={onConfirm} disabled={processing}>
                        {processing && <Spinner />}
                        Comenzar de nuevo
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
