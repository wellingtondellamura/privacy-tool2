<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

const props = defineProps({
    inspection: {
        type: Object,
        required: true,
    },
    snapshot: {
        type: Object,
        required: true,
    }
});

const { t } = useI18n();

const getMedalColor = (medalName) => {
    const colors = {
        'gold': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'silver': 'bg-gray-100 text-gray-800 border-gray-300',
        'bronze': 'bg-orange-100 text-orange-800 border-orange-300',
        'incipient': 'bg-surface-100 text-surface-800 border-surface-300',
    };
    return colors[medalName] || colors['incipient'];
};

const getDivergenceColor = (classification) => {
    const colors = {
        'high': 'text-red-600 bg-red-100',
        'medium': 'text-yellow-700 bg-yellow-100',
        'low': 'text-green-700 bg-green-100',
    };
    return colors[classification] || colors['low'];
};

const formatDivergence = (classification) => {
    const map = {
        'high': t('results.divergence_high'),
        'medium': t('results.divergence_moderate'),
        'low': t('results.divergence_consensus'),
    };
    return map[classification] || t('results.divergence_consensus');
};

const toRoman = (num) => {
    if (isNaN(num)) return '';
    const map = { M: 1000, CM: 900, D: 500, CD: 400, C: 100, XC: 90, L: 50, XL: 40, X: 10, IX: 9, V: 5, IV: 4, I: 1 };
    let result = '';
    for (let key in map) {
        while (num >= map[key]) {
            result += key;
            num -= map[key];
        }
    }
    return result;
};

const toAlpha = (index) => String.fromCharCode(65 + index);

const getMedalImage = (medalName) => {
    const images = {
        'gold': '/images/badges-gold.png',
        'silver': '/images/badges-silver.png',
        'bronze': '/images/badges-bronze.png',
    };
    return images[medalName] || null;
};

</script>

<template>
    <Head :title="t('results.team_title') + ' - ' + t('project.inspection_label', { id: inspection.sequential_id })" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Breadcrumbs :items="[
                        { label: $t('nav.workspace'), url: route('projects.index') },
                        { label: inspection.project.name, url: route('projects.show', inspection.project.id) },
                        { label: inspection.evaluation_round.name, url: route('rounds.show', inspection.evaluation_round.id) },
                        { label: $t('results.team_title') }
                    ]" />
                    <h2 class="text-2xl font-semibold text-surface-900 tracking-tight mt-1">
                        {{ $t('results.team_title') }}
                    </h2>
                    <p class="text-sm text-surface-500 mt-1">{{ $t('results.team_subtitle', { id: inspection.sequential_id, round: inspection.evaluation_round.name }) }}</p>
                </div>
                
                <div class="flex items-center gap-2">
                    <Button variant="outline" @click="$inertia.get(route('rounds.show', inspection.evaluation_round.id))">
                        {{ $t('results.back_to_round') }}
                    </Button>
                    <Button variant="outline" @click="$inertia.get(route('results.individual', inspection.id))">
                        {{ $t('results.view_individual') }}
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Global Score -->
                <Card class="bg-gradient-to-br from-surface-50 to-white border-surface-200 py-10 shadow-sm relative overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-300 to-brand-600"></div>
                    <div class="flex items-center justify-center gap-12">
                        <!-- Medal on the left -->
                        <img v-if="snapshot.medal && getMedalImage(snapshot.medal.name)" 
                             :src="getMedalImage(snapshot.medal.name)" 
                             class="w-48 h-48 object-contain drop-shadow-lg" 
                             :alt="snapshot.medal.name" />
                        
                        <!-- Score column on the right -->
                        <div class="flex flex-col items-center">
                            <div class="text-[100px] font-bold text-surface-800 leading-none">
                                {{ snapshot.global_score }}
                            </div>
                            <div class="mt-4 text-xs font-bold text-surface-400 uppercase tracking-[0.2em]">
                                {{ $t('results.global_avg_score') }}
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Sections & Divergence Indicator -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-surface-900 border-b border-surface-200 pb-2">{{ $t('results.section_avg_performance') }}</h3>
                    
                    <div class="space-y-8">
                        <section v-for="(section, sIndex) in snapshot.sections" :key="section.id" class="bg-white rounded-xl shadow-tactile border border-surface-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                                <h4 class="font-medium text-lg text-surface-900">{{ toRoman(sIndex + 1) }}. {{ section.name }}</h4>
                                <span class="text-xl font-bold text-brand-600">{{ section.score }}</span>
                            </div>

                            <div class="p-6 space-y-8">
                                <div v-for="(cat, cIndex) in section.categories" :key="cat.id">
                                    <div class="flex justify-between items-center mb-4">
                                        <h5 class="text-surface-700 font-medium">{{ toAlpha(cIndex) }}. {{ cat.name }}</h5>
                                        <span class="text-lg font-bold text-surface-900">{{ cat.score }}</span>
                                    </div>
                                    
                                    <!-- Questions and Divergences (If present in snapshot) -->
                                    <ul v-if="cat.questions" class="space-y-3 pl-4 border-l-2 border-surface-100">
                                        <li v-for="q in cat.questions" :key="q.question_id" class="flex flex-col sm:flex-row sm:items-center justify-between bg-surface-50 rounded p-3 gap-3">
                                            <span class="text-sm text-surface-700 truncate max-w-lg" :title="q.question_text">
                                                {{ q.question_text }}
                                            </span>
                                            
                                            <div class="flex items-center gap-4 shrink-0 justify-end">
                                                <div class="text-sm font-semibold text-surface-900 w-12 text-right">
                                                    Score: {{ q.score }}
                                                </div>
                                                <Badge v-if="q.classification" :class="[getDivergenceColor(q.classification), 'w-36 justify-center']">
                                                    {{ formatDivergence(q.classification) }} (Var: {{ q.variance }})
                                                </Badge>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer with Project Info -->
        <footer class="mt-12 bg-surface-900 text-surface-300 py-12 shrink-0 border-t border-surface-800">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-2 gap-12 items-start mb-12">
                    <!-- Project Column -->
                    <div>
                        <h4 class="text-white font-bold text-lg mb-6 flex items-center gap-2">
                            <span class="w-2 h-6 bg-brand-500 rounded-full"></span>
                            {{ $t('results.footer.project_info') }}
                        </h4>
                        <div class="space-y-4">
                            <div>
                                <span class="block text-xs uppercase tracking-wider text-surface-500 font-bold mb-1">{{ $t('results.footer.project_name') }}</span>
                                <p class="text-surface-200 font-medium">{{ inspection.project.name }}</p>
                            </div>
                            <div v-if="inspection.project.website_url">
                                <span class="block text-xs uppercase tracking-wider text-surface-500 font-bold mb-1">{{ $t('results.footer.website') }}</span>
                                <a :href="inspection.project.website_url" target="_blank" class="text-brand-400 hover:text-brand-300 transition-colors underline decoration-brand-400/30">
                                    {{ inspection.project.website_url }}
                                </a>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <span class="block text-xs uppercase tracking-wider text-surface-500 font-bold mb-1">{{ $t('results.footer.inspection_date') }}</span>
                                    <p class="text-surface-200">{{ new Date(inspection.created_at).toLocaleDateString(t('common.locale_code')) }}</p>
                                </div>
                                <div>
                                    <span class="block text-xs uppercase tracking-wider text-surface-500 font-bold mb-1">{{ $t('results.footer.sequential_id') }}</span>
                                    <p class="text-surface-200">#{{ inspection.sequential_id }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- References Column -->
                    <div class="flex flex-col md:items-end gap-3 text-sm h-full justify-between">
                        <div class="w-full md:w-auto">
                            <span class="font-semibold text-white mb-4 block md:text-right">{{ $t('results.footer.references') }}</span>
                            <div class="space-y-3">
                                <a href="https://each.usp.br/cond_met_pand/trmodel/" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2 md:justify-end group">
                                    <span class="group-hover:translate-x-[-4px] transition-transform">{{ $t('results.footer.trmodel_link') }}</span>
                                    <svg class="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                                <a href="https://www.gov.br/esporte/pt-br/acesso-a-informacao/lgpd" target="_blank" rel="noopener noreferrer" class="hover:text-white transition-colors flex items-center gap-2 md:justify-end group">
                                    <span class="group-hover:translate-x-[-4px] transition-transform">{{ $t('results.footer.lgpd_link') }}</span>
                                    <svg class="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>

                        <p class="text-xs text-surface-500 mt-auto md:text-right leading-relaxed max-w-sm">
                            {{ $t('results.footer.description') }}
                        </p>
                    </div>
                </div>

                <hr class="border-surface-800 mb-8" />

                <div class="flex flex-col sm:flex-row justify-between items-center text-xs text-surface-500">
                    <div class="flex items-center gap-3 opacity-50 mb-4 sm:mb-0">
                        <ApplicationLogo class="h-4 w-auto fill-current" />
                        <span>&copy; {{ new Date().getFullYear() }} Privacy Tool.</span>
                    </div>
                    <p>{{ $t('results.footer.developed_with') }}</p>
                </div>
            </div>
        </footer>
    </AuthenticatedLayout>
</template>
