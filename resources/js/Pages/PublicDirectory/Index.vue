<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import ProjectIcon from '@/Components/ProjectIcon.vue';

const props = defineProps({
    tools: Object,
    filters: Object,
});

const filters = ref({
    medal: props.filters.medal || '',
    year: props.filters.year || '',
    sort: props.filters.sort || 'score_desc',
});

watch(filters, (newFilters) => {
    router.get(route('public.tools.index'), newFilters, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
}, { deep: true });

const { t } = useI18n();

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
    <PublicLayout :title="t('directory.page_title')">
        <div class="py-12 bg-surface-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-100 text-brand-700 text-[10px] font-bold uppercase tracking-wider mb-4">
                            {{ $t('directory.badge') }}
                        </div>
                        <h1 class="text-4xl font-extrabold text-surface-900 tracking-tight">{{ $t('directory.title') }}</h1>
                        <p class="mt-3 text-lg text-surface-500 max-w-2xl">{{ $t('directory.description') }}</p>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap items-center gap-3">
                        <select 
                            v-model="filters.medal" 
                            class="rounded-lg border-surface-200 text-sm focus:ring-brand-500 focus:border-brand-500 bg-white"
                        >
                            <option value="">{{ $t('directory.all_medals') }}</option>
                            <option value="Ouro">{{ $t('directory.medal_gold') }}</option>
                            <option value="Prata">{{ $t('directory.medal_silver') }}</option>
                            <option value="Bronze">{{ $t('directory.medal_bronze') }}</option>
                            <option value="Incipiente">{{ $t('directory.medal_incipient') }}</option>
                        </select>

                        <select 
                            v-model="filters.year" 
                            class="rounded-lg border-surface-200 text-sm focus:ring-brand-500 focus:border-brand-500 bg-white"
                        >
                            <option value="">{{ $t('directory.all_years') }}</option>
                            <option v-for="year in [2026, 2027, 2028, 2029, 2030]" :key="year" :value="year">{{ year }}</option>
                        </select>

                        <select 
                            v-model="filters.sort" 
                            class="rounded-lg border-surface-200 text-sm focus:ring-brand-500 focus:border-brand-500 bg-white"
                        >
                            <option value="score_desc">{{ $t('directory.sort_best') }}</option>
                            <option value="score_asc">{{ $t('directory.sort_worst') }}</option>
                            <option value="date_desc">{{ $t('directory.sort_recent') }}</option>
                        </select>
                    </div>
                </header>

                <!-- Tools Grid -->
                <div v-if="tools.data.length > 0" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Link 
                        v-for="tool in tools.data" 
                        :key="tool.slug" 
                        :href="route('public.tools.show', tool.slug)"
                        class="group block"
                    >
                        <Card class="h-full flex flex-col transition-all duration-smooth group-hover:shadow-tactile hover:-translate-y-1">
                            <div class="p-6 flex-grow">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-xl bg-surface-50 flex items-center justify-center border border-surface-100 group-hover:scale-110 transition-transform duration-smooth shadow-sm">
                                            <svg class="w-6 h-6 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-xl font-bold text-surface-900 line-clamp-1 decoration-brand-500 group-hover:underline decoration-2 underline-offset-4">{{ tool.project_name }}</h2>
                                            <p class="text-xs text-surface-400 mt-1 font-medium">{{ $t('directory.published_at', { date: tool.published_at }) }}</p>
                                        </div>
                                    </div>
                                    <div class="flex flex-col items-end gap-1.5 whitespace-nowrap">
                                        <img v-if="getMedalImage(tool.medal)" 
                                             :src="getMedalImage(tool.medal)" 
                                             class="w-10 h-10 object-contain drop-shadow-sm bg-white/50 rounded-full p-0.5" 
                                             :alt="tool.medal.name || tool.medal" />
                                        <Badge v-if="tool.medal" :variant="getMedalVariant(tool.medal)" size="sm">
                                            {{ tool.medal.name || tool.medal }}
                                        </Badge>
                                    </div>
                                </div>

                                <div class="relative pt-2">
                                    <p class="text-[10px] font-bold text-brand-600 uppercase tracking-tight mb-2">{{ tool.round_name }}</p>
                                    <div class="flex justify-between items-end mb-2">
                                        <span class="text-xs font-bold text-surface-400 uppercase tracking-tighter">{{ $t('directory.global_score') }}</span>
                                        <span class="text-3xl font-black text-surface-900">{{ tool.score }}%</span>
                                    </div>
                                    <div class="w-full bg-surface-100 h-2.5 rounded-full overflow-hidden border border-surface-200/50 p-0.5">
                                        <div 
                                            class="h-full rounded-full transition-all duration-1000 bg-gradient-to-r from-brand-400 to-brand-600"
                                            :style="{ width: `${tool.score}%` }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="px-6 py-4 bg-surface-50/50 border-t border-surface-100 flex items-center justify-between group-hover:bg-brand-50 transition-colors">
                                <span class="text-sm font-medium text-surface-600 group-hover:text-brand-700">{{ $t('directory.view_details') }}</span>
                                <svg class="w-4 h-4 text-surface-400 group-hover:text-brand-600 transform group-hover:translate-x-1 transition-all" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </div>
                        </Card>
                    </Link>
                </div>

                <!-- Empty State -->
                <Card v-else class="py-20 flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-surface-50 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-surface-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-surface-900">{{ $t('directory.no_results') }}</h3>
                    <p class="text-surface-500 mt-2">{{ $t('directory.no_results_description') }}</p>
                    <button 
                        @click="filters = { medal: '', year: '', sort: 'score_desc' }"
                        class="mt-6 text-brand-600 font-bold hover:underline"
                    >
                        {{ $t('directory.clear_filters') }}
                    </button>
                </Card>

                <!-- Pagination -->
                <div class="mt-12 flex justify-center" v-if="tools.links && tools.links.length > 3">
                    <nav class="flex items-center gap-2">
                        <Link 
                            v-for="(link, idx) in tools.links" 
                            :key="idx"
                            :href="link.url || '#'"
                            class="px-4 py-2 rounded-lg text-sm font-bold transition-all border"
                            :class="[
                                link.active 
                                    ? 'bg-brand-600 text-white border-brand-600 shadow-brand' 
                                    : 'bg-white text-surface-600 border-surface-200 hover:border-brand-300 hover:bg-surface-50',
                                !link.url ? 'opacity-50 cursor-not-allowed hidden sm:flex' : ''
                            ]"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
