type Props = {
    position: number;
    total: number;
};

export function ActivityProgress({ position, total }: Props) {
    const percentage = Math.round((position / total) * 100);

    return (
        <div className="grid gap-3">
            <div className="flex items-center justify-between text-sm font-medium">
                <span className="text-primary">
                    Pregunta {position} de {total}
                </span>
                <span className="text-muted-foreground">{percentage}%</span>
            </div>
            <div
                className="h-2 overflow-hidden rounded-full bg-muted"
                role="progressbar"
                aria-label="Progreso de la actividad"
                aria-valuemin={0}
                aria-valuemax={total}
                aria-valuenow={position}
            >
                <div
                    className="h-full rounded-full bg-primary transition-[width] duration-300"
                    style={{ width: `${percentage}%` }}
                />
            </div>
        </div>
    );
}
