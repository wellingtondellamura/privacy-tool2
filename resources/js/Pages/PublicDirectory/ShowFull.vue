<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    tool: Object,
});

const { t } = useI18n();

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

const getClassificationColor = (score) => {
    if (score >= 90) return 'text-success-600 bg-success-50 border-success-100';
    if (score >= 70) return 'text-brand-600 bg-brand-50 border-brand-100';
    if (score >= 50) return 'text-warning-600 bg-warning-50 border-warning-100';
    return 'text-error-600 bg-error-50 border-error-100';
};

const getMedalVariant = (medal) => {
    if (!medal) return 'neutral';
    const name = (medal.name || medal).toLowerCase();
    if (name.includes('ouro') || name.includes('gold')) return 'warning';
    if (name.includes('prata') || name.includes('silver')) return 'neutral';
    if (name.includes('bronze')) return 'warning';
    if (name.includes('incipiente')) return 'error';
    return 'primary';
};

const getMedalImage = (medal) => {
    if (!medal) return null;
    const name = (typeof medal === 'string' ? medal : (medal.name || '')).toLowerCase();
    
    if (name.includes('ouro') || name.includes('gold')) return '/images/badges-gold.png';
    if (name.includes('prata') || name.includes('silver')) return '/images/badges-silver.png';
    if (name.includes('bronze')) return '/images/badges-bronze.png';
    
    return null;
};
</script>

<template>
    <PublicLayout :title="t('directory.full_page_title', { name: tool.name })">
        <div class="py-12 bg-surface-50 min-h-screen">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
                
                <!-- Back Link & Header -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="flex items-center gap-4">
                        <Link :href="route('public.tools.index')" class="w-10 h-10 rounded-full bg-white shadow-sm border border-surface-200 flex items-center justify-center text-surface-400 hover:text-brand-600 hover:border-brand-300 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        </Link>
                        <div>
                            <h1 class="text-3xl font-extrabold text-surface-900 tracking-tight">{{ tool.name }}</h1>
                            <p class="text-xs text-surface-500 font-medium uppercase tracking-widest mt-1">
                                <a :href="tool.url" target="_blank" class="hover:text-brand-600 transition-colors underline underline-offset-2 capitalize">{{ tool.url }}</a> 
                                • {{ $t('directory.full_inspection_date', { date: tool.inspection_date }) }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 bg-white p-2 pl-6 rounded-2xl shadow-sm border border-surface-200">
                        <div class="text-right">
                            <p class="text-[10px] font-bold text-surface-400 uppercase tracking-tighter">{{ $t('directory.full_consolidated_score') }}</p>
                            <p class="text-2xl font-black text-surface-900 leading-none">{{ tool.report.global_score }}%</p>
                        </div>

                    </div>
                </div>

                <!-- Medal & Summary -->
                <div class="grid md:grid-cols-3 gap-6">
                    <Card class="md:col-span-1 p-8 flex flex-col items-center justify-center text-center space-y-4">
                        <div class="text-[10px] font-bold text-surface-400 uppercase tracking-widest">{{ $t('directory.full_classification') }}</div>
                        <div v-if="tool.report.medal" class="flex flex-col items-center gap-2 font-medium">
                            <img v-if="getMedalImage(tool.report.medal)" 
                                 :src="getMedalImage(tool.report.medal)" 
                                 class="w-32 h-32 object-contain drop-shadow-md mb-2" 
                                 :alt="tool.report.medal.name || tool.report.medal" />
                            
                             <Badge :variant="getMedalVariant(tool.report.medal)" size="lg" class="px-6 py-2 text-sm uppercase tracking-widest shadow-sm">
                                {{ tool.report.medal.name || tool.report.medal }}
                            </Badge>
                            <p class="text-xs text-surface-400 font-medium mt-2 italic">{{ $t('directory.full_achieved_through', { count: tool.report.inspection_count }) }}</p>
                        </div>
                        <div v-else class="text-surface-400 italic text-sm">{{ $t('directory.full_no_medal') }}</div>
                    </Card>

                    <Card v-if="tool.report.diagnosis" class="md:col-span-2 p-8 relative overflow-hidden flex flex-col justify-center">
                        <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-full -mr-16 -mt-16 opacity-50"></div>
                        <h2 class="text-xs font-bold text-brand-600 uppercase tracking-widest mb-4 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                            {{ $t('directory.full_conclusion') }}
                        </h2>
                        <div class="text-lg text-surface-700 leading-relaxed italic whitespace-pre-wrap font-medium relative z-10">
                            "{{ tool.report.diagnosis }}"
                        </div>
                    </Card>
                </div>

                <!-- Sections Detail -->
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <h2 class="text-2xl font-bold text-surface-900 tracking-tight whitespace-nowrap">{{ $t('directory.full_dimension_detail') }}</h2>
                        <div class="h-px bg-surface-200 w-full"></div>
                    </div>
                    
                    <div v-for="(section, sIndex) in tool.report.sections" :key="section.id" class="group">
                        <div class="bg-white rounded-3xl border border-surface-200 overflow-hidden shadow-sm hover:shadow-tactile transition-all duration-smooth">
                            <div class="px-8 py-5 bg-surface-50/80 border-b border-surface-200 flex justify-between items-center group-hover:bg-surface-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-brand-600 font-black text-xl tracking-tighter">{{ toRoman(sIndex + 1) }}.</span>
                                    <h3 class="font-bold text-surface-900 text-lg uppercase tracking-tight">{{ section.name }}</h3>
                                </div>
                                <div class="flex items-center gap-4">
                                    <Badge v-if="section.medal" :variant="getMedalVariant(section.medal)" size="sm" class="hidden sm:block">
                                        {{ section.medal }}
                                    </Badge>
                                    <div class="flex flex-col items-end">
                                        <span class="text-[8px] font-bold text-surface-400 uppercase leading-none mb-0.5">Score</span>
                                        <span class="text-2xl font-black text-brand-600 leading-none">{{ section.score }}%</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="divide-y divide-surface-100">
                                <div v-for="category in section.categories" :key="category.id" class="p-8">
                                    <div class="flex justify-between items-center mb-6">
                                        <h4 class="font-bold text-surface-800 flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full bg-brand-500"></span>
                                            {{ category.name }}
                                        </h4>
                                        <span class="px-3 py-1 bg-surface-50 rounded-lg border border-surface-100 text-sm font-black text-surface-600">{{ category.score }}%</span>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div v-for="question in category.questions" :key="question.question_id" class="p-4 bg-surface-50/50 rounded-2xl border border-surface-100 flex justify-between items-center gap-6 group/item hover:bg-white hover:border-brand-200 transition-all shadow-sm shadow-black/[0.02]">
                                            <span class="text-xs text-surface-700 leading-tight font-medium">{{ question.question_text }}</span>
                                            <span class="shrink-0 px-3 py-1 rounded-full text-[10px] font-bold border shadow-sm" :class="getClassificationColor(question.score)">
                                                {{ question.score }}%
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer CTA -->
                <div class="text-center pt-8 border-t border-surface-200">
                    <Link :href="route('public.tools.index')" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-white border border-surface-200 text-surface-600 font-bold hover:text-brand-600 hover:border-brand-300 hover:shadow-tactile transition-all group">
                        <svg class="w-4 h-4 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        {{ $t('directory.full_back') }}
                    </Link>
                </div>

            </div>
        </div>
    </PublicLayout>
</template>
