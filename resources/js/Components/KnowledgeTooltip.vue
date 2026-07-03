<script setup>
import Modal from '@/Components/Modal.vue';
import Button from '@/Components/Button.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    question: {
        type: Object,
        required: true,
    },
    index: {
        type: Number,
        required: true,
    }
});

const emit = defineEmits(['close']);
</script>

<template>
    <Modal :show="show" max-width="2xl" @close="$emit('close')">
        <div class="p-6 space-y-6">
            <!-- Header -->
            <div class="flex items-start justify-between">
                <div>
                    <span class="block text-xs font-semibold text-brand-600 uppercase tracking-wider mb-1">
                        {{ $t('tooltip.specialist_guide') }}
                    </span>
                    <h3 class="text-lg font-bold text-surface-900 leading-snug">
                        {{ index }}. {{ question.text }}
                    </h3>
                </div>
            </div>

            <!-- Explanation / Tooltip text -->
            <div class="p-4 bg-brand-50/50 rounded-xl border border-brand-100/80 flex gap-3 items-start">
                <svg class="w-5.5 h-5.5 text-brand-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div class="text-sm text-surface-700 leading-relaxed whitespace-pre-wrap">
                    {{ question.tooltip || $t('tooltip.under_construction') }}
                </div>
            </div>

            <!-- Examples Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Good Practice Example -->
                <div class="p-4 bg-emerald-50/40 rounded-xl border border-emerald-100 flex flex-col">
                    <span class="flex items-center gap-1.5 text-xs font-bold text-emerald-700 uppercase tracking-wider mb-2">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $t('tooltip.good_practice') }}
                    </span>
                    <p class="text-xs text-surface-700 leading-relaxed whitespace-pre-wrap flex-grow">
                        {{ question.good_practice_example || $t('tooltip.under_construction') }}
                    </p>
                </div>

                <!-- Bad Practice Example -->
                <div class="p-4 bg-rose-50/40 rounded-xl border border-rose-100 flex flex-col">
                    <span class="flex items-center gap-1.5 text-xs font-bold text-rose-700 uppercase tracking-wider mb-2">
                        <svg class="w-4 h-4 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $t('tooltip.bad_practice') }}
                    </span>
                    <p class="text-xs text-surface-700 leading-relaxed whitespace-pre-wrap flex-grow">
                        {{ question.bad_practice_example || $t('tooltip.under_construction') }}
                    </p>
                </div>
            </div>

            <!-- Footer -->
            <div class="flex justify-end pt-2">
                <Button variant="outline" size="sm" @click="$emit('close')">
                    {{ $t('common.close') }}
                </Button>
            </div>
        </div>
    </Modal>
</template>
