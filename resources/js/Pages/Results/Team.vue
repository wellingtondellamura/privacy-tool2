<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

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

const getMedalColor = (medalName) => {
    const colors = {
        'Ouro': 'bg-yellow-100 text-yellow-800 border-yellow-300',
        'Prata': 'bg-gray-100 text-gray-800 border-gray-300',
        'Bronze': 'bg-orange-100 text-orange-800 border-orange-300',
        'Sem Medalha': 'bg-surface-100 text-surface-800 border-surface-300',
    };
    return colors[medalName] || colors['Sem Medalha'];
};

const getDivergenceColor = (classification) => {
    const colors = {
        'alta': 'text-red-600 bg-red-100',
        'moderada': 'text-yellow-700 bg-yellow-100',
        'baixa': 'text-green-700 bg-green-100',
    };
    return colors[classification] || colors['baixa'];
};

const formatDivergence = (classification) => {
    const map = {
        'alta': 'Divergência Alta',
        'moderada': 'Divergência Moderada',
        'baixa': 'Consenso',
    };
    return map[classification] || 'Consenso';
};
</script>

<template>
    <Head :title="`Resultado Consolidado - Inspeção #${inspection.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Breadcrumbs :items="[
                        { label: 'Workspace', url: route('projects.index') },
                        { label: inspection.project.name, url: route('projects.show', inspection.project.id) },
                        { label: 'Resultado Consolidado da Equipe' }
                    ]" />
                    <h2 class="text-2xl font-semibold text-surface-900 tracking-tight mt-1">
                        Resultado Consolidado da Equipe
                    </h2>
                    <p class="text-sm text-surface-500 mt-1">Inspeção #{{ inspection.id }} — {{ inspection.project.name }}</p>
                </div>
                
                <div>
                    <Button variant="outline" @click="$inertia.get(route('results.individual', inspection.id))">
                        Ver Meu Resultado (Individual)
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Global Score -->
                <Card class="bg-gradient-to-br from-surface-50 to-white border-surface-200 text-center py-10 shadow-sm relative overflow-hidden">
                    <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-brand-300 to-brand-600"></div>
                    <h3 class="text-surface-500 font-medium uppercase tracking-widest text-sm mb-4">Pontuação Média Global</h3>
                    <div class="text-6xl font-extrabold text-surface-800 tracking-tight">
                        {{ snapshot.global_score }}
                    </div>
                    <div class="mt-6 flex justify-center">
                        <div :class="['px-6 py-2 rounded-full border border-2 text-lg font-bold shadow-sm', getMedalColor(snapshot.medal.name)]">
                            Medalha: {{ snapshot.medal.name }}
                        </div>
                    </div>
                </Card>

                <!-- Sections & Divergence Indicator -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-surface-900 border-b border-surface-200 pb-2">Desempenho Médio por Seção</h3>
                    
                    <div class="space-y-8">
                        <section v-for="section in snapshot.sections" :key="section.id" class="bg-white rounded-xl shadow-tactile border border-surface-100 overflow-hidden">
                            <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                                <h4 class="font-medium text-lg text-surface-900">{{ section.name }}</h4>
                                <span class="text-xl font-bold text-brand-600">{{ section.score }}</span>
                            </div>

                            <div class="p-6 space-y-8">
                                <div v-for="cat in section.categories" :key="cat.id">
                                    <div class="flex justify-between items-center mb-4">
                                        <h5 class="text-surface-700 font-medium">{{ cat.name }}</h5>
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
    </AuthenticatedLayout>
</template>
