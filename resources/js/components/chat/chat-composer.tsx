import { CircleAlert, Send } from 'lucide-react';
import { useEffect, useRef } from 'react';
import type { FormEvent, KeyboardEvent } from 'react';
import { Button } from '@/components/ui/button';
import { cn } from '@/lib/utils';

type Props = {
    message: string;
    onMessageChange: (message: string) => void;
    onSubmit: () => void;
    processing: boolean;
    error: string | null;
};

export function ChatComposer({
    message,
    onMessageChange,
    onSubmit,
    processing,
    error,
}: Props) {
    const textarea = useRef<HTMLTextAreaElement>(null);

    useEffect(() => {
        if (!textarea.current) {
            return;
        }

        textarea.current.style.height = 'auto';
        textarea.current.style.height = `${Math.min(textarea.current.scrollHeight, 144)}px`;
    }, [message]);

    function submit(event: FormEvent<HTMLFormElement>) {
        event.preventDefault();
        onSubmit();
    }

    function handleKeyDown(event: KeyboardEvent<HTMLTextAreaElement>) {
        if (event.key === 'Enter' && !event.shiftKey) {
            event.preventDefault();
            event.currentTarget.form?.requestSubmit();
        }
    }

    return (
        <div className="shrink-0 border-t bg-background px-4 py-3 md:px-6 md:py-4">
            <form
                className="mx-auto grid w-full max-w-3xl gap-2"
                onSubmit={submit}
            >
                {error && (
                    <div
                        className="flex items-start gap-2 text-sm text-destructive"
                        role="alert"
                    >
                        <CircleAlert className="mt-0.5 size-4 shrink-0" />
                        <span>{error}</span>
                    </div>
                )}
                <div
                    className={cn(
                        'flex items-end gap-2 rounded-2xl border bg-background p-2 shadow-sm transition-shadow focus-within:ring-2 focus-within:ring-ring/40',
                        error && 'border-destructive/60',
                    )}
                >
                    <textarea
                        ref={textarea}
                        value={message}
                        onChange={(event) =>
                            onMessageChange(event.target.value)
                        }
                        onKeyDown={handleKeyDown}
                        maxLength={2000}
                        rows={1}
                        disabled={processing}
                        aria-label="Mensaje para el agente"
                        placeholder="Describe qué necesita aprender o practicar…"
                        className="max-h-36 min-h-10 flex-1 resize-none bg-transparent px-2 py-2 text-sm leading-5 outline-none placeholder:text-muted-foreground disabled:opacity-50"
                    />
                    <Button
                        type="submit"
                        size="icon"
                        disabled={processing || !message.trim()}
                        aria-label="Enviar mensaje"
                        className="rounded-xl"
                    >
                        <Send />
                    </Button>
                </div>
                <div className="flex justify-between gap-3 px-1 text-xs text-muted-foreground">
                    <span>
                        Enter para enviar · Shift+Enter para una línea nueva
                    </span>
                    <span>{message.length}/2000</span>
                </div>
            </form>
        </div>
    );
}
