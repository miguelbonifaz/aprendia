import { Square, Volume2 } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { useSpeech } from '@/hooks/use-speech';
import { cn } from '@/lib/utils';

type Props = {
    text: string;
    label: string;
    className?: string;
};

export function SpeechButton({ text, label, className }: Props) {
    const { isSupported, isSpeaking, speak, stop } = useSpeech();

    return (
        <Button
            type="button"
            variant="outline"
            disabled={!isSupported}
            aria-label={isSpeaking ? 'Detener audio' : label}
            title={
                isSupported
                    ? undefined
                    : 'El navegador no permite reproducir este audio.'
            }
            onClick={() => (isSpeaking ? stop() : speak(text))}
            className={cn(
                'w-fit rounded-full border-primary/30 bg-primary/5 text-primary hover:bg-primary/10',
                className,
            )}
        >
            {isSpeaking ? <Square /> : <Volume2 />}
            {isSpeaking ? 'Detener audio' : label}
        </Button>
    );
}
