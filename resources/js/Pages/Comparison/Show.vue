<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
    baseInspection: {
        type: Object,
        required: false,
    },
    otherInspection: {
        type: Object,
        required: false,
    },
    baseRound: {
        type: Object,
        required: false,
    },
    otherRound: {
        type: Object,
        required: false,
    },
    comparison: {
        type: Object,
        required: true,
    },
    isRoundComparison: {
        type: Boolean,
        default: false,
    }
});

const base = computed(() => props.isRoundComparison ? props.baseRound : props.baseInspection);
const other = computed(() => props.isRoundComparison ? props.otherRound : props.otherInspection);
const project = computed(() => base.value?.project);

const baseLabel = computed(() => props.isRoundComparison ? base.value?.name : `#${base.value?.sequential_id}`);
const otherLabel = computed(() => props.isRoundComparison ? other.value?.name : `#${other.value?.sequential_id}`);

const getDeltaColor = (delta) => {
    if (delta > 0) return 'text-green-600 bg-green-50 px-2 py-1 rounded font-bold';
    if (delta < 0) return 'text-red-600 bg-red-50 px-2 py-1 rounded font-bold';
    return 'text-surface-500 font-medium';
};

const formatDelta = (delta) => {
    if (delta > 0) return `+${delta}`;
    if (delta < 0) return `${delta}`;
    return '=';
};

// Extracted metrics for high-level overview
const totalBaseScore = props.comparison.sections.reduce((acc, curr) => acc + curr.baseline_score, 0);
const totalCompScore = props.comparison.sections.reduce((acc, curr) => acc + curr.comparison_score, 0);
const globalDelta = totalCompScore - totalBaseScore;
</script>

<template>
    <Head :title="`Comparação - ${baseLabel} vs ${otherLabel}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between w-full">
                <div>
                    <Breadcrumbs :items="[
                        { label: 'Workspace', url: route('projects.index') },
                        { label: project?.name, url: route('projects.show', project?.id) },
                        { label: 'Evolução e Comparação' }
                    ]" />
                    <h2 class="text-2xl font-semibold text-surface-900 tracking-tight mt-1">
                        Evolução / Comparação
                    </h2>
                    <p class="text-sm text-surface-500 mt-1">
                        Referência: {{ baseLabel }}
                        <span class="mx-2 text-surface-300">|</span>
                        Comparação: {{ otherLabel }}
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Global Metrics Comparison -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card class="text-center py-6">
                        <h4 class="text-sm text-surface-500 tracking-wider uppercase mb-2">{{ baseLabel }}</h4>
                        <div class="text-4xl font-bold text-surface-800">{{ totalBaseScore }}</div>
                    </Card>
                    
                    <Card class="text-center py-6 bg-brand-50 border-brand-100 flex flex-col justify-center items-center">
                        <h4 class="text-sm text-brand-700 tracking-wider uppercase mb-2">Variação Global</h4>
                        <div :class="['text-4xl', getDeltaColor(globalDelta)]">
                            {{ formatDelta(globalDelta) }}
                        </div>
                    </Card>

                    <Card class="text-center py-6">
                        <h4 class="text-sm text-surface-500 tracking-wider uppercase mb-2">{{ otherLabel }}</h4>
                        <div class="text-4xl font-bold text-surface-800">{{ totalCompScore }}</div>
                    </Card>
                </div>

                <!-- Detailed Breakdown -->
                <div class="space-y-8 mt-12">
                    <h3 class="text-xl font-semibold text-surface-900 pb-2 border-b border-surface-200">
                        Análise de Variâncias por Seção
                    </h3>

                    <div v-for="section in comparison.sections" :key="section.id" class="bg-white rounded-xl shadow-tactile border border-surface-100 overflow-hidden">
                        
                        <!-- Section Header -->
                        <div class="px-6 py-4 border-b border-surface-100 bg-surface-50 flex justify-between items-center">
                            <h4 class="font-medium text-lg text-surface-900">{{ section.name }}</h4>
                            <div class="flex items-center gap-6">
                                <div class="text-sm">
                                    <span class="text-surface-500 pr-1">Base:</span>
                                    <span class="font-bold text-surface-800">{{ section.baseline_score }}</span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-surface-500 pr-1">Atual:</span>
                                    <span class="font-bold text-surface-800">{{ section.comparison_score }}</span>
                                </div>
                                <div :class="['text-base w-16 text-center', getDeltaColor(section.delta)]">
                                    {{ formatDelta(section.delta) }}
                                </div>
                            </div>
                        </div>

                        <!-- Categories & Questions inside Section -->
                        <div class="p-6 space-y-6">
                            <div v-for="cat in section.categories" :key="cat.id" class="border border-surface-100 rounded-lg overflow-hidden">
                                
                                <!-- Category Summary -->
                                <div class="px-4 py-3 bg-surface-50/50 flex justify-between items-center border-b border-surface-100">
                                    <h5 class="text-surface-700 font-medium">{{ cat.name }}</h5>
                                    <div class="flex items-center gap-4 text-sm">
                                        <span class="text-surface-500">{{ cat.baseline_score }} → <span class="text-surface-900 font-semibold">{{ cat.comparison_score }}</span></span>
                                        <span :class="['w-10 text-right', getDeltaColor(cat.delta)]">{{ formatDelta(cat.delta) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Questions (Only showing if there is a delta to keep UI clean, or all) -->
                                <ul class="divide-y divide-surface-100 p-2">
                                    <template v-for="q in cat.questions" :key="q.question_id">
                                        <li v-if="q.delta !== 0" class="p-3 flex items-center justify-between text-sm bg-brand-50/30">
                                            <div class="truncate pr-4 max-w-2xl" :title="q.question_text">
                                                {{ q.question_text }}
                                            </div>
                                            <div class="flex items-center gap-4 shrink-0 font-medium">
                                                <span class="text-surface-500 w-16 text-right">{{ q.baseline_score }} pt</span>
                                                <span class="text-surface-300">→</span>
                                                <span class="text-surface-900 w-16 font-semibold">{{ q.comparison_score }} pt</span>
                                                <span :class="['w-12 text-center', getDeltaColor(q.delta)]">{{ formatDelta(q.delta) }}</span>
                                            </div>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
