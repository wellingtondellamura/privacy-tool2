<script setup>
import { ref, watch } from 'vue';
import { useI18n } from 'vue-i18n';
import Card from '@/Components/Card.vue';
import axios from 'axios';

const { t } = useI18n();

const props = defineProps({
    question: {
        type: Object,
        required: true,
    },
    inspectionId: {
        type: Number,
        required: true,
    },
    existingResponse: {
        type: Object,
        default: null,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    index: {
        type: Number,
        required: true,
    },
    options: {
        type: Array,
        required: true,
    }
});

const emit = defineEmits(['saved']);

const answer = ref(props.existingResponse?.answer || null);
const observation = ref(props.existingResponse?.observation || '');
const isSaving = ref(false);
const saveError = ref(null);

let saveTimeout = null;

const saveResponse = async () => {
    if (!answer.value) return;

    isSaving.value = true;
    saveError.value = null;

    try {
        const response = await axios.post(`/inspections/${props.inspectionId}/response`, {
            question_id: props.question.id,
            answer: answer.value,
            observation: observation.value,
        });
        
        emit('saved', response.data);
    } catch (error) {
        saveError.value = error.response?.data?.message || t('common.save_error');
    } finally {
        isSaving.value = false;
    }
};

const triggerSave = () => {
    if (saveTimeout) clearTimeout(saveTimeout);
    saveTimeout = setTimeout(saveResponse, 500);
};

const selectAnswer = (val) => {
    if (props.disabled) return;
    if (answer.value === val) return;
    
    answer.value = val;
    if (saveTimeout) clearTimeout(saveTimeout);
    saveResponse();
};

// Color scheme per answer value
const colorConfig = {
    high:   { dot: 'bg-emerald-500', selected: 'bg-emerald-50 border-emerald-500 ring-1 ring-emerald-500', hover: 'hover:bg-emerald-50 hover:border-emerald-300', label: 'text-emerald-900', desc: 'text-emerald-700' },
    medium: { dot: 'bg-amber-500',   selected: 'bg-amber-50 border-amber-500 ring-1 ring-amber-500',       hover: 'hover:bg-amber-50 hover:border-amber-300',   label: 'text-amber-900',   desc: 'text-amber-700' },
    low:    { dot: 'bg-rose-500',    selected: 'bg-rose-50 border-rose-500 ring-1 ring-rose-500',           hover: 'hover:bg-rose-50 hover:border-rose-300',     label: 'text-rose-900',    desc: 'text-rose-700' },
    other:  { dot: 'bg-violet-500',  selected: 'bg-violet-50 border-violet-500 ring-1 ring-violet-500',     hover: 'hover:bg-violet-50 hover:border-violet-300', label: 'text-violet-900',  desc: 'text-violet-700' },
};

const getButtonClass = (optValue) => {
    const cfg = colorConfig[optValue] || colorConfig.other;
    const isSelected = answer.value === optValue;
    return [
        'relative flex flex-col flex-1 px-4 py-3 rounded-lg border-2 focus:outline-none transition-all duration-200 text-left',
        isSelected ? cfg.selected : `border-surface-200 bg-white ${cfg.hover}`,
        props.disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer',
    ];
};

const getLabelClass = (optValue) => {
    const cfg = colorConfig[optValue] || colorConfig.other;
    return ['block text-sm font-semibold', answer.value === optValue ? cfg.label : 'text-surface-900'];
};

const getDescClass = (optValue) => {
    const cfg = colorConfig[optValue] || colorConfig.other;
    return ['block text-xs mt-1', answer.value === optValue ? cfg.desc : 'text-surface-500'];
};
</script>

<template>
    <Card class="relative">
        <!-- Saving Indicator -->
        <div v-if="isSaving" class="absolute top-4 right-4 flex items-center gap-2">
            <span class="relative flex h-3 w-3">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-3 w-3 bg-brand-500"></span>
            </span>
            <span class="text-xs text-brand-500 font-medium">{{ $t('common.saving') }}</span>
        </div>

        <h4 class="text-lg font-medium text-surface-900 leading-relaxed pr-24">
            {{ index }}. {{ question.text }}
        </h4>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <button 
                v-for="opt in options" 
                :key="opt.value"
                type="button"
                @click="selectAnswer(opt.value)"
                :disabled="disabled"
                :class="getButtonClass(opt.value)"
            >
                <!-- Color dot indicator -->
                <span class="flex items-center gap-2 mb-1">
                    <span :class="['w-2 h-2 rounded-full shrink-0', colorConfig[opt.value]?.dot || 'bg-violet-500']"></span>
                    <span :class="getLabelClass(opt.value)">{{ opt.label }}</span>
                </span>
                <span v-if="opt.desc" :class="getDescClass(opt.value)">{{ opt.desc }}</span>
            </button>
        </div>

        <transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 translate-y-1"
        >
            <div v-if="answer === 'other'" class="mt-4">
                <label :for="'obs-' + question.id" class="block text-sm font-medium text-surface-700 mb-1">
                    {{ $t('question.specify_label') }}
                </label>
                <textarea
                    :id="'obs-' + question.id"
                    v-model="observation"
                    @input="triggerSave"
                    rows="3"
                    :disabled="disabled"
                    :placeholder="$t('component.observation_placeholder')"
                    class="block w-full rounded-md border-surface-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm transition-colors"
                ></textarea>
            </div>
        </transition>

        <p v-if="saveError" class="mt-2 text-sm text-red-600">
            {{ saveError }}
        </p>
    </Card>
</template>

