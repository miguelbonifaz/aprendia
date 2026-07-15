import {
    useCallback,
    useEffect,
    useRef,
    useState,
    useSyncExternalStore,
} from 'react';

const subscribe = () => () => undefined;
const serverSnapshot = () => false;
const browserSnapshot = () =>
    'speechSynthesis' in window && 'SpeechSynthesisUtterance' in window;

export function useSpeech() {
    const utterance = useRef<SpeechSynthesisUtterance | null>(null);
    const [isSpeaking, setIsSpeaking] = useState(false);
    const isSupported = useSyncExternalStore(
        subscribe,
        browserSnapshot,
        serverSnapshot,
    );

    useEffect(() => {
        return () => {
            if (utterance.current) {
                window.speechSynthesis.cancel();
            }
        };
    }, []);

    const stop = useCallback(() => {
        window.speechSynthesis?.cancel();
        utterance.current = null;
        setIsSpeaking(false);
    }, []);

    const speak = useCallback(
        (text: string) => {
            if (!isSupported) {
                return;
            }

            window.speechSynthesis.cancel();

            const nextUtterance = new SpeechSynthesisUtterance(text);
            const spanishVoice = window.speechSynthesis
                .getVoices()
                .find((voice) => voice.lang.toLowerCase().startsWith('es'));

            nextUtterance.lang = spanishVoice?.lang ?? 'es-EC';
            nextUtterance.voice = spanishVoice ?? null;
            nextUtterance.rate = 0.82;
            nextUtterance.pitch = 1;
            nextUtterance.onend = stop;
            nextUtterance.onerror = stop;
            utterance.current = nextUtterance;
            setIsSpeaking(true);
            window.speechSynthesis.speak(nextUtterance);
        },
        [isSupported, stop],
    );

    return { isSupported, isSpeaking, speak, stop };
}
