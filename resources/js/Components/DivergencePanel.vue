<script setup>
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import DivergenceBadge from '@/Components/DivergenceBadge.vue';

const { t } = useI18n();

const props = defineProps({
    sections: {
        type: Array,
        required: true,
    },
    // Optional callback if we want to show a details/resolve action
    onResolveQuestion: {
        type: Function,
        default: null,
    },
    consensusModel: {
        type: String,
        default: 'owner_decides',
    }
});

const levelFilter = ref('high'); // 'all', 'high', 'medium', 'low'
const selectedSectionId = ref('all');
const selectedCategoryId = ref('all');
const collapseCategories = ref(false);

const collapsedState = ref({});

watch(selectedSectionId, () => {
    selectedCategoryId.value = 'all';
});

// Watch global collapse toggle to reset individual category states
watch(collapseCategories, (newVal) => {
    collapsedState.value = {};
});

const availableCategories = computed(() => {
    const list = [{ id: 'all', name: t('divergence.all') }];
    props.sections.forEach(s => {
        if (selectedSectionId.value === 'all' || s.id === selectedSectionId.value) {
            s.categories.forEach(c => {
                list.push({ id: c.id, name: c.name });
            });
        }
    });
    return list;
});

const filteredSections = computed(() => {
    return props.sections.map(s => {
        if (selectedSectionId.value !== 'all' && s.id !== selectedSectionId.value) {
            return null;
        }

        const categories = s.categories.map(c => {
            if (selectedCategoryId.value !== 'all' && c.id !== selectedCategoryId.value) {
                return null;
            }

            const questions = c.questions.filter(q => {
                if (levelFilter.value !== 'all' && q.classification !== levelFilter.value) {
                    return false;
                }
                return true;
            });

            if (questions.length === 0) return null;

            return {
                ...c,
                questions
            };
        }).filter(Boolean);

        if (categories.length === 0) return null;

        return {
            ...s,
            categories
        };
    }).filter(Boolean);
});

const isCategoryCollapsed = (catId) => {
    if (collapsedState.value[catId] !== undefined) {
        return collapsedState.value[catId];
    }
    return collapseCategories.value;
};

const toggleCategoryCollapse = (catId) => {
    collapsedState.value[catId] = !isCategoryCollapsed(catId);
};

// Count matching questions in a category for summary display
const getCategoryDivergenceCounts = (cat) => {
    let high = 0, medium = 0, low = 0;
    cat.questions.forEach(q => {
        if (q.classification === 'high') high++;
        else if (q.classification === 'medium') medium++;
        else if (q.classification === 'low') low++;
    });
    return { high, medium, low };
};
</script>

<template>
    <div class="space-y-6">
        <!-- Dashboard Filters Card -->
        <Card class="bg-surface-50/50">
            <div class="p-6 space-y-4">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <!-- Divergence Level filter (Buttons) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-semibold text-surface-500 uppercase tracking-wider">
                            {{ $t('divergence.filter_level') }}
                        </label>
                        <div class="inline-flex rounded-lg border border-surface-200 p-0.5 bg-white shadow-sm">
                            <button
                                v-for="level in ['all', 'high', 'medium', 'low']"
                                :key="level"
                                @click="levelFilter = level"
                                type="button"
                                class="px-3.5 py-1.5 text-xs font-medium rounded-md transition-all duration-200"
                                :class="levelFilter === level
                                    ? {
                                        'all': 'bg-surface-900 text-white shadow-sm',
                                        'high': 'bg-red-600 text-white shadow-sm',
                                        'medium': 'bg-amber-500 text-white shadow-sm',
                                        'low': 'bg-emerald-600 text-white shadow-sm'
                                      }[level]
                                    : 'text-surface-600 hover:text-surface-900 hover:bg-surface-50'"
                            >
                                <span class="flex items-center gap-1.5">
                                    <span v-if="level !== 'all'" class="w-1.5 h-1.5 rounded-full shrink-0" :class="{
                                        'high': 'bg-white',
                                        'medium': 'bg-white',
                                        'low': 'bg-white'
                                    }[level]"></span>
                                    <span>{{ level === 'all' ? $t('divergence.all') : $t('labels.divergence.' + level) }}</span>
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Toggle Group and Collapse -->
                    <div class="flex items-center gap-2 lg:self-end h-10">
                        <input
                            id="collapse_categories"
                            type="checkbox"
                            v-model="collapseCategories"
                            class="h-4 w-4 rounded border-surface-300 text-brand-600 focus:ring-brand-500 cursor-pointer"
                        />
                        <label for="collapse_categories" class="text-xs font-semibold text-surface-700 cursor-pointer select-none">
                            {{ $t('divergence.collapse_categories') }}
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-surface-100">
                    <!-- Section Filter Dropdown -->
                    <div class="space-y-1">
                        <label for="filter-section" class="block text-[10px] font-bold text-surface-500 uppercase tracking-wider">
                            {{ $t('divergence.filter_section') }}
                        </label>
                        <select
                            id="filter-section"
                            v-model="selectedSectionId"
                            class="block w-full text-xs rounded-lg border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option value="all">{{ $t('divergence.all') }}</option>
                            <option v-for="sec in sections" :key="sec.id" :value="sec.id">
                                {{ sec.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Category Filter Dropdown -->
                    <div class="space-y-1">
                        <label for="filter-category" class="block text-[10px] font-bold text-surface-500 uppercase tracking-wider">
                            {{ $t('divergence.filter_category') }}
                        </label>
                        <select
                            id="filter-category"
                            v-model="selectedCategoryId"
                            class="block w-full text-xs rounded-lg border-surface-300 bg-white shadow-sm focus:border-brand-500 focus:ring-brand-500"
                        >
                            <option v-for="cat in availableCategories" :key="cat.id" :value="cat.id">
                                {{ cat.name }}
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </Card>

        <!-- No matches indicator -->
        <div v-if="filteredSections.length === 0" class="py-16 text-center bg-white border border-surface-200 rounded-xl">
            <svg class="w-12 h-12 text-surface-300 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="text-sm font-medium text-surface-500">
                {{ $t('divergence.no_questions') }}
            </p>
        </div>

        <!-- Grouped Results Tree -->
        <div v-else class="space-y-8">
            <div v-for="section in filteredSections" :key="section.id" class="space-y-4">
                <!-- Section Header -->
                <div class="border-b border-surface-200 pb-2">
                    <h4 class="text-xs font-bold text-surface-400 uppercase tracking-widest">
                        {{ section.name }}
                    </h4>
                </div>

                <div class="space-y-4">
                    <div v-for="category in section.categories" :key="category.id" 
                        class="bg-white border border-surface-200 rounded-xl shadow-sm overflow-hidden"
                    >
                        <!-- Category Bar (Collapsible trigger) -->
                        <div 
                            @click="toggleCategoryCollapse(category.id)"
                            class="flex items-center justify-between p-4 bg-surface-50/50 hover:bg-surface-50 cursor-pointer transition-colors border-b border-surface-100"
                        >
                            <div class="flex items-center gap-3">
                                <!-- Chevron toggle indicator -->
                                <svg 
                                    class="w-4 h-4 text-surface-400 transition-transform duration-200" 
                                    :class="{'rotate-180': isCategoryCollapsed(category.id)}"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                                <span class="font-semibold text-sm text-surface-800">{{ category.name }}</span>
                            </div>

                            <div class="flex items-center gap-3">
                                <!-- Group count indicators -->
                                <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                    <span v-if="getCategoryDivergenceCounts(category).high > 0" class="px-1.5 py-0.5 rounded bg-red-100 text-red-800">
                                        🔴 {{ getCategoryDivergenceCounts(category).high }}
                                    </span>
                                    <span v-if="getCategoryDivergenceCounts(category).medium > 0" class="px-1.5 py-0.5 rounded bg-amber-100 text-amber-800">
                                        🟡 {{ getCategoryDivergenceCounts(category).medium }}
                                    </span>
                                    <span v-if="getCategoryDivergenceCounts(category).low > 0" class="px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                        🟢 {{ getCategoryDivergenceCounts(category).low }}
                                    </span>
                                </div>
                                <span class="text-xs bg-surface-200 text-surface-800 font-semibold px-2 py-0.5 rounded-full">
                                    {{ $t('divergence.avg_score') }}: {{ category.score }}%
                                </span>
                            </div>
                        </div>

                        <!-- Questions List under this Category -->
                        <div v-show="!isCategoryCollapsed(category.id)" class="divide-y divide-surface-100">
                            <div 
                                v-for="q in category.questions" 
                                :key="q.question_id"
                                class="p-4 sm:flex items-start justify-between gap-4 hover:bg-surface-50/30 transition-colors"
                            >
                                <div class="space-y-1 flex-grow">
                                    <p class="text-sm font-medium text-surface-900 leading-relaxed">
                                        {{ q.question_text }}
                                    </p>
                                    <div class="flex items-center gap-3 text-xs text-surface-500">
                                        <span>ID: #{{ q.question_id }}</span>
                                        <span>•</span>
                                        <span>{{ $t('divergence.variance_label') }}: <span class="font-semibold text-surface-700">{{ q.variance }}</span></span>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 mt-3 sm:mt-0 shrink-0">
                                    <span class="text-xs bg-surface-100 text-surface-800 font-medium px-2 py-1 rounded">
                                        Média: <span class="font-bold">{{ q.score }}</span>
                                    </span>
                                    <DivergenceBadge :classification="q.classification" />
                                    
                                    <!-- Action Button (only if callback is provided) -->
                                    <Button 
                                        v-if="onResolveQuestion && q.classification === 'high'" 
                                        @click="onResolveQuestion(q.question_id)" 
                                        size="xs" 
                                        variant="outline"
                                        class="!text-brand-700 !border-brand-200 hover:bg-brand-50"
                                    >
                                        {{ consensusModel === 'owner_decides' ? 'Decidir Nota' : 'Discutir' }}
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
