<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';
import InspectionLayout from '@/Layouts/InspectionLayout.vue';
import QuestionCard from '@/Components/QuestionCard.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    inspection: {
        type: Object,
        required: true,
    }
});

const activeCategoryId = ref(
    props.inspection.questionnaire_version.sections[0]?.categories[0]?.id || null
);

// Map existing user responses for quick lookup
const responseMap = computed(() => {
    const map = {};
    if (props.inspection.responses) {
        props.inspection.responses.forEach(r => {
            map[r.question_id] = r;
        });
    }
    return map;
});

const sections = computed(() => props.inspection.questionnaire_version.sections);

const activeCategory = computed(() => {
    for (const section of sections.value) {
        const cat = section.categories.find(c => c.id === activeCategoryId.value);
        if (cat) return cat;
    }
    return null;
});

const calculateCategoryProgress = (category) => {
    if (!category.questions || category.questions.length === 0) return 0;
    
    let answered = 0;
    category.questions.forEach(q => {
        if (responseMap.value[q.id]?.answer) {
            answered++;
        }
    });
    
    return Math.round((answered / category.questions.length) * 100);
};

const calculateGlobalProgress = () => {
    let totalQuestions = 0;
    let totalAnswered = 0;

    sections.value.forEach(s => {
        s.categories.forEach(c => {
            totalQuestions += c.questions.length;
            c.questions.forEach(q => {
                if (responseMap.value[q.id]?.answer) {
                    totalAnswered++;
                }
            });
        });
    });

    return totalQuestions === 0 ? 0 : Math.round((totalAnswered / totalQuestions) * 100);
};

const handleResponseSaved = (updatedResponse) => {
    // Interactivity: Could update local state instantly, but since responseMap is computed mapped to props, we mutate props for reactivity.
    const index = props.inspection.responses.findIndex(r => r.question_id === updatedResponse.question_id);
    if (index !== -1) {
        props.inspection.responses[index] = updatedResponse;
    } else {
        props.inspection.responses.push(updatedResponse);
    }
};

const activateForm = useForm({});
const closeForm = useForm({});

const activateInspection = () => {
    activateForm.post(route('inspections.activate', props.inspection.id));
};

const showCloseConfirm = ref(false);

const openCloseConfirm = () => {
    showCloseConfirm.value = true;
};

const cancelClose = () => {
    showCloseConfirm.value = false;
};

const closeInspection = () => {
    closeForm.post(route('inspections.close', props.inspection.id), {
        onSuccess: () => showCloseConfirm.value = false,
    });
};

const isDraft = computed(() => props.inspection.status === 'draft');
const isActive = computed(() => props.inspection.status === 'active');
const isClosed = computed(() => props.inspection.status === 'closed');
const isOwner = computed(() => props.inspection.project.owner_id === usePage().props.auth.user.id);
const isResponsible = computed(() => props.inspection.user_id === usePage().props.auth.user.id);
const { t } = useI18n();

const activeSection = computed(() => {
    return sections.value.find(s => s.categories.some(c => c.id === activeCategoryId.value));
});

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

const activeSectionIndex = computed(() => {
    return sections.value.findIndex(s => s.categories.some(c => c.id === activeCategoryId.value));
});

const activeCategoryIndex = computed(() => {
    const section = sections.value[activeSectionIndex.value];
    return section ? section.categories.findIndex(c => c.id === activeCategoryId.value) : -1;
});

</script>

<template>
    <Head :title="t('project.inspection_label', { id: inspection.sequential_id })" />

    <InspectionLayout>

        <!-- Sidebar Navigation -->
        <template #sidebar>
            <div>
                <!-- Project & Round info -->
                <div class="mb-4 pb-4 border-b border-surface-100">
                    <Link :href="route('projects.show', inspection.project.id)"
                        class="block text-sm font-semibold text-surface-900 hover:text-brand-600 transition-colors truncate"
                        :title="inspection.project.name">
                        {{ inspection.project.name }}
                    </Link>
                    <Link :href="route('rounds.show', inspection.evaluation_round.id)"
                        class="block text-xs text-surface-500 hover:text-brand-600 transition-colors truncate mt-0.5"
                        :title="inspection.evaluation_round.name">
                        {{ inspection.evaluation_round.name }}
                    </Link>
                </div>

                <h3 class="text-xs font-semibold text-surface-500 uppercase tracking-wider mb-3">{{ $t('inspection.sections') }}</h3>
                <nav class="space-y-4">
                    <div v-for="(section, sIndex) in sections" :key="section.id" class="space-y-1">
                        <div class="px-2 font-medium text-surface-900 text-sm mb-2">
                            {{ toRoman(sIndex + 1) }}. {{ section.name }}
                        </div>
                        <button
                            v-for="(cat, cIndex) in section.categories"
                            :key="cat.id"
                            @click="activeCategoryId = cat.id"
                            :class="[
                                'w-full flex flex-col px-3 py-2 rounded-lg text-sm transition-colors text-left',
                                activeCategoryId === cat.id 
                                    ? 'bg-brand-50 text-brand-700 font-medium' 
                                    : 'text-surface-600 hover:bg-surface-100 hover:text-surface-900'
                            ]"
                        >
                            <div class="flex justify-between items-center w-full">
                                <span class="truncate pr-2">{{ toAlpha(cIndex) }}. {{ cat.name }}</span>
                                <span v-if="calculateCategoryProgress(cat) === 100" class="text-brand-600">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                                </span>
                            </div>
                            <!-- Small Progress Bar -->
                            <div class="w-full bg-surface-200 rounded-full h-1 mt-2 mb-1">
                                <div class="bg-brand-500 h-1 rounded-full transition-all duration-500" :style="`width: ${calculateCategoryProgress(cat)}%`"></div>
                            </div>
                        </button>
                    </div>
                </nav>
            </div>
        </template>

        <!-- Main Header -->
        <template #header>
            <div class="flex items-center justify-between w-full min-w-0 gap-3">
                <div class="min-w-0 flex-1">
                    <!-- Compact breadcrumb: Workspace > Project > Round -->
                    <nav class="flex items-center gap-1 text-xs text-surface-400 mb-1 overflow-hidden" aria-label="Breadcrumb">
                        <Link :href="route('projects.index')" class="hover:text-brand-600 transition-colors shrink-0">
                            {{ $t('nav.workspace') }}
                        </Link>
                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <Link :href="route('projects.show', inspection.project.id)"
                            class="font-semibold text-brand-600 hover:text-brand-700 transition-colors truncate max-w-[120px] sm:max-w-[180px]"
                            :title="inspection.project.name">
                            {{ inspection.project.name }}
                        </Link>
                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <Link :href="route('rounds.show', inspection.evaluation_round.id)"
                            class="font-semibold text-surface-600 hover:text-brand-600 transition-colors truncate max-w-[100px] sm:max-w-[160px]"
                            :title="inspection.evaluation_round.name">
                            {{ inspection.evaluation_round.name }}
                        </Link>
                        <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        <span class="text-surface-500 shrink-0">{{ t('project.inspection_label', { id: inspection.sequential_id }) }}</span>
                    </nav>
                </div>


                <div class="flex items-center gap-2 shrink-0">
                    <Badge :variant="isActive ? 'brand' : (isClosed ? 'success' : 'surface')">
                        {{ isActive ? $t('inspection.status.active') : (isClosed ? $t('inspection.status.closed') : $t('inspection.status.draft')) }}
                    </Badge>
                </div>
            </div>
        </template>

        <!-- Global Status Panel -->
        <template #panel>
            <div class="space-y-6">
                <!-- Project Shortcut -->
                <div>
                    <Button variant="outline" size="xs" class="w-full flex items-center justify-center gap-2" @click="$inertia.get(route('rounds.show', inspection.evaluation_round.id))">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                        {{ $t('inspection.back_to_round') }}
                    </Button>
                </div>

                <hr class="border-surface-200" />
                
                <!-- Status Actions -->
                <div class="space-y-3">
                    <h3 class="text-sm font-medium text-surface-900">{{ $t('inspection.actions') }}</h3>
                    
                    <template v-if="isDraft">
                        <p v-if="isResponsible" class="text-xs text-surface-600 mb-3">
                            {{ $t('inspection.draft_message') }}
                        </p>
                        <p v-else class="text-xs text-surface-600 mb-3 italic">
                            {{ $t('inspection.draft_awaiting') }} (<span class="font-medium">{{ inspection.user?.name }}</span>)
                        </p>
                        <Button v-if="isResponsible" variant="primary" size="sm" class="w-full" @click="activateInspection" :disabled="activateForm.processing">
                            {{ $t('inspection.activate_button') }}
                        </Button>
                    </template>

                    <div v-if="isActive" class="p-4 bg-surface-50 rounded-lg border border-surface-200">
                        <p class="text-xs text-surface-600 mb-3">
                            {{ $t('inspection.global_progress') }} {{ calculateGlobalProgress() }}%
                        </p>
                        <div class="w-full bg-surface-200 rounded-full h-2 mb-4">
                            <div class="bg-brand-500 h-2 rounded-full transition-all duration-500" :style="`width: ${calculateGlobalProgress()}%`"></div>
                        </div>
                        <Button v-if="isResponsible" variant="danger" size="sm" class="w-full" @click="openCloseConfirm" :disabled="closeForm.processing">
                            {{ $t('inspection.close_button') }}
                        </Button>
                        <p v-else class="text-[10px] text-surface-500 text-center italic">
                            {{ $t('inspection.only_responsible_prefix') }} (<span class="font-medium text-surface-700">{{ inspection.user?.name }}</span>) {{ $t('inspection.only_responsible_suffix') }}
                        </p>
                    </div>

                    <div v-if="isClosed" class="p-4 bg-green-50 rounded-lg border border-green-200 text-center">
                        <p class="text-sm font-medium text-green-800 mb-2">{{ $t('inspection.closed_title') }}</p>
                        <Button variant="outline" size="sm" class="w-full" @click="$inertia.get(route('results.individual', inspection.id))">
                            {{ $t('inspection.view_results') }}
                        </Button>
                    </div>
                </div>

                <hr class="border-surface-200" />

                <!-- Informações -->
                <div class="space-y-2">
                    <h3 class="text-sm font-medium text-surface-900">{{ $t('inspection.details') }}</h3>
                    <p class="text-xs text-surface-500">
                        <span class="font-medium text-surface-700">{{ $t('round.project_label') }}: </span>
                        <Link :href="route('projects.show', inspection.project.id)"
                            class="text-brand-600 hover:text-brand-700 transition-colors">{{ inspection.project.name }}</Link>
                    </p>
                    <p class="text-xs text-surface-500">
                        <span class="font-medium text-surface-700">{{ $t('round.inspections_label') }}: </span>
                        <Link :href="route('rounds.show', inspection.evaluation_round.id)"
                            class="text-surface-700 hover:text-brand-600 transition-colors">{{ inspection.evaluation_round.name }}</Link>
                    </p>
                    <p v-if="isClosed && inspection.result_snapshots?.length > 0" class="text-xs text-surface-500">
                        <span class="font-medium text-surface-700">{{ $t('inspection.score_label') }} </span>
                        <span class="text-brand-600 font-bold">{{ inspection.result_snapshots[0].payload_json.global_score }}%</span>
                    </p>
                    <p class="text-xs text-surface-500">
                        <span class="font-medium text-surface-700">{{ $t('inspection.status_label') }} </span>
                        {{ isDraft ? $t('inspection.status.draft_detail') : (isActive ? $t('inspection.status.active_detail') : $t('inspection.status.closed')) }}
                    </p>
                    <p class="text-xs text-surface-500">
                        <span class="font-medium text-surface-700">{{ $t('inspection.responsible_label') }} </span>
                        {{ inspection.user?.name || $t('inspection.responsible_system') }}
                    </p>
                    <p class="text-xs text-surface-500">
                        <span class="font-medium text-surface-700">{{ $t('inspection.version_label') }} </span>
                        {{ inspection.questionnaire_version.version_number }}
                    </p>
                    <p class="text-xs text-surface-500">
                        <span class="font-medium text-surface-700">{{ $t('inspection.start_label') }} </span>
                        {{ inspection.started_at ? new Date(inspection.started_at).toLocaleDateString(t('common.locale_code')) : new Date(inspection.created_at).toLocaleDateString(t('common.locale_code')) }}
                    </p>
                    <p v-if="inspection.closed_at" class="text-xs text-surface-500">
                        <span class="font-medium text-surface-700">{{ $t('inspection.end_label') }} </span>
                        {{ new Date(inspection.closed_at).toLocaleDateString(t('common.locale_code')) }}
                    </p>
                </div>
            </div>
        </template>

        <!-- Form Main Content -->
        <div class="space-y-8" v-if="activeCategory">
            <div class="mb-8">
                <!-- Section label above category -->
                <p class="text-xs font-semibold text-surface-400 uppercase tracking-wider mb-1">
                    {{ toRoman(activeSectionIndex + 1) }}. {{ activeSection?.name }}
                </p>
                <h2 class="text-2xl font-semibold text-surface-900 tracking-tight">
                    {{ toAlpha(activeCategoryIndex) }}. {{ activeCategory.name }}
                </h2>

                <p class="text-surface-500 mt-2">
                    {{ $t('inspection.instructions') }}
                </p>
                <div v-if="isDraft" class="mt-4 p-3 bg-yellow-50 text-yellow-800 text-sm border border-yellow-200 rounded-md">
                    {{ $t('inspection.draft_alert') }}
                </div>
                <div v-if="isClosed" class="mt-4 p-3 bg-green-50 text-green-800 text-sm border border-green-200 rounded-md">
                    {{ $t('inspection.closed_alert') }}
                </div>
                <div v-if="isActive && !isResponsible" class="mt-4 p-3 bg-blue-50 text-blue-800 text-sm border border-blue-200 rounded-md flex items-start gap-2">
                    <svg class="w-4 h-4 shrink-0 mt-0.5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <span>{{ $t('inspection.read_only_observer', { name: inspection.user?.name }) }}</span>
                </div>
            </div>

            <div class="space-y-6">
                <transition-group 
                    enter-active-class="transition duration-300 ease-out" 
                    enter-from-class="opacity-0 translate-y-4" 
                    enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="hidden"
                >
                    <QuestionCard
                        v-for="(question, qIndex) in activeCategory.questions"
                        :key="question.id"
                        :question="question"
                        :index="qIndex + 1"
                        :inspection-id="inspection.id"
                        :existing-response="responseMap[question.id]"
                        :options="activeSection?.options || []"
                        :disabled="!isActive || !isResponsible"
                        :require-evidence-for-high="inspection.project.require_evidence_for_high"
                        @saved="handleResponseSaved"
                    />
                </transition-group>
            </div>
        </div>

        <!-- Modals -->
        <ConfirmModal
            :show="showCloseConfirm"
            :title="$t('inspection.close_modal_title')"
            :message="$t('inspection.close_modal_message')"
            :confirm-text="$t('inspection.close_confirm')"
            :cancel-text="$t('common.cancel')"
            confirm-variant="danger"
            :processing="closeForm.processing"
            @confirm="closeInspection"
            @close="cancelClose"
        />

    </InspectionLayout>
</template>
