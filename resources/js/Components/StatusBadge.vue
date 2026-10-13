<script setup>
import Badge from '@/Components/Badge.vue';

// Single place that maps round/inspection statuses to label + color.
defineProps({
    status: {
        type: String,
        required: true,
    },
});

const dots = {
    draft: 'bg-slate-400',
    active: 'bg-blue-500',
    review: 'bg-amber-500',
    closed: 'bg-emerald-500',
};
</script>

<template>
    <Badge :variant="status">
        <span class="flex items-center gap-1.5">
            <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="dots[status] || 'bg-slate-400'"></span>
            {{ $t(`status.${status}`) }}
        </span>
    </Badge>
</template>
