<script setup>
defineProps({
    title: {
        type: String,
        default: null,
    },
    description: {
        type: String,
        default: null,
    },
    noPadding: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="relative bg-white rounded-xl shadow-tactile border border-surface-100 overflow-hidden transition-all duration-smooth hover:shadow-tactile-hover">
        <div v-if="title || $slots.header" class="px-6 py-5 border-b border-surface-100 bg-surface-50/50">
            <slot name="header">
                <h3 class="text-lg font-medium leading-6 text-surface-900">{{ title }}</h3>
                <p v-if="description" class="mt-1 text-sm text-surface-500">{{ description }}</p>
            </slot>
        </div>
        <div :class="[noPadding ? '' : 'px-6 py-5']">
            <slot />
        </div>
        <div v-if="$slots.footer" class="px-6 py-4 bg-surface-50 border-t border-surface-100 sm:px-6">
            <slot name="footer" />
        </div>
    </div>
</template>
