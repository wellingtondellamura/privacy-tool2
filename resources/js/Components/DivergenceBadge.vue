<script setup>
import { computed } from 'vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    classification: {
        type: String,
        required: true,
        validator: (val) => ['low', 'medium', 'high'].includes(val),
    }
});

const variant = computed(() => {
    return {
        'low': 'success',
        'medium': 'warning',
        'high': 'danger',
    }[props.classification] || 'surface';
});

const dotColor = computed(() => {
    return {
        'low': 'bg-emerald-500',
        'medium': 'bg-amber-500',
        'high': 'bg-rose-500',
    }[props.classification] || 'bg-surface-500';
});
</script>

<template>
    <Badge :variant="variant" class="flex items-center gap-1.5 font-semibold text-[10px] sm:text-xs">
        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="dotColor"></span>
        <span>{{ $t('labels.divergence.' + classification) }}</span>
    </Badge>
</template>
