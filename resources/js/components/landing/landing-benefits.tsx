import { Eye, HeartHandshake, Sparkles } from 'lucide-react';

const benefits = [
    {
        icon: Sparkles,
        title: 'Adaptada a su edad',
        description:
            'Cada actividad parte del alumno y de lo que necesita reforzar.',
    },
    {
        icon: Eye,
        title: 'Visual y fácil de seguir',
        description:
            'Preguntas claras, imágenes atractivas y una experiencia sin distracciones.',
    },
    {
        icon: HeartHandshake,
        title: 'Hecha para acompañar',
        description:
            'Una herramienta sencilla para compartir el aprendizaje en familia.',
    },
];

export function LandingBenefits() {
    return (
        <section className="bg-[#173e42] px-5 py-24 text-[#fff8e7] sm:px-8 sm:py-32 lg:px-12 dark:bg-[#071c20]">
            <div className="mx-auto grid max-w-7xl gap-16 lg:grid-cols-[1.05fr_1.35fr] lg:items-end">
                <div className="max-w-xl">
                    <p className="text-sm font-semibold tracking-[0.18em] text-[#ffad8e] uppercase">
                        Aprender con intención
                    </p>
                    <h2 className="mt-4 text-4xl leading-tight font-semibold tracking-[-0.045em] text-balance sm:text-5xl">
                        Lo que necesita practicar, presentado de una forma que
                        quiere explorar.
                    </h2>
                </div>

                <div className="grid border-t border-white/20 sm:grid-cols-3">
                    {benefits.map((benefit) => (
                        <div
                            key={benefit.title}
                            className="border-b border-white/20 py-8 sm:border-r sm:border-b-0 sm:px-7 sm:first:pl-0 sm:last:border-r-0"
                        >
                            <benefit.icon className="size-6 text-[#ffad8e]" />
                            <h3 className="mt-7 text-xl font-semibold tracking-[-0.025em]">
                                {benefit.title}
                            </h3>
                            <p className="mt-3 text-sm leading-relaxed text-[#c7d8d1]">
                                {benefit.description}
                            </p>
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
}
