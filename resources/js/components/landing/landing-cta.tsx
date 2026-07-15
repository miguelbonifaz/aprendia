import { Link } from '@inertiajs/react';
import { ArrowRight } from 'lucide-react';
import { dashboard, register } from '@/routes';

type LandingCtaProps = {
    isAuthenticated: boolean;
};

export function LandingCta({ isAuthenticated }: LandingCtaProps) {
    return (
        <>
            <section className="bg-[#ed8c68] px-5 py-24 text-[#17383c] sm:px-8 sm:py-32 lg:px-12 dark:bg-[#c95f49] dark:text-[#fff8e7]">
                <div className="mx-auto flex max-w-7xl flex-col items-start justify-between gap-10 lg:flex-row lg:items-end">
                    <div className="max-w-3xl">
                        <p className="text-sm font-semibold tracking-[0.18em] uppercase opacity-75">
                            Empecemos
                        </p>
                        <h2 className="mt-4 text-5xl leading-[1.02] font-semibold tracking-[-0.055em] text-balance sm:text-6xl">
                            Hoy puede ser un buen día para aprender algo nuevo.
                        </h2>
                    </div>
                    <Link
                        href={isAuthenticated ? dashboard() : register()}
                        className="inline-flex shrink-0 items-center gap-3 rounded-full bg-[#17383c] px-7 py-4 font-semibold text-white shadow-lg transition hover:-translate-y-1 hover:bg-[#0e292c] motion-reduce:transform-none dark:bg-[#fff4da] dark:text-[#17383c]"
                    >
                        {isAuthenticated ? 'Ir al panel' : 'Registrarse'}
                        <ArrowRight className="size-4" />
                    </Link>
                </div>
            </section>

            <footer className="bg-[#fffaf0] px-5 py-8 text-sm text-[#5c7373] sm:px-8 lg:px-12 dark:bg-[#10272b] dark:text-[#9db3ad]">
                <div className="mx-auto flex max-w-7xl items-center justify-between gap-4">
                    <span className="font-semibold text-[#17383c] dark:text-[#fff8e7]">
                        Aprendia
                    </span>
                    <span>Aprendizaje pensado para cada niño.</span>
                </div>
            </footer>
        </>
    );
}
