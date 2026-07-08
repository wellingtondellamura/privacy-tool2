<script setup>
import { ref, computed, onMounted } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import DivergenceBadge from '@/Components/DivergenceBadge.vue';
import ConsensusBadge from '@/Components/ConsensusBadge.vue';
import ConflictThread from '@/Components/ConflictThread.vue';
import OwnerDecidePanel from '@/Components/OwnerDecidePanel.vue';
import MajorityResult from '@/Components/MajorityResult.vue';

const props = defineProps({
    round: {
        type: Object,
        required: true,
    },
    preview: {
        type: Object,
        required: true,
    },
    evaluatorResponses: {
        type: Object,
        default: () => ({}),
    }
});

const { t } = useI18n();
const user = usePage().props.auth.user;
const canManage = props.round.project.owner_id === user.id;

// Form for closing the round
const closeForm = useForm({
    diagnosis: props.round.diagnosis || '',
    publish_immediately: false,
    visibility: 'score_public',
});

// Filters for the conflicts list
const filterDivergence = ref('all'); // all, high, medium, low
const filterSection = ref('all');
const filterCategory = ref('all');
const searchQuery = ref('');

// Flattened list of all questions with section and category names
const allQuestions = computed(() => {
    const list = [];
    props.preview.sections.forEach(sec => {
        sec.categories.forEach(cat => {
            cat.questions.forEach(q => {
                list.push({
                    ...q,
                    section_id: sec.id,
                    section_name: sec.name,
                    category_id: cat.id,
                    category_name: cat.name,
                });
            });
        });
    });
    return list;
});

// Dynamic sections and categories list for dropdown filters
const availableSections = computed(() => {
    const sections = new Map();
    allQuestions.value.forEach(q => {
        sections.set(q.section_id, q.section_name);
    });
    return Array.from(sections.entries()).map(([id, name]) => ({ id, name }));
});

const availableCategories = computed(() => {
    const categories = new Map();
    allQuestions.value.forEach(q => {
        if (filterSection.value === 'all' || q.section_id === Number(filterSection.value)) {
            categories.set(q.category_id, q.category_name);
        }
    });
    return Array.from(categories.entries()).map(([id, name]) => ({ id, name }));
});

// Filtered questions list
const filteredQuestions = computed(() => {
    return allQuestions.value.filter(q => {
        // Filter by divergence level
        if (filterDivergence.value !== 'all' && q.classification !== filterDivergence.value) {
            return false;
        }
        // Filter by section
        if (filterSection.value !== 'all' && q.section_id !== Number(filterSection.value)) {
            return false;
        }
        // Filter by category
        if (filterCategory.value !== 'all' && q.category_id !== Number(filterCategory.value)) {
            return false;
        }
        // Filter by text search
        if (searchQuery.value.trim() && !q.question_text.toLowerCase().includes(searchQuery.value.toLowerCase())) {
            return false;
        }
        return true;
    });
});

// Active selected question state
const activeQuestionId = ref(null);

const activeQuestion = computed(() => {
    if (filteredQuestions.value.length === 0) return null;
    return filteredQuestions.value.find(q => q.question_id === activeQuestionId.value) 
        || filteredQuestions.value[0];
});

// Initialize first selected question
onMounted(() => {
    if (filteredQuestions.value.length > 0) {
        activeQuestionId.value = filteredQuestions.value[0].question_id;
    }
});

const selectQuestion = (qId) => {
    activeQuestionId.value = qId;
};

// Computed properties for selected question
const activeComments = computed(() => {
    if (!activeQuestion.value) return [];
    return props.round.review_comments.filter(c => c.question_id === activeQuestion.value.question_id);
});

const activeConsolidated = computed(() => {
    if (!activeQuestion.value) return null;
    const res = props.round.consolidated_responses.find(cr => cr.question_id === activeQuestion.value.question_id);
    return res ? res.final_answer : null;
});

const activeEvaluatorResponses = computed(() => {
    if (!activeQuestion.value) return [];
    return props.evaluatorResponses[activeQuestion.value.question_id] || [];
});

// Close/consolidate submission
const submitClose = () => {
    closeForm.post(route('rounds.close', props.round.id));
};

const getAnswerBgClass = (ans) => {
    switch (ans) {
        case 'high': return 'bg-emerald-50 text-emerald-800 border-emerald-200';
        case 'medium': return 'bg-amber-50 text-amber-800 border-amber-200';
        case 'low': return 'bg-rose-50 text-rose-800 border-rose-200';
        default: return 'bg-surface-50 text-surface-800 border-surface-200';
    }
};

const consensusModelLabel = computed(() => {
    const model = props.round.project.consensus_model ?? 'owner_decides';
    return t(`labels.consensus_model.${model}`, model);
});
</script>

<template>
    <Head :title="t('review.page_title', { name: round.name })" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <Breadcrumbs :items="[
                        { label: $t('nav.workspace'), url: route('projects.index') },
                        { label: round.project.name, url: route('projects.show', round.project.id) },
                        { label: round.name, url: route('rounds.show', round.id) },
                        { label: $t('review.header_title') }
                    ]" />
                    <h2 class="text-2xl font-semibold text-surface-900 tracking-tight mt-1 flex items-center gap-2">
                        {{ $t('review.header_title') }}
                        <Badge variant="warning">{{ t('round.status_review') }}</Badge>
                    </h2>
                    <p class="text-xs text-surface-500 mt-1">
                        {{ $t('review.header_description', 'Workspace de resolução colaborativa e consenso.') }}
                        • <span class="font-bold text-brand-600">{{ t('settings.consensus_title') }}: {{ consensusModelLabel }}</span>
                    </p>
                </div>
            </div>
        </template>

        <div class="py-12 bg-surface-50 min-h-screen">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <!-- Main Grid Layout -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left: Questions / Conflict List (5 Cols) -->
                    <div class="lg:col-span-5 space-y-4">
                        <Card class="p-4 bg-white border border-surface-200 shadow-sm space-y-4">
                            <h3 class="text-sm font-bold text-surface-800 flex items-center justify-between">
                                <span>{{ t('review.questions_list', 'Lista de Questões') }}</span>
                                <span class="text-xs font-normal text-surface-500">{{ filteredQuestions.length }} / {{ allQuestions.length }}</span>
                            </h3>

                            <!-- Filters block -->
                            <div class="space-y-3 pt-2 border-t border-surface-100">
                                <!-- Text Search -->
                                <input 
                                    v-model="searchQuery" 
                                    type="text" 
                                    :placeholder="t('common.search', 'Buscar questão...')"
                                    class="w-full text-xs rounded-xl border-surface-200 focus:border-brand-500 focus:ring-brand-500"
                                />

                                <!-- Divergence Selector Toggle -->
                                <div class="flex flex-wrap gap-1">
                                    <button 
                                        v-for="lvl in ['all', 'high', 'medium', 'low']" 
                                        :key="lvl"
                                        @click="filterDivergence = lvl"
                                        class="px-2.5 py-1 text-[10px] font-bold rounded-lg uppercase tracking-wider transition-all border"
                                        :class="filterDivergence === lvl 
                                            ? 'bg-surface-800 border-surface-800 text-white shadow-sm'
                                            : 'bg-white border-surface-200 text-surface-600 hover:bg-surface-50'"
                                    >
                                        {{ lvl === 'all' ? t('divergence.all', 'Todas') : t(`labels.divergence.${lvl}`, lvl) }}
                                    </button>
                                </div>

                                <!-- Section & Category Selector Dropdowns -->
                                <div class="grid grid-cols-2 gap-2">
                                    <select v-model="filterSection" class="text-xs rounded-xl border-surface-200 bg-white py-1.5 focus:border-brand-500 focus:ring-brand-500">
                                        <option value="all">{{ t('divergence.filter_section', 'Seção') }}: {{ t('divergence.all') }}</option>
                                        <option v-for="sec in availableSections" :key="sec.id" :value="sec.id">{{ sec.name }}</option>
                                    </select>
                                    <select v-model="filterCategory" class="text-xs rounded-xl border-surface-200 bg-white py-1.5 focus:border-brand-500 focus:ring-brand-500">
                                        <option value="all">{{ t('divergence.filter_category', 'Categoria') }}: {{ t('divergence.all') }}</option>
                                        <option v-for="cat in availableCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                                    </select>
                                </div>
                            </div>
                        </Card>

                        <!-- Question List Cards -->
                        <div class="space-y-2 max-h-[600px] overflow-y-auto pr-1">
                            <div v-if="filteredQuestions.length === 0" class="py-8 text-center text-xs text-surface-400 bg-white rounded-2xl border border-surface-200 shadow-sm">
                                {{ t('divergence.no_questions', 'Nenhuma questão encontrada com os filtros selecionados.') }}
                            </div>
                            
                            <div 
                                v-for="q in filteredQuestions" 
                                :key="q.question_id"
                                @click="selectQuestion(q.question_id)"
                                class="p-4 bg-white rounded-2xl border transition-all cursor-pointer text-left hover:shadow-tactile hover:-translate-y-0.5"
                                :class="activeQuestion?.question_id === q.question_id 
                                    ? 'border-brand-500 shadow-sm ring-1 ring-brand-500/20' 
                                    : 'border-surface-200'"
                            >
                                <div class="flex items-start justify-between gap-3 mb-2">
                                    <span class="text-[10px] font-bold text-surface-400 uppercase truncate max-w-[180px]">
                                        {{ q.category_name }}
                                    </span>
                                    <div class="flex gap-1.5 shrink-0">
                                        <DivergenceBadge :classification="q.classification" />
                                        <ConsensusBadge 
                                            :classification="q.classification" 
                                            :consolidated-answer="props.round.consolidated_responses.find(cr => cr.question_id === q.question_id)?.final_answer"
                                        />
                                    </div>
                                </div>
                                <p class="text-xs font-semibold text-surface-800 line-clamp-2 leading-relaxed">
                                    {{ q.question_text }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Workspace Panel (7 Cols) -->
                    <div class="lg:col-span-7 space-y-6">
                        <div v-if="!activeQuestion" class="bg-white p-12 rounded-[32px] border border-surface-200 text-center text-xs text-surface-400 shadow-sm">
                            {{ t('review.select_question_prompt', 'Selecione uma questão na lista para abrir o Workspace de Resolução.') }}
                        </div>
                        
                        <div v-else class="space-y-6">
                            <!-- Question Details Card -->
                            <Card class="p-6 bg-white border border-surface-200 shadow-sm relative overflow-hidden">
                                <div class="absolute top-0 right-0 w-32 h-32 bg-brand-50 rounded-full -mr-16 -mt-16 opacity-40 pointer-events-none"></div>
                                
                                <div class="flex items-center gap-2 mb-3">
                                    <span class="text-[10px] font-black text-brand-600 uppercase tracking-widest bg-brand-50 border border-brand-100 px-2 py-0.5 rounded-md">
                                        {{ activeQuestion.section_name }}
                                    </span>
                                    <span class="text-[10px] font-semibold text-surface-400 uppercase">
                                        {{ activeQuestion.category_name }}
                                    </span>
                                </div>
                                
                                <h3 class="text-sm font-extrabold text-surface-900 leading-snug">
                                    {{ activeQuestion.question_text }}
                                </h3>

                                <div class="flex gap-4 mt-4 pt-4 border-t border-surface-100 text-xs">
                                    <div>
                                        <span class="text-surface-400 block text-[10px] font-bold uppercase tracking-wider">{{ t('divergence.avg_score', 'Média') }}</span>
                                        <span class="font-extrabold text-surface-800">{{ activeQuestion.score }}%</span>
                                    </div>
                                    <div>
                                        <span class="text-surface-400 block text-[10px] font-bold uppercase tracking-wider">{{ t('divergence.filter_level', 'Divergência') }}</span>
                                        <DivergenceBadge :classification="activeQuestion.classification" class="mt-0.5" />
                                    </div>
                                </div>
                            </Card>

                            <!-- Evaluator Responses list (Side-by-side / Stacks) -->
                            <!-- Only shown to owner OR when show_evaluations_to_all is enabled (backend controls data) -->
                            <div class="space-y-2" v-if="canManage || activeEvaluatorResponses.length > 0">
                                <h4 class="text-xs font-bold text-surface-500 uppercase tracking-wider px-1">
                                    {{ t('review.evaluator_scores', 'Notas e Evidências dos Avaliadores') }}
                                </h4>
                                
                                <div class="grid grid-cols-1 gap-3">
                                    <div 
                                        v-for="resp in activeEvaluatorResponses" 
                                        :key="resp.user_id"
                                        class="p-4 bg-white rounded-2xl border border-surface-200 shadow-sm flex flex-col md:flex-row justify-between items-start gap-4"
                                    >
                                        <div class="space-y-1">
                                            <span class="text-xs font-bold text-surface-800 block">
                                                {{ resp.user_name }}
                                            </span>
                                            <p v-if="resp.observation" class="text-xs text-surface-600 leading-relaxed italic">
                                                "{{ resp.observation }}"
                                            </p>
                                            <p v-else class="text-xs text-surface-400 italic">
                                                {{ t('review.no_evidence', 'Nenhuma justificativa fornecida.') }}
                                            </p>
                                        </div>
                                        
                                        <span class="shrink-0 px-3 py-1 rounded-xl text-xs font-bold border" :class="getAnswerBgClass(resp.answer)">
                                            {{ t(`labels.answer_level.${resp.answer}`, resp.answer) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- Message for evaluators when responses are hidden -->
                            <div v-else class="p-4 bg-surface-50 rounded-2xl border border-surface-200 text-xs text-surface-500 italic">
                                {{ t('review.evaluations_hidden', 'As avaliações individuais não estão visíveis para preservar a independência dos avaliadores.') }}
                            </div>

                            <!-- Resolution Actions Panel based on consensus strategy -->
                            <div class="space-y-2">
                                <div v-if="props.round.project.consensus_model === 'owner_decides'">
                                    <OwnerDecidePanel 
                                        :round-id="round.id"
                                        :question-id="activeQuestion.question_id"
                                        :consolidated-answer="activeConsolidated"
                                        :can-manage="canManage"
                                    />
                                </div>
                                <div v-else-if="props.round.project.consensus_model === 'majority_vote'">
                                    <MajorityResult 
                                        :evaluator-responses="activeEvaluatorResponses"
                                    />
                                </div>
                                <div v-else class="p-4 bg-white rounded-2xl border border-surface-200 shadow-sm flex items-start gap-3">
                                    <span class="p-2 bg-brand-50 text-brand-600 rounded-xl">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                    </span>
                                    <div class="text-xs space-y-1">
                                        <span class="font-bold text-surface-800 block">
                                            {{ t('review.resolution_model_convergence', 'Resolução: Convergência dos Avaliadores') }}
                                        </span>
                                        <p class="text-surface-600 leading-relaxed">
                                            {{ t('review.convergence_instructions', 'Avaliadores devem discutir abaixo para alinhar interpretações. A rodada será consolidada pela média das notas assim que o dono fechar.') }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Discussion comments thread -->
                            <ConflictThread 
                                :round-id="round.id"
                                :question-id="activeQuestion.question_id"
                                :comments="activeComments"
                                :current-user-id="user.id"
                                :is-project-owner="canManage"
                            />
                        </div>
                    </div>
                </div>

                <!-- Final Close Form (Always at the bottom) -->
                <div v-if="canManage" class="mt-12 pt-8 border-t border-surface-200">
                    <Card :title="t('review.finish_panel_title', 'Consolidar e Fechar Rodada')">
                        <form @submit.prevent="submitClose" class="p-6 space-y-6">
                            <div class="space-y-2">
                                <label class="block text-sm font-bold text-surface-700">
                                    {{ t('review.final_diagnosis_label', 'Diagnóstico Consolidado Final') }}
                                </label>
                                <p class="text-xs text-surface-500">
                                    {{ t('review.final_diagnosis_desc', 'Escreva uma conclusão geral sobre o nível de privacidade do produto baseado nos consensos alcançados.') }}
                                </p>
                                <textarea
                                    v-model="closeForm.diagnosis"
                                    rows="6"
                                    class="w-full text-sm rounded-2xl border-surface-200 focus:border-brand-500 focus:ring-brand-500"
                                    :placeholder="t('review.final_diagnosis_placeholder', 'Descreva o diagnóstico...')"
                                ></textarea>
                            </div>

                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-surface-50 p-4 rounded-2xl border border-surface-200">
                                <div class="space-y-1">
                                    <span class="text-xs font-bold text-surface-700 block">
                                        {{ t('review.ready_to_close', 'Tudo pronto para finalizar?') }}
                                    </span>
                                    <p class="text-[11px] text-surface-500 leading-normal">
                                        {{ t('review.close_warning', 'Ao fechar, as notas serão consolidadas de acordo com as resoluções e o score final será gerado.') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-surface-600 mr-2">
                                        <input 
                                            type="checkbox" 
                                            v-model="closeForm.publish_immediately"
                                            class="rounded border-surface-300 text-brand-600 focus:ring-brand-500"
                                        />
                                        {{ t('review.publish_immediately', 'Publicar no diretório agora') }}
                                    </label>
                                    <Button
                                        type="submit"
                                        variant="danger"
                                        :disabled="closeForm.processing"
                                    >
                                        {{ t('review.consolidate_and_close', 'Consolidar e Fechar Rodada') }}
                                    </Button>
                                </div>
                            </div>
                        </form>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
