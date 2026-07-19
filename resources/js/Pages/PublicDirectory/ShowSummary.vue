<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import ProvenanceBadge from '@/Components/ProvenanceBadge.vue';

const props = defineProps({
    tool: Object,
});

const { t, locale } = useI18n();

const getMedalVariant = (medal) => {
    if (!medal) return 'neutral';
    const name = (medal.name || medal).toLowerCase();
    if (name.includes('ouro') || name.includes('gold')) return 'warning';
    if (name.includes('prata') || name.includes('silver')) return 'neutral';
    if (name.includes('bronze')) return 'primary'; // Bug 5 fix: distinct from gold (warning)
    if (name.includes('incipiente') || name.includes('incipient')) return 'error';
    return 'primary';
};

const getMedalImage = (medal) => {
    if (!medal) return null;
    const name = (typeof medal === 'string' ? medal : (medal.name || '')).toLowerCase();
    const loc = locale.value;
    if (name.includes('ouro') || name.includes('gold')) return `/images/badges-gold_${loc}.png`;
    if (name.includes('prata') || name.includes('silver')) return `/images/badges-silver_${loc}.png`;
    if (name.includes('bronze')) return `/images/badges-bronze_${loc}.png`;
    
    return null;
};

const getConsensusModelLabel = (model) => {
    switch(model) {
        case 'owner_decides': return t('models.owner_decides', 'Decisão do Coordenador');
        case 'evaluator_convergence': return t('models.evaluator_convergence', 'Convergência de Avaliadores');
        case 'majority_vote': return t('models.majority_vote', 'Voto da Maioria');
        default: return model;
    }
};
</script>

<template>
    <PublicLayout :title="t('directory.summary_page_title', { name: tool.name })">
        <div class="py-12 bg-surface-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                <!-- Hero Section -->
                <div class="relative py-16 px-8 rounded-[40px] bg-surface-900 border border-surface-700 shadow-2xl overflow-hidden text-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-900/40 to-transparent"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500 rounded-full blur-[120px] opacity-20 -mr-32 -mt-32"></div>
                    
                    <div class="relative z-10 space-y-8">
                        <div class="flex flex-col items-center">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-200 text-[10px] font-bold uppercase tracking-widest mb-6">
                                {{ $t('directory.summary_badge') }}
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tight leading-tight">{{ tool.name }}</h1>
                        </div>

                        <div class="flex flex-col md:flex-row justify-center items-center gap-12 md:gap-20">
                            <div class="text-center group">
                                <div class="text-7xl font-black text-white mb-2 group-hover:scale-110 transition-transform duration-500">{{ tool.score }}%</div>
                                <div class="text-surface-300 uppercase tracking-[0.2em] text-[10px] font-bold">{{ $t('directory.summary_score') }}</div>
                            </div>
                            
                            <div class="h-16 w-px bg-surface-700 hidden md:block"></div>
                            
                            <div class="text-center" v-if="tool.medal">
                                <div class="mb-4 flex flex-col items-center">
                                    <img v-if="getMedalImage(tool.medal)" 
                                         :src="getMedalImage(tool.medal)" 
                                         class="w-32 h-32 object-contain drop-shadow-2xl mb-4 transform hover:scale-110 transition-transform duration-500" 
                                         :alt="tool.medal.name || tool.medal" />
                                         
                                    <Badge :variant="getMedalVariant(tool.medal)" size="lg" class="px-8 py-2 text-sm uppercase tracking-widest shadow-lg">
                                        {{ tool.medal.name || tool.medal }}
                                    </Badge>
                                </div>
                                <div class="text-surface-300 uppercase tracking-[0.2em] text-[10px] font-bold">{{ $t('directory.summary_classification') }}</div>
                            </div>
                        </div>

                        <!-- Summary Info Pills -->
                        <div class="pt-6 border-t border-surface-700/50 flex flex-wrap justify-center items-center gap-4 text-xs font-medium text-surface-300">
                            <!-- Evaluators -->
                            <div class="bg-surface-800/80 px-4 py-2 rounded-full border border-surface-700/80 flex items-center gap-2 shadow-inner">
                                <svg class="w-4 h-4 text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                {{ tool.user_count_total }} {{ tool.user_count_total === 1 ? $t('directory.evaluator_singular', 'Avaliador') : $t('directory.evaluator_plural', 'Avaliadores') }}
                            </div>
                            
                            <!-- Internal/External -->
                            <div class="bg-surface-800/80 px-4 py-2 rounded-full border border-surface-700/80 flex items-center gap-2 shadow-inner">
                                <svg v-if="tool.is_self_assessment" class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <svg v-else class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                {{ tool.is_self_assessment ? $t('directory.provenance_self', 'Autoavaliação') : $t('directory.provenance_external', 'Auditoria Externa') }}
                            </div>
                            
                            <!-- Consensus Model -->
                            <a href="https://mitra.ufca.edu.br" target="_blank" class="bg-surface-800/80 px-4 py-2 rounded-full border border-surface-700/80 flex items-center gap-2 shadow-inner hover:bg-surface-700 hover:text-white transition-colors group">
                                <svg class="w-4 h-4 text-brand-400 group-hover:text-brand-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                                {{ getConsensusModelLabel(tool.consensus_model) }}
                                <svg class="w-3 h-3 opacity-50 group-hover:opacity-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="space-y-10">
                    <!-- Diagnosis & Provenance Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div v-if="tool.diagnosis" class="md:col-span-2 space-y-4 flex flex-col">
                            <h2 class="text-xl font-bold text-surface-900 flex items-center gap-2">
                                 <svg class="w-5 h-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                 {{ $t('directory.summary_diagnosis') }}
                            </h2>
                            <Card class="p-8 bg-white border-surface-200 flex-1 flex items-center">
                                <div class="text-xl text-surface-700 leading-relaxed italic whitespace-pre-wrap font-medium">
                                    "{{ tool.diagnosis }}"
                                </div>
                            </Card>
                        </div>
                        
                        <div :class="tool.diagnosis ? 'md:col-span-1' : 'md:col-span-3'" class="space-y-4">
                            <h2 class="text-xl font-bold text-surface-900 flex items-center gap-2">
                                 <svg class="w-5 h-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                 {{ $t('directory.provenance_title') }}
                            </h2>
                            <ProvenanceBadge
                                :is-self-assessment="tool.is_self_assessment"
                                :software-version="tool.software_version"
                                :user-count="tool.user_count_total"
                                :inspection-count="tool.inspection_count"
                                :inspection-date="tool.inspection_date"
                                class="h-full"
                            />
                        </div>
                    </div>

                    <!-- Sections Summary -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xl font-bold text-surface-900 tracking-tight whitespace-nowrap">{{ $t('directory.summary_dimensions') }}</h2>
                            <div class="h-px bg-surface-200 w-full"></div>
                        </div>

                        <div class="grid gap-4">
                            <div v-for="section in tool.sections" :key="section.name" class="group">
                                <div class="flex items-center justify-between p-6 bg-white rounded-2xl border border-surface-200 shadow-sm hover:shadow-tactile hover:-translate-y-1 transition-all duration-smooth">
                                    <span class="font-bold text-surface-800 text-lg group-hover:text-brand-700 transition-colors">{{ section.name }}</span>
                                    <div class="flex items-center gap-6">
                                        <Badge v-if="section.medal" :variant="getMedalVariant(section.medal)" size="sm" class="hidden sm:inline-flex">
                                            {{ section.medal }}
                                        </Badge>
                                        <div class="flex flex-col items-end">
                                            <span class="text-[8px] font-bold text-surface-400 uppercase leading-none mb-0.5">Score</span>
                                            <span class="text-2xl font-black text-brand-600 leading-none">{{ section.score }}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer CTA -->
                    <div class="bg-surface-900 rounded-[32px] p-10 text-white flex flex-col md:flex-row justify-between items-center gap-8 relative overflow-hidden group shadow-xl">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-700"></div>
                        <div class="relative z-10 text-center md:text-left">
                            <h3 class="text-xl font-extrabold mb-2 tracking-tight uppercase">{{ $t('directory.summary_cta_title') }}</h3>
                            <p class="text-surface-400 text-sm max-w-sm">{{ $t('directory.summary_cta_description') }}</p>
                        </div>
                        <Link :href="route('public.tools.index')" class="relative z-10 px-8 py-4 bg-white text-surface-900 font-black rounded-2xl hover:bg-brand-50 transition-all hover:shadow-brand active:scale-95 uppercase tracking-wider text-xs">
                            {{ $t('directory.summary_back') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
