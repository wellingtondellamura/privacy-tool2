<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'surface', // surface, brand, success, warning, danger
    }
});

const classes = computed(() => {
    let base = 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium tracking-wide transition-colors';
    
    let variant = {
        'surface': 'bg-surface-100 text-surface-800',
        'brand':   'bg-brand-100 text-brand-800',
        'success': 'bg-emerald-100 text-emerald-800',
        'warning': 'bg-yellow-100 text-yellow-800',
        'danger':  'bg-red-100 text-red-800',
        'error':   'bg-red-100 text-red-800',
        'neutral': 'bg-surface-100 text-surface-800',
        'primary': 'bg-brand-100 text-brand-800',
        // Inspection status variants
        'draft':   'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
        'active':  'bg-blue-100 text-blue-700 ring-1 ring-blue-200',
        'closed':  'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
    }[props.variant] || 'bg-surface-100 text-surface-800';

    return `${base} ${variant}`;
});
</script>

<template>
    <span :class="classes">
        <slot />
    </span>
</template>
