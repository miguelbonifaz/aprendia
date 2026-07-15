import { LoaderCircle, Volume2 } from 'lucide-react';
import { useEffect, useRef, useState } from 'react';
import { Button } from '@/components/ui/button';

type Props = {
    audioUrl: string;
};

type PlaybackState = 'idle' | 'loading' | 'playing' | 'error';

export function ActivityPronunciationButton({ audioUrl }: Props) {
    const audio = useRef<HTMLAudioElement | null>(null);
    const [state, setState] = useState<PlaybackState>('idle');

    useEffect(() => {
        return () => {
            audio.current?.pause();
            audio.current = null;
        };
    }, [audioUrl]);

    const play = () => {
        audio.current?.pause();

        const nextAudio = new Audio(audioUrl);
        nextAudio.preload = 'none';
        nextAudio.onplaying = () => setState('playing');
        nextAudio.onended = () => setState('idle');
        nextAudio.onerror = () => setState('error');
        audio.current = nextAudio;
        setState('loading');

        void nextAudio.play().catch(() => setState('error'));
    };

    return (
        <div className="grid justify-items-start gap-2">
            <div className="flex flex-wrap items-center gap-3">
                <Button
                    type="button"
                    variant="outline"
                    size="sm"
                    disabled={state === 'loading'}
                    onClick={play}
                    aria-label="Escuchar el nombre del dibujo"
                >
                    {state === 'loading' ? (
                        <LoaderCircle className="animate-spin" />
                    ) : (
                        <Volume2 />
                    )}
                    {state === 'loading'
                        ? 'Preparando audio…'
                        : state === 'playing'
                          ? 'Repetir palabra'
                          : 'Escuchar palabra'}
                </Button>
                <span className="text-xs text-muted-foreground">
                    Voz generada por IA
                </span>
            </div>
            {state === 'error' && (
                <p
                    className="text-sm font-medium text-destructive"
                    role="alert"
                >
                    No pudimos reproducir el audio. Inténtalo nuevamente.
                </p>
            )}
        </div>
    );
}
