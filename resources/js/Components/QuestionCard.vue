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
    // If answer is not set, we don't save. 
    // If it's the same as existing, we skip (unless it's an auto-save for observation)
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
    saveTimeout = setTimeout(saveResponse, 500); // Debounce auto-save
};

// Auto-save when answer changes immediately
const selectAnswer = (val) => {
    if (props.disabled) return;
    if (answer.value === val) return; // Toggle logic if needed, but here we just select
    
    answer.value = val;
    // Debounced text area save logic, immediate for radios
    if (saveTimeout) clearTimeout(saveTimeout);
    saveResponse();
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

        <h4 class="text-lg font-medium text-surface-900 leading-relaxed">
            {{ index }}. {{ question.text }}
        </h4>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <button 
                v-for="opt in options" 
                :key="opt.value"
                type="button"
                @click="selectAnswer(opt.value)"
                :disabled="disabled"
                :class="[
                    'relative flex flex-col flex-1 px-4 py-3 rounded-lg border focus:outline-none transition-all duration-smooth text-left',
                    answer === opt.value 
                        ? 'bg-brand-50 border-brand-500 ring-1 ring-brand-500 z-10' 
                        : 'border-surface-200 bg-white hover:bg-surface-50 text-surface-900',
                    disabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'
                ]"
            >
                <span :class="['block text-sm font-medium', answer === opt.value ? 'text-brand-900' : 'text-surface-900']">
                    {{ opt.label }}
                </span>
                <span v-if="opt.desc" :class="['block text-xs mt-1', answer === opt.value ? 'text-brand-700' : 'text-surface-500']">
                    {{ opt.desc }}
                </span>
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
                    Por favor, especifique
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
