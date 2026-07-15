const steps = [
    {
        number: '01',
        title: 'Registra al alumno',
        description:
            'Cuéntanos su edad para empezar con una experiencia adecuada a su etapa.',
    },
    {
        number: '02',
        title: 'Dinos qué practicará',
        description:
            'Escribe la habilidad, tema o dificultad que quieres trabajar hoy.',
    },
    {
        number: '03',
        title: 'Recibe su actividad',
        description:
            'Aprendia prepara una experiencia visual lista para aprender jugando.',
    },
];

export function LandingSteps() {
    return (
        <section
            id="como-funciona"
            className="bg-[#fffaf0] px-5 py-24 sm:px-8 sm:py-32 lg:px-12 dark:bg-[#10272b]"
        >
            <div className="mx-auto max-w-7xl">
                <div className="max-w-2xl">
                    <p className="text-sm font-semibold tracking-[0.18em] text-[#d35f48] uppercase">
                        Así de sencillo
                    </p>
                    <h2 className="mt-4 text-4xl leading-tight font-semibold tracking-[-0.045em] text-balance sm:text-5xl">
                        De una necesidad concreta a una actividad para
                        disfrutar.
                    </h2>
                </div>

                <ol className="mt-16 grid border-t border-[#17383c]/15 md:grid-cols-3 dark:border-white/15">
                    {steps.map((step) => (
                        <li
                            key={step.number}
                            className="grid gap-5 border-b border-[#17383c]/15 py-9 md:border-r md:border-b-0 md:px-8 md:first:pl-0 md:last:border-r-0 dark:border-white/15"
                        >
                            <span className="text-sm font-semibold text-[#d35f48]">
                                {step.number}
                            </span>
                            <h3 className="text-2xl font-semibold tracking-[-0.03em]">
                                {step.title}
                            </h3>
                            <p className="max-w-sm leading-relaxed text-[#5c7373] dark:text-[#b9cbc5]">
                                {step.description}
                            </p>
                        </li>
                    ))}
                </ol>
            </div>
        </section>
    );
}
