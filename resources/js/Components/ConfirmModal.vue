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
        required: true,
    },
    message: {
        type: String,
        required: true,
    },
    confirmText: {
        type: String,
        default: null,
    },
    cancelText: {
        type: String,
        default: null,
    },
    confirmVariant: {
        type: String,
        default: 'danger',
    },
    processing: {
        type: Boolean,
        default: false,
    }
});

const emit = defineEmits(['close', 'confirm']);
</script>

<template>
    <Modal :show="show" max-width="sm" @close="$emit('close')">
        <div class="p-6">
            <h2 class="text-lg font-semibold text-surface-900 mb-4">
                {{ title }}
            </h2>
            <p class="text-sm text-surface-600 mb-6" v-if="message">
                {{ message }}
            </p>
            <slot />
            <div class="flex justify-end gap-3" :class="{'mt-6': $slots.default}">
                <Button variant="outline" @click="$emit('close')" :disabled="processing">
                    {{ cancelText || $t('common.cancel') }}
                </Button>
                <Button :variant="confirmVariant" @click="$emit('confirm')" :disabled="processing">
                    {{ confirmText || $t('common.confirm') }}
                </Button>
            </div>
        </div>
    </Modal>
</template>
