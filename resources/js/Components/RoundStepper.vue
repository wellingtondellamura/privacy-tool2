<script setup>
import { computed } from 'vue';

// Explains the round lifecycle (draft → review → closed) and what happens in each phase.
const props = defineProps({
    status: {
        type: String,
        required: true,
    },
});

const steps = ['draft', 'review', 'closed'];

// Legacy rounds may still use 'active', which behaves like 'draft'.
const currentIndex = computed(() => Math.max(0, steps.indexOf(props.status === 'active' ? 'draft' : props.status)));
</script>

<template>
    <ol class="grid grid-cols-1 sm:grid-cols-3 gap-2 sm:gap-3" :aria-label="$t('round_steps.aria_label')">
        <li
            v-for="(step, index) in steps"
            :key="step"
            class="flex gap-3 rounded-xl border p-3"
            :class="index === currentIndex
                ? 'border-brand-300 bg-brand-50'
                : (index < currentIndex ? 'border-emerald-200 bg-emerald-50/50' : 'border-surface-200 bg-white')"
            :aria-current="index === currentIndex ? 'step' : undefined"
        >
            <span
                class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-xs font-bold"
                :class="index === currentIndex
                    ? 'bg-brand-600 text-white'
                    : (index < currentIndex ? 'bg-emerald-500 text-white' : 'bg-surface-200 text-surface-600')"
            >
                <svg v-if="index < currentIndex" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                <template v-else>{{ index + 1 }}</template>
            </span>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-surface-900">{{ $t(`round_steps.${step}_title`) }}</p>
                <p class="text-xs text-surface-600 mt-0.5 leading-relaxed">{{ $t(`round_steps.${step}_desc`) }}</p>
            </div>
        </li>
    </ol>
</template>
