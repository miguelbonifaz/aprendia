type Props = {
    position: number;
    total: number;
};

export function ActivityProgress({ position, total }: Props) {
    return (
        <div className="grid gap-2.5">
            <div className="flex items-center justify-between gap-3 text-sm font-semibold">
                <span className="text-primary">
                    Paso {position} de {total}
                </span>
                <span className="text-muted-foreground">Sigue avanzando</span>
            </div>
            <div
                className="flex gap-2"
                role="progressbar"
                aria-label="Progreso de la actividad"
                aria-valuemin={0}
                aria-valuemax={total}
                aria-valuenow={position}
            >
                {Array.from({ length: total }, (_, index) => (
                    <span
                        key={index}
                        className={`h-2 flex-1 rounded-full transition-colors duration-300 ${
                            index < position ? 'bg-primary' : 'bg-muted'
                        }`}
                        aria-hidden="true"
                    />
                ))}
            </div>
        </div>
    );
}
