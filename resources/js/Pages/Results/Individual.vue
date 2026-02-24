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
</script>

<template>
    <Head :title="`Resultados - Inspeção #${inspection.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Breadcrumbs :items="[
                        { label: 'Workspace', url: route('projects.index') },
                        { label: inspection.project.name, url: route('projects.show', inspection.project.id) },
                        { label: 'Resultados Individuais' }
                    ]" />
                    <h2 class="text-2xl font-semibold text-surface-900 tracking-tight mt-1">
                        Resultados Individuais
                    </h2>
                    <p class="text-sm text-surface-500 mt-1">Inspeção #{{ inspection.id }} — {{ inspection.project.name }}</p>
                </div>
                
                <div v-if="inspection.status === 'closed'">
                    <Button variant="outline" @click="$inertia.get(route('results.team', inspection.id))">
                        Ver Resultado Consolidado (Equipe)
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Global Score -->
                <Card class="bg-gradient-to-br from-brand-50 to-white border-brand-100 text-center py-12">
                    <h3 class="text-surface-500 font-medium uppercase tracking-widest text-sm mb-4">Pontuação Global</h3>
                    <div class="text-6xl font-extrabold text-brand-700 tracking-tight">
                        {{ snapshot.global_score }}
                    </div>
                    <div class="mt-6 flex justify-center">
                        <div :class="['px-6 py-2 rounded-full border border-2 text-lg font-bold', getMedalColor(snapshot.medal.name)]">
                            Medalha: {{ snapshot.medal.name }}
                        </div>
                    </div>
                </Card>

                <!-- Sections -->
                <div class="space-y-6">
                    <h3 class="text-lg font-semibold text-surface-900 border-b border-surface-200 pb-2">Desempenho por Seção</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <Card v-for="section in snapshot.sections" :key="section.id" class="flex flex-col h-full">
                            <template #header>
                                <div class="flex justify-between items-center">
                                    <h4 class="font-medium text-surface-900 truncate pr-4" :title="section.name">{{ section.name }}</h4>
                                    <span class="text-xl font-bold text-brand-600">{{ section.score }}</span>
                                </div>
                            </template>
                            
                            <div class="space-y-4">
                                <div v-for="cat in section.categories" :key="cat.id" class="flex items-center justify-between text-sm">
                                    <span class="text-surface-600 truncate mr-4">{{ cat.name }}</span>
                                    <div class="flex items-center gap-3">
                                        <div class="w-24 bg-surface-200 rounded-full h-1.5 hidden sm:block">
                                            <div class="bg-brand-400 h-1.5 rounded-full" :style="`width: ${(cat.score / 100) * 100}%`"></div>
                                        </div>
                                        <span class="font-medium text-surface-900 w-8 text-right">{{ cat.score }}</span>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
