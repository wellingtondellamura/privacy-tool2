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
    title: {
        type: String,
        default: null,
    },
    message: {
        type: String,
        required: true,
    },
    buttonText: {
        type: String,
        default: 'OK',
    },
    variant: {
        type: String,
        default: 'primary', // primary, success, danger, warning
    },
});

const emit = defineEmits(['close']);
</script>

<template>
    <Modal :show="show" max-width="md" @close="$emit('close')">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div v-if="variant === 'success'" class="hidden sm:flex shrink-0 items-center justify-center h-10 w-10 rounded-full bg-green-100">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div v-if="variant === 'danger'" class="hidden sm:flex shrink-0 items-center justify-center h-10 w-10 rounded-full bg-red-100">
                    <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div v-if="variant === 'warning'" class="hidden sm:flex shrink-0 items-center justify-center h-10 w-10 rounded-full bg-amber-100">
                    <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                
                <div class="flex-grow">
                    <h2 class="text-lg font-semibold text-surface-900 mb-2">
                        {{ title || $t('common.warning') }}
                    </h2>
                    <p class="text-sm text-surface-600">
                        {{ message }}
                    </p>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <Button :variant="variant === 'danger' ? 'danger' : 'primary'" @click="$emit('close')">
                    {{ buttonText }}
                </Button>
            </div>
        </div>
    </Modal>
</template>
