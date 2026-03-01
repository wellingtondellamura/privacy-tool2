<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    tool: Object,
});

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
    <PublicLayout :title="tool.name + ' - Resumo de Privacidade'">
        <div class="py-12 bg-surface-50 min-h-screen">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
                
                <!-- Hero Section -->
                <div class="relative py-16 px-8 rounded-[40px] bg-surface-900 border border-surface-700 shadow-2xl overflow-hidden text-center">
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-900/40 to-transparent"></div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-brand-500 rounded-full blur-[120px] opacity-20 -mr-32 -mt-32"></div>
                    
                    <div class="relative z-10 space-y-8">
                        <div class="flex flex-col items-center">
                            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-500/10 border border-brand-500/20 text-brand-400 text-[10px] font-bold uppercase tracking-widest mb-6">
                                Relatório de Privacidade
                            </div>
                            <h1 class="text-4xl md:text-5xl font-black text-white uppercase tracking-tight leading-tight">{{ tool.name }}</h1>
                        </div>

                        <div class="flex flex-col md:flex-row justify-center items-center gap-12 md:gap-20">
                            <div class="text-center group">
                                <div class="text-7xl font-black text-white mb-2 group-hover:scale-110 transition-transform duration-500">{{ tool.score }}%</div>
                                <div class="text-surface-400 uppercase tracking-[0.2em] text-[10px] font-bold">Score Global</div>
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
                                <div class="text-surface-400 uppercase tracking-[0.2em] text-[10px] font-bold">Classificação Final</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="space-y-10">
                    <!-- Diagnosis -->
                    <div v-if="tool.diagnosis" class="space-y-4">
                        <h2 class="text-xl font-bold text-surface-900 flex items-center gap-2">
                             <svg class="w-5 h-5 text-brand-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                             Diagnóstico Geral
                        </h2>
                        <Card class="p-8 bg-white border-surface-200">
                            <div class="text-xl text-surface-700 leading-relaxed italic whitespace-pre-wrap font-medium">
                                "{{ tool.diagnosis }}"
                            </div>
                        </Card>
                    </div>

                    <!-- Sections Summary -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-4">
                            <h2 class="text-xl font-bold text-surface-900 tracking-tight whitespace-nowrap">Resumo por Dimensão</h2>
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
                            <h3 class="text-xl font-extrabold mb-2 tracking-tight uppercase">Deseja o relatório detalhado?</h3>
                            <p class="text-surface-400 text-sm max-w-sm">Esta visualização apresenta apenas os índices gerais e medalhas por seção conforme configurado pelo projeto.</p>
                        </div>
                        <Link :href="route('public.tools.index')" class="relative z-10 px-8 py-4 bg-white text-surface-900 font-black rounded-2xl hover:bg-brand-50 transition-all hover:shadow-brand active:scale-95 uppercase tracking-wider text-xs">
                            Voltar ao Diretório
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
