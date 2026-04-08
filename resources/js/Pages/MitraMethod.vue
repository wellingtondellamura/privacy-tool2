<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';

// Determine which layout to use based on authentication
const props = defineProps({
    auth: Object,
});

const isMobile = typeof window !== 'undefined' ? window.innerWidth < 768 : false;

const { t } = useI18n();
</script>

<template>
    <Head :title="t('mitra.page_title')" />

    <component :is="$page.props.auth.user ? AuthenticatedLayout : PublicLayout" :title="t('mitra.header')">
        <template v-if="$page.props.auth.user" #header>
            <h2 class="font-semibold text-xl text-surface-800 leading-tight">
                {{ $t('mitra.header') }}
            </h2>
        </template>

        <div class="py-12 bg-surface-50 min-h-screen">
            <div class="max-w-5xl mx-auto px-6 lg:px-8">
                
                <!-- Hero Section for the Method -->
                <div class="bg-white rounded-3xl shadow-sm border border-surface-200 overflow-hidden mb-12">
                    <div class="relative p-8 md:p-12 lg:p-16">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full -mr-32 -mt-32 blur-3xl opacity-60"></div>
                        <div class="relative z-10 max-w-3xl">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-xs font-semibold uppercase tracking-wider mb-6">
                                {{ $t('mitra.scientific_badge') }}
                            </div>
                            <h1 class="text-4xl md:text-5xl font-extrabold text-surface-900 tracking-tight mb-6">
                                {{ $t('mitra.hero_title_start') }}<span class="text-brand-600">{{ $t('mitra.hero_title_highlight') }}</span>
                            </h1>
                            <p class="text-lg text-surface-600 leading-relaxed mb-8">
                                {{ $t('mitra.hero_description') }}
                            </p>
                            <div class="flex flex-wrap gap-4">
                                <a href="https://sol.sbc.org.br/index.php/ihc/article/view/37687" target="_blank" rel="noopener noreferrer">
                                    <Button variant="outline" class="gap-2">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                        Artigo Científico (SBC)
                                    </Button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Three Main Pillars -->
                <div class="grid md:grid-cols-2 gap-8 mb-16">
                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-surface-200">
                        <div class="h-12 w-12 bg-brand-50 rounded-xl flex items-center justify-center mb-6 text-brand-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-surface-900 mb-4">{{ $t('mitra.what_is_title') }}</h3>
                        <p class="text-surface-600 leading-relaxed">
                            {{ $t('mitra.what_is_text') }}
                        </p>
                    </div>

                    <div class="bg-white p-8 rounded-2xl shadow-sm border border-surface-200">
                        <div class="h-12 w-12 bg-brand-50 rounded-xl flex items-center justify-center mb-6 text-brand-600">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-surface-900 mb-4">{{ $t('mitra.for_whom_title') }}</h3>
                        <p class="text-surface-600 leading-relaxed">
                            {{ $t('mitra.for_whom_text') }}
                        </p>
                    </div>
                </div>

                <!-- Dimensions/Groups -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-8 text-center">{{ $t('mitra.dimensions_title') }}</h2>
                    <div class="grid gap-6">
                        <div class="flex flex-col md:flex-row gap-6 bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-lg">1</div>
                            <div>
                                <h4 class="text-lg font-bold text-surface-900 mb-2">{{ $t('mitra.dim1_title') }}</h4>
                                <p class="text-surface-600">{{ $t('mitra.dim1_text') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-6 bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-lg">2</div>
                            <div>
                                <h4 class="text-lg font-bold text-surface-900 mb-2">{{ $t('mitra.dim2_title') }}</h4>
                                <p class="text-surface-600">{{ $t('mitra.dim2_text') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-6 bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-lg">3</div>
                            <div>
                                <h4 class="text-lg font-bold text-surface-900 mb-2">{{ $t('mitra.dim3_title') }}</h4>
                                <p class="text-surface-600">{{ $t('mitra.dim3_text') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-6 bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-lg">4</div>
                            <div>
                                <h4 class="text-lg font-bold text-surface-900 mb-2">{{ $t('mitra.dim4_title') }}</h4>
                                <p class="text-surface-600">{{ $t('mitra.dim4_text') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col md:flex-row gap-6 bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-lg">5</div>
                            <div>
                                <h4 class="text-lg font-bold text-surface-900 mb-2">{{ $t('mitra.dim5_title') }}</h4>
                                <p class="text-surface-600">{{ $t('mitra.dim5_text') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Classification System -->
                <div class="bg-surface-900 rounded-3xl p-8 md:p-12 text-white relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_30%_20%,#16a34a_0%,transparent_50%)] opacity-20"></div>
                    <div class="relative z-10 text-center max-w-2xl mx-auto">
                        <h2 class="text-3xl font-bold mb-6">{{ $t('mitra.medals_title') }}</h2>
                        <p class="text-surface-300 mb-10">
                            {{ $t('mitra.medals_description') }}
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-yellow-400 rounded-full flex items-center justify-center text-yellow-900 text-2xl mb-3 shadow-lg shadow-yellow-400/20">🥇</div>
                                <span class="font-bold">{{ $t('mitra.medal_gold') }}</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-gray-300 rounded-full flex items-center justify-center text-gray-700 text-2xl mb-3 shadow-lg shadow-gray-300/20">🥈</div>
                                <span class="font-bold">{{ $t('mitra.medal_silver') }}</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-amber-600 rounded-full flex items-center justify-center text-amber-100 text-2xl mb-3 shadow-lg shadow-amber-600/20">🥉</div>
                                <span class="font-bold">{{ $t('mitra.medal_bronze') }}</span>
                            </div>
                            <div class="flex flex-col items-center">
                                <div class="w-16 h-16 bg-red-400 rounded-full flex items-center justify-center text-red-900 text-2xl mb-3 shadow-lg shadow-red-400/20">⚠️</div>
                                <span class="font-bold">{{ $t('mitra.medal_incipient') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Call to Action -->
                <div class="mt-16 text-center">
                    <h3 class="text-2xl font-bold text-surface-900 mb-6">{{ $t('mitra.cta_title') }}</h3>
                    <div class="flex justify-center gap-4">
                        <Link v-if="!$page.props.auth.user" :href="route('register')">
                            <Button variant="primary" size="lg">{{ $t('mitra.cta_start') }}</Button>
                        </Link>
                        <Link v-else :href="route('dashboard')">
                            <Button variant="primary" size="lg">{{ $t('mitra.cta_dashboard') }}</Button>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </component>
</template>
