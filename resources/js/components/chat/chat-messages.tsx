import { Bot, ExternalLink, MessageCircle } from 'lucide-react';
import { useEffect, useRef } from 'react';
import { Button } from '@/components/ui/button';
import { Spinner } from '@/components/ui/spinner';
import type { ChatMessage } from '@/types';

type Props = {
    messages: ChatMessage[];
    studentName: string;
    responding: boolean;
};

export function ChatMessages({ messages, studentName, responding }: Props) {
    const bottom = useRef<HTMLDivElement>(null);

    useEffect(() => {
        bottom.current?.scrollIntoView({
            behavior: messages.length > 0 ? 'smooth' : 'auto',
        });
    }, [messages, responding]);

    return (
        <section
            className="min-h-0 flex-1 overflow-y-auto px-4 py-6 md:px-6"
            aria-label="Conversación"
        >
            <div className="mx-auto flex min-h-full w-full max-w-3xl flex-col justify-end gap-6">
                {messages.length === 0 && !responding ? (
                    <div className="my-auto flex flex-col items-center gap-4 py-12 text-center">
                        <div className="flex size-14 items-center justify-center rounded-2xl bg-primary/10 text-primary">
                            <MessageCircle className="size-7" />
                        </div>
                        <div className="grid max-w-lg gap-2">
                            <h1 className="text-xl font-semibold tracking-tight">
                                ¿Qué necesita practicar {studentName}?
                            </h1>
                            <p className="text-sm leading-relaxed text-muted-foreground">
                                Describe el tema, la habilidad o la dificultad
                                que quieres trabajar. El agente te ayudará a
                                elegir la actividad adecuada.
                            </p>
                        </div>
                    </div>
                ) : (
                    messages.map((message, index) => (
                        <article
                            key={`${message.role}-${index}`}
                            className={
                                message.role === 'user'
                                    ? 'flex justify-end'
                                    : 'flex items-start gap-3'
                            }
                            aria-label={
                                message.role === 'user'
                                    ? 'Mensaje del representante'
                                    : 'Respuesta del agente'
                            }
                        >
                            {message.role === 'assistant' && (
                                <div className="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary text-primary-foreground">
                                    <Bot className="size-4" />
                                </div>
                            )}
                            <div
                                className={
                                    message.role === 'user'
                                        ? 'max-w-[85%] rounded-2xl rounded-br-md bg-primary px-4 py-3 text-sm leading-relaxed text-primary-foreground md:max-w-[75%]'
                                        : 'grid max-w-[calc(100%-2.75rem)] gap-3 pt-1 text-sm leading-relaxed text-foreground'
                                }
                            >
                                <p className="whitespace-pre-wrap">
                                    {message.content}
                                </p>
                                {message.activity && (
                                    <Button asChild className="w-fit" size="sm">
                                        <a
                                            href={message.activity.url}
                                            target="_blank"
                                            rel="noreferrer"
                                        >
                                            Abrir actividad
                                            <ExternalLink />
                                        </a>
                                    </Button>
                                )}
                            </div>
                        </article>
                    ))
                )}

                {responding && (
                    <div
                        className="flex items-center gap-3 text-sm text-muted-foreground"
                        role="status"
                        aria-live="polite"
                    >
                        <div className="flex size-8 items-center justify-center rounded-full bg-primary text-primary-foreground">
                            <Bot className="size-4" />
                        </div>
                        <Spinner />
                        <span>El agente está respondiendo…</span>
                    </div>
                )}
                <div ref={bottom} />
            </div>
        </section>
    );
}
