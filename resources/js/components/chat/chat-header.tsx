import { Link } from '@inertiajs/react';
import { ArrowLeft, UserRound } from 'lucide-react';
import { NewConversationDialog } from '@/components/chat/new-conversation-dialog';
import { Button } from '@/components/ui/button';
import { dashboard } from '@/routes';
import type { Student } from '@/types';

type Props = {
    student: Student;
    hasMessages: boolean;
    dialogOpen: boolean;
    onDialogOpenChange: (open: boolean) => void;
    onNewConversation: () => void;
    resetting: boolean;
    sending: boolean;
    resetError: string | null;
};

export function ChatHeader({
    student,
    hasMessages,
    dialogOpen,
    onDialogOpenChange,
    onNewConversation,
    resetting,
    sending,
    resetError,
}: Props) {
    return (
        <header className="flex shrink-0 flex-wrap items-center justify-between gap-3 border-b bg-background/95 px-4 py-3 backdrop-blur md:px-6">
            <div className="flex min-w-0 items-center gap-3">
                <div className="flex size-10 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                    <UserRound className="size-5" />
                </div>
                <div className="min-w-0">
                    <p className="truncate font-semibold">{student.name}</p>
                    <p className="text-xs text-muted-foreground">
                        {student.age} {student.age === 1 ? 'año' : 'años'} ·
                        Alumno seleccionado
                    </p>
                </div>
            </div>

            <div className="flex items-center gap-2">
                <Button asChild variant="ghost" size="sm">
                    <Link href={dashboard()}>
                        <ArrowLeft />
                        <span className="hidden sm:inline">Cambiar alumno</span>
                        <span className="sm:hidden">Cambiar</span>
                    </Link>
                </Button>
                <NewConversationDialog
                    open={dialogOpen}
                    onOpenChange={onDialogOpenChange}
                    onConfirm={onNewConversation}
                    processing={resetting}
                    disabled={!hasMessages || sending}
                    error={resetError}
                />
            </div>
        </header>
    );
}
