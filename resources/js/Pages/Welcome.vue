<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Button from '@/Components/Button.vue';
import LocaleSwitcher from '@/Components/LocaleSwitcher.vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const { t } = useI18n();
</script>

<template>
    <Head :title="t('welcome.title')" />
    <div class="min-h-screen bg-surface-50 flex flex-col font-sans selection:bg-brand-500 selection:text-white">
        
        <!-- Navigation -->
        <header class="w-full px-6 py-4 flex justify-between items-center bg-white border-b border-surface-200 shrink-0 sticky top-0 z-50 shadow-sm">
            <div class="flex items-center gap-3">
                <ApplicationLogo class="h-8 w-auto text-brand-600 fill-current" />                
            </div>
            <nav v-if="canLogin" class="flex items-center gap-4">
                <Link :href="route('metodo.mitra')" class="text-sm font-medium text-surface-500 hover:text-brand-600 transition-colors">{{ $t('welcome.nav_mitra') }}</Link>
                <Link :href="route('manual')" class="hidden md:block text-sm font-medium text-surface-500 hover:text-brand-600 transition-colors">{{ $t('welcome.nav_manual') }}</Link>
                <a href="#funcionalidades" class="hidden md:block text-sm font-medium text-surface-500 hover:text-brand-600 transition-colors">{{ $t('welcome.features') }}</a>
                <a href="#diretorio" class="hidden lg:block text-sm font-medium text-surface-500 hover:text-brand-600 transition-colors">{{ $t('welcome.nav_directory') }}</a>
                
                <div class="w-px h-4 bg-surface-200 hidden md:block mx-2"></div>

                <LocaleSwitcher />

                <Link
                    v-if="$page.props.auth.user"
                    :href="route('dashboard')"
                    class="text-sm font-semibold text-brand-600 hover:text-brand-700 transition-colors duration-smooth"
                >
                    {{ $t('welcome.access_dashboard') }}
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="text-sm font-medium text-surface-600 hover:text-brand-600 transition-colors duration-smooth"
                    >
                        {{ $t('welcome.enter') }}
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                    >
                        <Button variant="primary" size="sm" class="shadow-sm">{{ $t('welcome.register') }}</Button>
                    </Link>
                </template>
            </nav>
        </header>

        <!-- Hero Section -->
        <main class="flex-grow flex flex-col">
            
            <section class="relative pt-20 pb-24 lg:pt-32 lg:pb-32 overflow-hidden border-b border-surface-200 bg-white">
                <div class="absolute inset-0 bg-[linear-gradient(to_right,#f0fdf4_1px,transparent_1px),linear-gradient(to_bottom,#f0fdf4_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_0%,#000_70%,transparent_100%)] opacity-30"></div>
                
                <div class="max-w-7xl mx-auto px-6 relative z-10">
                    <div class="grid lg:grid-cols-2 gap-12 lg:gap-8 items-center">
                        <div class="max-w-2xl">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-xs font-semibold uppercase tracking-wider mb-6">
                                <span class="w-2 h-2 rounded-full bg-brand-500 animate-pulse"></span>
                                {{ $t('welcome.inspection_tool') }}
                            </div>
                            
                            <h1 class="text-5xl lg:text-6xl font-extrabold text-surface-900 tracking-tight mb-6 leading-[1.1]">
                                {{ $t('welcome.hero_title_start') }}<span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-brand-400">{{ $t('welcome.hero_title_highlight') }}</span>
                            </h1>
                            
                            <p class="text-lg text-surface-500 mb-10 leading-relaxed max-w-xl">
                                {{ $t('welcome.hero_description') }}
                            </p>
                            
                            <div class="flex flex-col sm:flex-row gap-4">
                                <template v-if="$page.props.auth.user">
                                    <Link :href="route('dashboard')">
                                        <Button variant="primary" size="lg" class="w-full sm:w-auto px-8 py-3.5 text-base shadow-brand">{{ $t('welcome.go_to_dashboard') }}</Button>
                                    </Link>
                                </template>
                                <template v-else>
                                    <Link :href="route('register')">
                                        <Button variant="primary" size="lg" class="w-full sm:w-auto px-8 py-3.5 text-base shadow-brand">{{ $t('welcome.start_evaluating') }}</Button>
                                    </Link>
                                    <Link :href="route('login')" class="w-full sm:w-auto">
                                        <Button variant="outline" size="lg" class="w-full sm:w-auto px-8 py-3.5 text-base hover:bg-surface-50">{{ $t('welcome.login') }}</Button>
                                    </Link>
                                </template>
                            </div>
                        </div>
                        
                        <div class="relative lg:ml-auto w-full max-w-md mx-auto lg:max-w-none">
                            <div class="absolute -inset-4 bg-brand-100/50 rounded-full blur-3xl opacity-50"></div>
                            <img src="/images/undraw_personal-data_a1n8.svg" :alt="$t('welcome.alt_data_protection')" class="relative z-10 w-full h-auto drop-shadow-xl" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Visão Geral -->
            <section class="py-20 bg-surface-50" id="visao-geral">
                <div class="max-w-4xl mx-auto px-6 text-center">
                    <h2 class="text-3xl font-bold text-surface-900 mb-6">{{ $t('welcome.challenge_title') }}</h2>
                    <p class="text-lg text-surface-600 leading-relaxed">
                        {{ $t('welcome.challenge_text') }}
                    </p>
                </div>
            </section>

            <!-- Features Destaque -->
            <section class="py-12 bg-surface-50 border-b border-surface-200">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid md:grid-cols-3 gap-8">
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-surface-200 hover:shadow-tactile hover:-translate-y-1 transition-all duration-300">
                            <div class="h-12 w-12 bg-brand-50 rounded-xl flex items-center justify-center mb-6 text-brand-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-surface-900 mb-3">{{ $t('welcome.feature1_title') }}</h3>
                            <p class="text-surface-600 leading-relaxed">{{ $t('welcome.feature1_text') }}</p>
                        </div>
                        
                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-surface-200 hover:shadow-tactile hover:-translate-y-1 transition-all duration-300">
                            <div class="h-12 w-12 bg-brand-50 rounded-xl flex items-center justify-center mb-6 text-brand-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-surface-900 mb-3">{{ $t('welcome.feature2_title') }}</h3>
                            <p class="text-surface-600 leading-relaxed">{{ $t('welcome.feature2_text') }}</p>
                        </div>

                        <div class="bg-white p-8 rounded-2xl shadow-sm border border-surface-200 hover:shadow-tactile hover:-translate-y-1 transition-all duration-300">
                            <div class="h-12 w-12 bg-brand-50 rounded-xl flex items-center justify-center mb-6 text-brand-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-surface-900 mb-3">{{ $t('welcome.feature3_title') }}</h3>
                            <p class="text-surface-600 leading-relaxed">{{ $t('welcome.feature3_text') }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Metodologia -->
            <section id="metodologia" class="py-24 bg-white border-b border-surface-200 overflow-hidden">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid lg:grid-cols-2 gap-16 items-center">
                        <div class="order-2 lg:order-1 relative">
                            <div class="absolute -inset-4 bg-brand-50 rounded-full blur-3xl opacity-50"></div>
                            <img src="/images/undraw_online-survey_xq2g.svg" :alt="$t('welcome.alt_methodology')" class="relative z-10 w-full h-auto drop-shadow-lg" />
                        </div>
                        
                        <div class="order-1 lg:order-2">
                            <h2 class="text-3xl lg:text-4xl font-bold text-surface-900 mb-6">{{ $t('welcome.methodology_title') }}</h2>
                            <p class="text-lg text-surface-600 mb-6 leading-relaxed">
                                {{ $t('welcome.methodology_intro', { count: 5 }) }}
                            </p>
                            
                            <ul class="space-y-4 mb-8">
                                <li class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold">1</div>
                                    <span class="text-surface-700 font-medium">{{ $t('welcome.dimension1') }}</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold">2</div>
                                    <span class="text-surface-700 font-medium">{{ $t('welcome.dimension2') }}</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold">3</div>
                                    <span class="text-surface-700 font-medium">{{ $t('welcome.dimension3') }}</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold">4</div>
                                    <span class="text-surface-700 font-medium">{{ $t('welcome.dimension4') }}</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <div class="flex-shrink-0 w-8 h-8 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold">5</div>
                                    <span class="text-surface-700 font-medium">{{ $t('welcome.dimension5') }}</span>
                                </li>
                            </ul>

                            <hr class="border-surface-200 mb-6" />

                            <p class="text-surface-600 leading-relaxed mb-6">
                                {{ $t('welcome.methodology_explanation') }} <span class="font-semibold text-yellow-600">{{ $t('welcome.medal_gold') }}</span>, <span class="font-semibold text-gray-400">{{ $t('welcome.medal_silver') }}</span>, <span class="font-semibold text-amber-700">{{ $t('welcome.medal_bronze') }}</span>{{ $t('welcome.and_conjunction') }}<span class="font-semibold text-red-500">{{ $t('welcome.medal_incipient') }}</span>.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Funcionalidades Completas -->
            <section id="funcionalidades" class="py-24 bg-surface-50 border-b border-surface-200">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="text-center max-w-2xl mx-auto mb-16">
                        <h2 class="text-3xl font-bold text-surface-900 mb-4">{{ $t('welcome.features_title') }}</h2>
                        <p class="text-lg text-surface-600">
                            {{ $t('welcome.features_subtitle') }}
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 gap-12 items-center">
                        <div class="space-y-8">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-10 h-10 rounded-full bg-white shadow-sm border border-surface-200 flex items-center justify-center text-brand-600">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-surface-900 mb-2">{{ $t('welcome.feat_forms_title') }}</h4>
                                    <p class="text-surface-600 leading-relaxed">
                                        {{ $t('welcome.feat_forms_text') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-10 h-10 rounded-full bg-white shadow-sm border border-surface-200 flex items-center justify-center text-brand-600">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-surface-900 mb-2">{{ $t('welcome.feat_charts_title') }}</h4>
                                    <p class="text-surface-600 leading-relaxed">
                                        {{ $t('welcome.feat_charts_text') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-10 h-10 rounded-full bg-white shadow-sm border border-surface-200 flex items-center justify-center text-brand-600">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-surface-900 mb-2">{{ $t('welcome.feat_export_title') }}</h4>
                                    <p class="text-surface-600 leading-relaxed">
                                        {{ $t('welcome.feat_export_text') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="relative flex justify-center lg:justify-end">
                            <img src="/images/undraw_terms_sx63.svg" :alt="$t('welcome.alt_features')" class="w-full max-w-sm h-auto drop-shadow-xl" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Método Mitra (New Section) -->
            <section id="metodo-mitra" class="py-24 bg-surface-50 border-b border-surface-200">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid lg:grid-cols-2 gap-16 items-center">
                        <div>
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-xs font-semibold uppercase tracking-wider mb-6">
                                {{ $t('welcome.scientific_basis') }}
                            </div>
                            <h2 class="text-3xl lg:text-4xl font-bold text-surface-900 mb-6">{{ $t('welcome.mitra_title') }}</h2>
                            <p class="text-lg text-surface-600 mb-6 leading-relaxed">
                                {{ $t('welcome.mitra_text1') }}
                            </p>
                            <p class="text-surface-600 mb-8 leading-relaxed">
                                {{ $t('welcome.mitra_text2') }}
                            </p>
                            <Link :href="route('metodo.mitra')">
                                <Button variant="outline" size="lg" class="gap-2">
                                    {{ $t('welcome.mitra_learn_more') }}
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                                </Button>
                            </Link>
                        </div>
                        <div class="relative flex justify-center lg:justify-end">
                            <div class="absolute -inset-4 bg-brand-100/50 rounded-full blur-3xl opacity-50"></div>
                            <img src="/images/undraw_logic_re_nyb4.svg" :alt="$t('welcome.alt_mitra')" class="relative z-10 w-full max-w-sm h-auto drop-shadow-xl" />
                        </div>
                    </div>
                </div>
            </section>
 
            <!-- Diretório Público (New Section) -->
            <section id="diretorio" class="py-24 bg-white border-b border-surface-200">
                <div class="max-w-7xl mx-auto px-6">
                    <div class="grid lg:grid-cols-2 gap-16 items-center">
                        <div class="relative flex justify-center lg:justify-start order-2 lg:order-1">
                            <div class="absolute -inset-4 bg-brand-50 rounded-full blur-3xl opacity-50"></div>
                            <img src="/images/undraw_performance-overview_1b4y.svg" :alt="$t('welcome.alt_directory')" class="relative z-10 w-full max-w-sm h-auto drop-shadow-xl" />
                        </div>
                        
                        <div class="order-1 lg:order-2">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-xs font-semibold uppercase tracking-wider mb-6">
                                {{ $t('welcome.public_transparency') }}
                            </div>
                            <h2 class="text-3xl lg:text-4xl font-bold text-surface-900 mb-6">{{ $t('welcome.directory_title') }}</h2>
                            <p class="text-lg text-surface-600 mb-8 leading-relaxed">
                                {{ $t('welcome.directory_text') }}
                            </p>
                            
                            <div class="space-y-4 mb-10">
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <p class="text-surface-600 text-sm"><span class="font-semibold text-surface-900">{{ $t('welcome.directory_open_query') }}</span> {{ $t('welcome.directory_open_query_text') }}</p>
                                </div>
                                <div class="flex items-start gap-3">
                                    <div class="mt-1 flex-shrink-0 w-5 h-5 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                    </div>
                                    <p class="text-surface-600 text-sm"><span class="font-semibold text-surface-900">{{ $t('welcome.directory_history') }}</span> {{ $t('welcome.directory_history_text') }}</p>
                                </div>
                            </div>
 
                            <Link :href="route('public.tools.index')">
                                <Button variant="primary" size="lg" class="w-full sm:w-auto px-8 py-4 text-base shadow-brand">
                                    {{ $t('welcome.directory_browse') }}
                                </Button>
                            </Link>
                        </div>
                    </div>
                </div>
            </section>

        </main>

        <!-- Informações Oficiais e Rodapé -->
        <footer class="bg-surface-900 text-surface-300 py-12 shrink-0">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-8 items-center mb-8">
                    <div>
                        <div class="flex items-center gap-3 mb-6">
                            <ApplicationLogo class="h-6 w-auto text-brand-400 fill-current" />                            
                        </div>
                        <p class="text-sm max-w-sm leading-relaxed text-surface-400">
                            {{ $t('welcome.footer_description') }}
                        </p>
                    </div>
                    <div class="flex flex-col md:items-end gap-3 text-sm">
                        <span class="font-semibold text-white mb-2">{{ $t('welcome.footer_references') }}</span>
                        <a href="https://each.usp.br/cond_met_pand/trmodel/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2">
                            <span>{{ $t('welcome.footer_trmodel') }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                        <a href="https://www.gov.br/esporte/pt-br/acesso-a-informacao/lgpd" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2">
                            <span>{{ $t('welcome.footer_lgpd') }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </a>
                    </div>
                </div>

                <hr class="border-surface-800 mb-8" />

                <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-surface-500">
                    <p>&copy; {{ new Date().getFullYear() }} Privacy Tool. {{ $t('welcome.footer_rights') }}</p>
                    <p class="mt-2 sm:mt-0">{{ $t('welcome.developed_with', { laravel: laravelVersion, php: phpVersion }) }}</p>
                </div>
            </div>
        </footer>
    </div>
</template>
