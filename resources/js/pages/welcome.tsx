import { Head, usePage } from '@inertiajs/react';
import { LandingBenefits } from '@/components/landing/landing-benefits';
import { LandingCta } from '@/components/landing/landing-cta';
import { LandingHero } from '@/components/landing/landing-hero';
import { LandingSteps } from '@/components/landing/landing-steps';

export default function Welcome() {
    const { auth } = usePage().props;

    return (
        <>
            <Head title="Aprendizaje personalizado" />

            <main className="overflow-hidden bg-[#fffaf0] text-[#18363a] dark:bg-[#10272b] dark:text-[#fff8e7]">
                <LandingHero isAuthenticated={Boolean(auth.user)} />
                <LandingSteps />
                <LandingBenefits />
                <LandingCta isAuthenticated={Boolean(auth.user)} />
            </main>
        </>
    );
}
