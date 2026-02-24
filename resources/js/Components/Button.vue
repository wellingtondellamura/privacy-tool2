<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        default: null,
    },
    variant: {
        type: String,
        default: 'primary', // primary, secondary, outline, ghost, danger
    },
    size: {
        type: String,
        default: 'md', // sm, md, lg
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    type: {
        type: String,
        default: 'button',
    }
});

const classes = computed(() => {
    let base = 'inline-flex items-center justify-center font-medium transition-all duration-smooth rounded-lg focus:outline-none focus:ring-2 focus:ring-offset-2';
    
    // Size variants
    let size = {
        'sm': 'px-3 py-1.5 text-sm',
        'md': 'px-4 py-2 text-sm',
        'lg': 'px-6 py-3 text-base',
    }[props.size];

    // Color variants
    let variant = {
        'primary': 'bg-brand-600 text-white shadow-tactile hover:bg-brand-700 hover:shadow-tactile-hover focus:ring-brand-500 active:shadow-tactile-active active:translate-y-[1px]',
        'secondary': 'bg-surface-100 text-surface-800 hover:bg-surface-200 focus:ring-surface-500 active:translate-y-[1px]',
        'outline': 'border border-surface-300 text-surface-700 hover:bg-surface-50 focus:ring-surface-500 active:bg-surface-100 active:translate-y-[1px]',
        'ghost': 'text-surface-600 hover:bg-surface-100 hover:text-surface-900 focus:ring-surface-500 active:bg-surface-200',
        'danger': 'bg-red-600 text-white shadow-tactile hover:bg-red-700 hover:shadow-tactile-hover focus:ring-red-500 active:shadow-tactile-active active:translate-y-[1px]',
    }[props.variant];

    let disabled = props.disabled ? 'opacity-50 cursor-not-allowed pointer-events-none' : '';

    return `${base} ${size} ${variant} ${disabled}`;
});
</script>

<template>
    <Link v-if="href" :href="href" :class="classes">
        <slot />
    </Link>
    <button v-else :type="type" :disabled="disabled" :class="classes">
        <slot />
    </button>
</template>
