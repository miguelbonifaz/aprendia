import { Link } from '@inertiajs/react';
import { ArrowDown, ArrowRight } from 'lucide-react';
import { dashboard, home, login, register } from '@/routes';

type LandingHeroProps = {
    isAuthenticated: boolean;
};

export function LandingHero({ isAuthenticated }: LandingHeroProps) {
    return (
        <section className="group relative isolate min-h-svh overflow-hidden bg-[#f3d89f] text-[#153337] dark:bg-[#10272b] dark:text-[#fff8e7]">
            <img
                src="/images/landing/aprendia-hero.webp"
                alt="Un padre acompaña a su hija durante una actividad visual de aprendizaje"
                className="absolute inset-0 z-0 h-full w-full object-cover object-[66%_center] transition-transform duration-[1600ms] ease-out group-hover:scale-[1.015] motion-reduce:transition-none md:object-center dark:brightness-[.48]"
            />
            <div className="absolute inset-0 z-10 bg-linear-to-b from-[#f9e8bd]/95 via-[#f9e8bd]/80 to-[#f9e8bd]/30 md:bg-linear-to-r md:from-[#f9e8bd]/95 md:via-[#f9e8bd]/80 md:to-transparent dark:from-[#10272b]/95 dark:via-[#10272b]/80 dark:to-[#10272b]/20" />
            <div className="absolute inset-x-0 top-0 z-10 h-40 bg-linear-to-b from-black/10 to-transparent" />

            <header className="relative z-20 mx-auto flex h-24 w-full max-w-7xl items-center justify-between px-5 sm:px-8 lg:px-12">
                <Link
                    href={home()}
                    className="text-2xl font-semibold tracking-[-0.04em] sm:text-3xl"
                >
                    Aprendia
                </Link>
                {isAuthenticated ? (
                    <Link
                        href={dashboard()}
                        className="rounded-full bg-[#163b3f] px-5 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#0d2c30] motion-reduce:transform-none dark:bg-[#fff2d4] dark:text-[#17383c]"
                    >
                        Ir al panel
                    </Link>
                ) : (
                    <nav
                        className="flex items-center gap-3"
                        aria-label="Acceso"
                    >
                        <Link
                            href={login()}
                            className="hidden text-sm font-semibold sm:inline"
                        >
                            Iniciar sesión
                        </Link>
                        <Link
                            href={register()}
                            className="rounded-full bg-[#163b3f] px-5 py-2.5 text-sm font-semibold text-white transition hover:-translate-y-0.5 hover:bg-[#0d2c30] motion-reduce:transform-none dark:bg-[#fff2d4] dark:text-[#17383c]"
                        >
                            Registrarse
                        </Link>
                    </nav>
                )}
            </header>

            <div className="relative z-20 mx-auto flex min-h-[calc(100svh-6rem)] w-full max-w-7xl items-center px-5 pb-16 sm:px-8 lg:px-12">
                <div className="flex w-full max-w-2xl flex-col items-center pt-6 pb-10 text-center md:max-w-[52%]">
                    <p className="text-sm font-semibold tracking-[0.18em] text-[#b34d38] uppercase motion-safe:animate-in motion-safe:duration-700 motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-3 dark:text-[#ffb596]">
                        Aprendizaje a su medida
                    </p>
                    <h1 className="mt-5 text-5xl leading-[0.98] font-semibold tracking-[-0.055em] text-balance motion-safe:animate-in motion-safe:delay-150 motion-safe:duration-700 motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-5 sm:text-6xl lg:text-7xl">
                        Una forma de aprender hecha para cada niño.
                    </h1>
                    <p className="mt-6 max-w-lg text-lg leading-relaxed text-[#31575a] motion-safe:animate-in motion-safe:delay-300 motion-safe:duration-700 motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-5 dark:text-[#d7e3dc]">
                        Crea actividades visuales según su edad y lo que
                        necesita practicar, listas para compartir un momento de
                        aprendizaje.
                    </p>
                    <div className="mt-9 flex flex-wrap items-center justify-center gap-4 motion-safe:animate-in motion-safe:delay-500 motion-safe:duration-700 motion-safe:fade-in-0 motion-safe:slide-in-from-bottom-5">
                        <Link
                            href={isAuthenticated ? dashboard() : register()}
                            className="inline-flex items-center gap-2 rounded-full bg-[#df654b] px-7 py-4 text-base font-semibold text-white shadow-lg shadow-[#8d3422]/15 transition hover:-translate-y-1 hover:bg-[#c9533d] hover:shadow-xl motion-reduce:transform-none"
                        >
                            {isAuthenticated ? 'Ir al panel' : 'Registrarse'}
                            <ArrowRight className="size-4" />
                        </Link>
                        {!isAuthenticated && (
                            <Link
                                href={login()}
                                className="text-sm font-semibold underline decoration-[#df654b]/50 decoration-2 underline-offset-8 transition hover:decoration-[#df654b]"
                            >
                                Ya tengo una cuenta
                            </Link>
                        )}
                    </div>
                </div>
            </div>

            <a
                href="#como-funciona"
                aria-label="Ver cómo funciona"
                className="absolute bottom-7 left-1/2 z-20 hidden -translate-x-1/2 rounded-full border border-[#17383c]/20 bg-[#fff8e7]/50 p-3 backdrop-blur-sm transition hover:bg-[#fff8e7] motion-safe:animate-bounce motion-reduce:animate-none md:block dark:border-white/25 dark:bg-[#10272b]/50"
            >
                <ArrowDown className="size-4" />
            </a>
        </section>
    );
}
