<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    illustrationType: {
        type: String,
        default: 'login', // 'login', 'forgot', 'reset', 'confirm', 'verify'
    },
    rightTitle: {
        type: String,
        default: '',
    },
    rightSubtitle: {
        type: String,
        default: '',
    },
    loading: {
        type: Boolean,
        default: false,
    }
});
</script>

<template>
    <div class="min-h-screen bg-surface-50 flex items-center justify-center p-4 sm:p-6 md:p-8 lg:p-12 font-sans relative overflow-hidden">
        <!-- Grid pattern matching Welcome page -->
        <div class="absolute inset-0 bg-[linear-gradient(to_right,#cbd5e1_1px,transparent_1px),linear-gradient(to_bottom,#cbd5e1_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-25 pointer-events-none"></div>
        <!-- Glow background decorations (Ambient Lighting) -->
        <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-brand-500/5 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-[600px] h-[600px] bg-indigo-500/5 blur-[120px] rounded-full pointer-events-none"></div>

        <!-- Centered Card Container -->
        <div class="w-full max-w-5xl bg-white rounded-[2rem] shadow-[0_20px_50px_-12px_rgba(15,23,42,0.12)] overflow-hidden flex flex-col md:flex-row border border-surface-200 relative z-10 min-h-[500px] lg:min-h-[580px] transition-all duration-smooth">
            
            <!-- Left Onboarding Panel -->
            <div class="md:w-[42%] bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 text-white flex flex-col justify-between p-8 lg:p-10 relative overflow-hidden hidden md:flex">
                <!-- Inner glow -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 blur-3xl rounded-full translate-x-1/3 -translate-y-1/3"></div>
                <div class="absolute bottom-0 left-0 w-64 h-64 bg-brand-300/10 blur-3xl rounded-full -translate-x-1/3 translate-y-1/3"></div>

                <!-- Header logo -->
                <div class="relative z-10 flex items-center">
                    <Link href="/" class="text-white hover:opacity-90 transition-opacity duration-smooth">
                        <ApplicationLogo class="h-8 w-auto text-white" />
                    </Link>
                </div>

                <!-- Step Illustration and Dynamic Sub-copy -->
                <div class="relative z-10 my-auto py-6">
                    <!-- SVG Illustration -->
                    <div class="mb-6">
                        <svg class="w-full max-w-[160px] lg:max-w-[190px] aspect-square mx-auto text-brand-200 opacity-95 drop-shadow-xl" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <circle cx="100" cy="100" r="80" fill="url(#bgGradient)" />
                            <!-- Connecting lines -->
                            <path d="M60 100 L100 60 L140 100 L100 140 Z" stroke="currentColor" stroke-width="1.5" stroke-dasharray="4 4" class="animate-[spin_40s_linear_infinite]" style="transform-origin: 100px 100px;" />
                            <line x1="100" y1="20" x2="100" y2="180" stroke="currentColor" stroke-width="0.75" opacity="0.4" />
                            <line x1="20" y1="100" x2="180" y2="100" stroke="currentColor" stroke-width="0.75" opacity="0.4" />
                            <!-- Core shield/lock -->
                            <g transform="translate(75, 70)">
                                <path d="M25 0 L50 10 V35 C50 48.75 39.25 58.5 25 62 C10.75 58.5 0 48.75 0 35 V10 L25 0 Z" fill="url(#shieldGrad)" />
                                <!-- Dynamic Icons inside the shield based on illustrationType -->
                                <path v-if="illustrationType === 'login'" d="M20 22a5 5 0 1 0 6 4.9V38h4v4h4v-4h-4v-4h-4v-7.1a5 5 0 0 0-6-4.9zm5 7a2 2 0 1 1 0-4 2 2 0 0 1 0 4z" fill="#fff" />
                                <path v-else-if="illustrationType === 'forgot'" d="M25 12a13 13 0 0 0-13 13h4a9 9 0 1 1 9 9v4a13 13 0 0 0 0-26zm-2 22v4h4v-4h-4z" fill="#fff" />
                                <path v-else-if="illustrationType === 'reset'" d="M25 14a8 8 0 0 0-8 8v6h16v-6a8 8 0 0 0-8-8zm-4 8a4 4 0 1 1 8 0v6H21v-6zm-4 12a4 4 0 0 0-4 4v10a4 4 0 0 0 4 4h16a4 4 0 0 0 4-4V38a4 4 0 0 0-4-4H17zm8 6a2 2 0 1 1 0 4 2 2 0 0 1 0-4z" fill="#fff" />
                                <path v-else-if="illustrationType === 'confirm'" d="M17 26v-6a8 8 0 1 1 16 0v6h2v18H15V26h2zm4 0h8v-6a4 4 0 1 0-8 0v6zm4 8a2 2 0 1 0 0 4 2 2 0 0 0 0-4z" fill="#fff" />
                                <path v-else-if="illustrationType === 'verify'" d="M12 18h26v24H12V18zm3 4l10 7.5L35 22v-2L25 27.5 15 20v2zm0 18h20V25L25 32.5 15 25v15z" fill="#fff" />
                            </g>
                            <!-- Orbiting dots -->
                            <circle cx="100" cy="20" r="6" fill="#fff" class="animate-pulse" />
                            <circle cx="100" cy="180" r="6" fill="#fff" />
                            <circle cx="20" cy="100" r="6" fill="#cbd5e1" />
                            <circle cx="180" cy="100" r="6" fill="#fff" class="animate-pulse" />
                            
                            <defs>
                                <radialGradient id="bgGradient" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="#ffffff" stop-opacity="0.2" />
                                    <stop offset="100%" stop-color="#ffffff" stop-opacity="0" />
                                </radialGradient>
                                <linearGradient id="shieldGrad" x1="25" y1="0" x2="25" y2="62" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#ffffff" />
                                    <stop offset="100%" stop-color="#e2e8f0" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>

                    <h2 class="text-xl lg:text-2xl font-extrabold tracking-tight mb-3 text-center leading-snug">
                        {{ title }}
                    </h2>
                    <p class="text-brand-100 text-xs lg:text-sm leading-relaxed text-center opacity-85">
                        {{ description }}
                    </p>
                </div>

                <!-- Footer info -->
                <div class="relative z-10 pt-4 mt-4 border-t border-white/10 text-[10px] text-brand-200 flex justify-between items-center">
                    <span>{{ $t('results.footer.developed_with') }}</span>
                    <span>v1.0</span>
                </div>
            </div>

            <!-- Right Form Panel -->
            <div class="flex-1 flex flex-col justify-center py-10 px-6 sm:px-12 md:px-14 lg:px-16 bg-white relative">
                <!-- Loading Overlay (Visibility of System Status Heuristic) -->
                <transition name="fade">
                    <div 
                        v-if="loading" 
                        class="absolute inset-0 bg-white/95 backdrop-blur-md z-50 flex flex-col items-center justify-center p-6 text-center"
                    >
                        <div class="relative flex items-center justify-center mb-6">
                            <div class="absolute w-20 h-20 rounded-full border-4 border-brand-500/20 animate-ping"></div>
                            <svg class="animate-spin h-12 w-12 text-brand-500 relative z-10" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-surface-900 mb-2">
                            {{ $t('common.processing') }}
                        </h3>
                    </div>
                </transition>

                <!-- Mobile Header -->
                <div class="absolute top-6 left-6 right-6 flex items-center justify-between md:hidden">
                    <Link href="/">
                        <ApplicationLogo class="h-8 w-auto text-brand-900" />
                    </Link>
                </div>

                <!-- Form Content Wrapper -->
                <div class="mx-auto w-full max-w-md mt-6 md:mt-0">
                    <div v-if="rightTitle" class="mb-6">
                        <h1 class="text-2xl font-bold tracking-tight text-surface-900">
                            {{ rightTitle }}
                        </h1>
                        <p v-if="rightSubtitle" class="text-sm text-surface-500 mt-1">
                            {{ rightSubtitle }}
                        </p>
                    </div>

                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.35s cubic-bezier(0.4, 0, 0.2, 1);
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
