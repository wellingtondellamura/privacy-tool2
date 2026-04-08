<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Button from '@/Components/Button.vue';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const activeSection = ref(null);
const toggle = (id) => { activeSection.value = activeSection.value === id ? null : id; };

const roles = [
    { nameKey: 'manual.role_owner_name', descKey: 'manual.role_owner_desc' },
    { nameKey: 'manual.role_evaluator_name', descKey: 'manual.role_evaluator_desc' },
    { nameKey: 'manual.role_observer_name', descKey: 'manual.role_observer_desc' },
];

const medals = [
    { icon: '🥇', nameKey: 'manual.medal_gold_name', descKey: 'manual.medal_gold_desc', bg: 'bg-yellow-400', text: 'text-yellow-900', shadow: 'shadow-yellow-400/30' },
    { icon: '🥈', nameKey: 'manual.medal_silver_name', descKey: 'manual.medal_silver_desc', bg: 'bg-gray-300', text: 'text-gray-700', shadow: 'shadow-gray-300/30' },
    { icon: '🥉', nameKey: 'manual.medal_bronze_name', descKey: 'manual.medal_bronze_desc', bg: 'bg-amber-600', text: 'text-amber-100', shadow: 'shadow-amber-600/30' },
    { icon: '⚠️', nameKey: 'manual.medal_incipient_name', descKey: 'manual.medal_incipient_desc', bg: 'bg-red-400', text: 'text-red-900', shadow: 'shadow-red-400/30' },
];

const answerLevels = [
    { labelKey: 'manual.answer_sufficient_label', value: 100, color: 'bg-green-100 text-green-800 border-green-200', descKey: 'manual.answer_sufficient_desc' },
    { labelKey: 'manual.answer_insufficient_label', value: 50, color: 'bg-yellow-100 text-yellow-800 border-yellow-200', descKey: 'manual.answer_insufficient_desc' },
    { labelKey: 'manual.answer_nonexistent_label', value: 0, color: 'bg-red-100 text-red-800 border-red-200', descKey: 'manual.answer_nonexistent_desc' },
    { labelKey: 'manual.answer_na_label', value: '—', color: 'bg-surface-100 text-surface-600 border-surface-200', descKey: 'manual.answer_na_desc' },
];

const dimensions = [
    { num: 1, nameKey: 'manual.dim_1_name', descKey: 'manual.dim_1_desc', icon: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z' },
    { num: 2, nameKey: 'manual.dim_2_name', descKey: 'manual.dim_2_desc', icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { num: 3, nameKey: 'manual.dim_3_name', descKey: 'manual.dim_3_desc', icon: 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4' },
    { num: 4, nameKey: 'manual.dim_4_name', descKey: 'manual.dim_4_desc', icon: 'M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4' },
    { num: 5, nameKey: 'manual.dim_5_name', descKey: 'manual.dim_5_desc', icon: 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z' },
];

const workflow = [
    { step: 1, titleKey: 'manual.wf_1_title', descKey: 'manual.wf_1_desc', icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6' },
    { step: 2, titleKey: 'manual.wf_2_title', descKey: 'manual.wf_2_desc', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z' },
    { step: 3, titleKey: 'manual.wf_3_title', descKey: 'manual.wf_3_desc', icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15' },
    { step: 4, titleKey: 'manual.wf_4_title', descKey: 'manual.wf_4_desc', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2' },
    { step: 5, titleKey: 'manual.wf_5_title', descKey: 'manual.wf_5_desc', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' },
    { step: 6, titleKey: 'manual.wf_6_title', descKey: 'manual.wf_6_desc', icon: 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' },
    { step: 7, titleKey: 'manual.wf_7_title', descKey: 'manual.wf_7_desc', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { step: 8, titleKey: 'manual.wf_8_title', descKey: 'manual.wf_8_desc', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064' },
];

const sections = [
    {
        id: 'cadastro', titleKey: 'manual.sec_cadastro_title', icon: 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z',
        content: [
            { subtitleKey: 'manual.sec_cadastro_sub1', textKey: 'manual.sec_cadastro_text1' },
            { subtitleKey: 'manual.sec_cadastro_sub2', textKey: 'manual.sec_cadastro_text2' },
            { subtitleKey: 'manual.sec_cadastro_sub3', textKey: 'manual.sec_cadastro_text3' },
        ]
    },
    {
        id: 'projetos', titleKey: 'manual.sec_projetos_title', icon: 'M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10',
        content: [
            { subtitleKey: 'manual.sec_projetos_sub1', textKey: 'manual.sec_projetos_text1' },
            { subtitleKey: 'manual.sec_projetos_sub2', textKey: 'manual.sec_projetos_text2' },
            { subtitleKey: 'manual.sec_projetos_sub3', textKey: 'manual.sec_projetos_text3' },
        ]
    },
    {
        id: 'convites', titleKey: 'manual.sec_convites_title', icon: 'M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z',
        content: [
            { subtitleKey: 'manual.sec_convites_sub1', textKey: 'manual.sec_convites_text1' },
            { subtitleKey: 'manual.sec_convites_sub2', textKey: 'manual.sec_convites_text2' },
            { subtitleKey: 'manual.sec_convites_sub3', textKey: 'manual.sec_convites_text3' },
        ]
    },
    {
        id: 'rodadas', titleKey: 'manual.sec_rodadas_title', icon: 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
        content: [
            { subtitleKey: 'manual.sec_rodadas_sub1', textKey: 'manual.sec_rodadas_text1' },
            { subtitleKey: 'manual.sec_rodadas_sub2', textKey: 'manual.sec_rodadas_text2' },
            { subtitleKey: 'manual.sec_rodadas_sub3', textKey: 'manual.sec_rodadas_text3' },
        ]
    },
    {
        id: 'inspecoes', titleKey: 'manual.sec_inspecoes_title', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
        content: [
            { subtitleKey: 'manual.sec_inspecoes_sub1', textKey: 'manual.sec_inspecoes_text1' },
            { subtitleKey: 'manual.sec_inspecoes_sub2', textKey: 'manual.sec_inspecoes_text2' },
            { subtitleKey: 'manual.sec_inspecoes_sub3', textKey: 'manual.sec_inspecoes_text3' },
        ]
    },
    {
        id: 'questionario', titleKey: 'manual.sec_questionario_title', icon: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z',
        content: [
            { subtitleKey: 'manual.sec_questionario_sub1', textKey: 'manual.sec_questionario_text1' },
            { subtitleKey: 'manual.sec_questionario_sub2', textKey: 'manual.sec_questionario_text2' },
        ]
    },
    {
        id: 'resultados', titleKey: 'manual.sec_resultados_title', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z',
        content: [
            { subtitleKey: 'manual.sec_resultados_sub1', textKey: 'manual.sec_resultados_text1' },
            { subtitleKey: 'manual.sec_resultados_sub2', textKey: 'manual.sec_resultados_text2' },
            { subtitleKey: 'manual.sec_resultados_sub3', textKey: 'manual.sec_resultados_text3' },
            { subtitleKey: 'manual.sec_resultados_sub4', textKey: 'manual.sec_resultados_text4' },
        ]
    },
    {
        id: 'diretorio', titleKey: 'manual.sec_diretorio_title', icon: 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064',
        content: [
            { subtitleKey: 'manual.sec_diretorio_sub1', textKey: 'manual.sec_diretorio_text1' },
            { subtitleKey: 'manual.sec_diretorio_sub2', textKey: 'manual.sec_diretorio_text2' },
            { subtitleKey: 'manual.sec_diretorio_sub3', textKey: 'manual.sec_diretorio_text3' },
        ]
    },
    {
        id: 'selos', titleKey: 'manual.sec_selos_title', icon: 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z',
        content: [
            { subtitleKey: 'manual.sec_selos_sub1', textKey: 'manual.sec_selos_text1' },
            { subtitleKey: 'manual.sec_selos_sub2', textKey: 'manual.sec_selos_text2' },
            { subtitleKey: 'manual.sec_selos_sub3', textKey: 'manual.sec_selos_text3' },
            { subtitleKey: 'manual.sec_selos_sub4', textKey: 'manual.sec_selos_text4' },
        ]
    },
    {
        id: 'exportacao', titleKey: 'manual.sec_exportacao_title', icon: 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4',
        content: [
            { subtitleKey: 'manual.sec_exportacao_sub1', textKey: 'manual.sec_exportacao_text1' },
            { subtitleKey: 'manual.sec_exportacao_sub2', textKey: 'manual.sec_exportacao_text2' },
        ]
    },
];
</script>

<template>
    <Head :title="$t('manual.head_title')" />

    <component :is="$page.props.auth.user ? AuthenticatedLayout : PublicLayout" :title="$t('manual.page_title')">
        <template v-if="$page.props.auth.user" #header>
            <h2 class="font-semibold text-xl text-surface-800 leading-tight">{{ $t('manual.page_title') }}</h2>
        </template>

        <div class="py-12 bg-surface-50 min-h-screen">
            <div class="max-w-5xl mx-auto px-6 lg:px-8">

                <!-- Hero -->
                <div class="bg-white rounded-3xl shadow-sm border border-surface-200 overflow-hidden mb-12">
                    <div class="relative p-8 md:p-12 lg:p-16">
                        <div class="absolute top-0 right-0 w-64 h-64 bg-brand-50 rounded-full -mr-32 -mt-32 blur-3xl opacity-60"></div>
                        <div class="absolute bottom-0 left-0 w-48 h-48 bg-brand-100 rounded-full -ml-24 -mb-24 blur-3xl opacity-40"></div>
                        <div class="relative z-10 max-w-3xl">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-xs font-semibold uppercase tracking-wider mb-6">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                {{ $t('manual.hero_badge') }}
                            </div>
                            <h1 class="text-4xl md:text-5xl font-extrabold text-surface-900 tracking-tight mb-6">
                                {{ $t('manual.hero_title_prefix') }} <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-600 to-brand-400">{{ $t('manual.hero_title_highlight') }}</span>
                            </h1>
                            <p class="text-lg text-surface-600 leading-relaxed mb-4">
                                {{ $t('manual.hero_desc') }}
                            </p>
                            <p class="text-sm text-surface-400">{{ $t('manual.hero_stats') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Quick Navigation -->
                <div class="bg-white rounded-2xl shadow-sm border border-surface-200 p-6 mb-12">
                    <h3 class="text-sm font-semibold text-surface-400 uppercase tracking-wider mb-4">{{ $t('manual.quick_nav') }}</h3>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="s in sections" :key="s.id" @click="activeSection = s.id; $nextTick(() => { const el = document.getElementById(s.id); if (el) { const y = el.getBoundingClientRect().top + window.scrollY - 100; window.scrollTo({ top: y, behavior: 'smooth' }); } })"
                            class="px-3 py-1.5 text-sm rounded-lg border transition-all duration-200"
                            :class="activeSection === s.id ? 'bg-brand-50 border-brand-200 text-brand-700 font-semibold' : 'bg-surface-50 border-surface-200 text-surface-600 hover:bg-brand-50 hover:text-brand-600'"
                        >{{ $t(s.titleKey) }}</button>
                    </div>
                </div>

                <!-- Workflow Overview -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-3 text-center">{{ $t('manual.workflow_title') }}</h2>
                    <p class="text-surface-500 text-center mb-10 max-w-2xl mx-auto">{{ $t('manual.workflow_desc') }}</p>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="w in workflow" :key="w.step" class="relative bg-white p-5 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-300 group">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center group-hover:bg-brand-100 transition-colors">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="w.icon"></path></svg>
                                </div>
                                <span class="text-xs font-bold text-brand-500 uppercase">{{ $t('manual.step_label', { step: w.step }) }}</span>
                            </div>
                            <h4 class="text-sm font-bold text-surface-900 mb-1">{{ $t(w.titleKey) }}</h4>
                            <p class="text-xs text-surface-500 leading-relaxed">{{ $t(w.descKey) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Roles -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-8 text-center">{{ $t('manual.roles_title') }}</h2>
                    <div class="grid md:grid-cols-3 gap-6">
                        <div v-for="r in roles" :key="r.nameKey" class="bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <h4 class="text-lg font-bold text-surface-900 mb-3">{{ $t(r.nameKey) }}</h4>
                            <p class="text-surface-600 text-sm leading-relaxed">{{ $t(r.descKey) }}</p>
                        </div>
                    </div>
                </div>

                <!-- Medal System -->
                <div class="bg-surface-900 rounded-3xl p-8 md:p-12 text-white relative overflow-hidden mb-16">
                    <div class="absolute top-0 left-0 w-full h-full bg-[radial-gradient(circle_at_30%_20%,#16a34a_0%,transparent_50%)] opacity-20"></div>
                    <div class="relative z-10 text-center max-w-2xl mx-auto">
                        <h2 class="text-3xl font-bold mb-4">{{ $t('manual.medals_title') }}</h2>
                        <p class="text-surface-300 mb-10">{{ $t('manual.medals_desc') }}</p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div v-for="m in medals" :key="m.nameKey" class="flex flex-col items-center">
                                <div :class="[m.bg, m.text, m.shadow]" class="w-16 h-16 rounded-full flex items-center justify-center text-2xl mb-3 shadow-lg">{{ m.icon }}</div>
                                <span class="font-bold text-lg">{{ $t(m.nameKey) }}</span>
                                <span class="text-xs text-surface-400 mt-1">{{ $t(m.descKey) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Answer Scale -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-8 text-center">{{ $t('manual.answer_scale_title') }}</h2>
                    <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div v-for="a in answerLevels" :key="a.labelKey" class="bg-white p-5 rounded-2xl border border-surface-200 shadow-sm">
                            <div :class="a.color" class="inline-block px-3 py-1 rounded-full text-sm font-semibold border mb-3">{{ $t(a.labelKey) }}</div>
                            <div class="text-2xl font-extrabold text-surface-900 mb-2">{{ a.value }}<span v-if="typeof a.value === 'number'" class="text-sm font-normal text-surface-400 ml-1">{{ $t('manual.pts') }}</span></div>
                            <p class="text-xs text-surface-500 leading-relaxed">{{ $t(a.descKey) }}</p>
                        </div>
                    </div>
                </div>

                <!-- 5 Dimensions -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-8 text-center">{{ $t('manual.dimensions_title') }}</h2>
                    <div class="grid gap-4">
                        <div v-for="d in dimensions" :key="d.num" class="flex flex-col md:flex-row gap-5 bg-white p-6 rounded-2xl border border-surface-200 shadow-sm hover:shadow-md transition-shadow">
                            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center font-bold text-lg">{{ d.num }}</div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-surface-900 mb-1">{{ $t(d.nameKey) }}</h4>
                                <p class="text-surface-600 text-sm">{{ $t(d.descKey) }}</p>
                            </div>
                            <div class="flex-shrink-0 hidden md:flex items-center">
                                <svg class="w-8 h-8 text-surface-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" :d="d.icon"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questionnaire Structure -->
                <div class="bg-white rounded-2xl border border-surface-200 shadow-sm p-6 md:p-8 mb-16">
                    <h2 class="text-2xl font-bold text-surface-900 mb-6">{{ $t('manual.questionnaire_title') }}</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        <div class="bg-surface-50 rounded-xl p-5 border border-surface-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold">I</div>
                                <div>
                                    <h4 class="font-bold text-surface-900">{{ $t('manual.section_i_title') }}</h4>
                                    <span class="text-xs text-surface-400">{{ $t('manual.section_i_stats') }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-surface-600">{{ $t('manual.section_i_desc') }}</p>
                        </div>
                        <div class="bg-surface-50 rounded-xl p-5 border border-surface-100">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-lg bg-brand-100 text-brand-700 flex items-center justify-center font-bold">II</div>
                                <div>
                                    <h4 class="font-bold text-surface-900">{{ $t('manual.section_ii_title') }}</h4>
                                    <span class="text-xs text-surface-400">{{ $t('manual.section_ii_stats') }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-surface-600">{{ $t('manual.section_ii_desc') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Detailed Sections (Accordion) -->
                <div class="mb-16">
                    <h2 class="text-3xl font-bold text-surface-900 mb-3 text-center">{{ $t('manual.detailed_title') }}</h2>
                    <p class="text-surface-500 text-center mb-10">{{ $t('manual.detailed_desc') }}</p>
                    <div class="space-y-3">
                        <div v-for="s in sections" :key="s.id" :id="s.id" class="bg-white rounded-2xl border border-surface-200 shadow-sm overflow-hidden transition-all duration-300" :class="activeSection === s.id ? 'ring-2 ring-brand-200' : ''">
                            <button @click="toggle(s.id)" class="w-full flex items-center gap-4 p-5 md:p-6 text-left hover:bg-surface-50 transition-colors">
                                <div class="flex-shrink-0 w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="s.icon"></path></svg>
                                </div>
                                <span class="flex-1 text-lg font-bold text-surface-900">{{ $t(s.titleKey) }}</span>
                                <svg class="w-5 h-5 text-surface-400 transition-transform duration-200" :class="activeSection === s.id ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div v-show="activeSection === s.id" class="px-5 md:px-6 pb-6 border-t border-surface-100">
                                <div class="pt-5 space-y-5">
                                    <div v-for="(c, ci) in s.content" :key="ci" class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1 w-6 h-6 rounded-full bg-brand-50 text-brand-600 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </div>
                                        <div>
                                            <h5 class="font-semibold text-surface-900 mb-1">{{ $t(c.subtitleKey) }}</h5>
                                            <p class="text-sm text-surface-600 leading-relaxed">{{ $t(c.textKey) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Best Practices Tip -->
                <div class="bg-gradient-to-r from-brand-50 to-green-50 rounded-2xl border border-brand-100 p-6 md:p-8 mb-16">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-brand-900 mb-2">{{ $t('manual.tip_title') }}</h4>
                            <p class="text-sm text-brand-800 leading-relaxed" v-html="$t('manual.tip_desc')"></p>
                        </div>
                    </div>
                </div>

                <!-- CTA -->
                <div class="text-center">
                    <h3 class="text-2xl font-bold text-surface-900 mb-6">{{ $t('manual.cta_title') }}</h3>
                    <div class="flex justify-center gap-4">
                        <Link v-if="!$page.props.auth.user" :href="route('register')">
                            <Button variant="primary" size="lg">{{ $t('manual.cta_start') }}</Button>
                        </Link>
                        <Link v-else :href="route('dashboard')">
                            <Button variant="primary" size="lg">{{ $t('manual.cta_dashboard') }}</Button>
                        </Link>
                        <Link :href="route('metodo.mitra')">
                            <Button variant="outline" size="lg">{{ $t('manual.cta_mitra') }}</Button>
                        </Link>
                    </div>
                </div>

            </div>
        </div>
    </component>
</template>
