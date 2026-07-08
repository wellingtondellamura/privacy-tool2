<script setup>
import { ref } from 'vue';
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

// Track open accordion sections — explanation open by default
const openSections = ref({ explanation: true, good: false, bad: false });

const toggleSection = (key) => {
    openSections.value[key] = !openSections.value[key];
};
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-200"
        enter-from-class="opacity-0 -translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-150"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 -translate-y-2"
    >
        <div
            v-if="show"
            class="mt-4 rounded-xl border border-brand-200/70 bg-brand-50/40 overflow-hidden shadow-sm"
        >
            <!-- Panel Header -->
            <div class="flex items-center justify-between px-4 py-2.5 border-b border-brand-100/80 bg-white/60">
                <div class="flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-lg bg-brand-100 text-brand-600">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.347.418A1 1 0 0113 17v1a1 1 0 01-1 1h-2a1 1 0 01-1-1v-1a1 1 0 01-.283-.695l-.347-.418z" />
                        </svg>
                    </span>
                    <span class="text-xs font-bold text-brand-700 uppercase tracking-wider">
                        {{ $t('tooltip.specialist_guide') }}
                    </span>
                </div>
                <button
                    type="button"
                    @click="$emit('close')"
                    class="text-surface-400 hover:text-surface-600 transition-colors rounded-md p-0.5 focus:outline-none focus:ring-1 focus:ring-brand-400"
                    :title="$t('common.close')"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Accordion sections -->
            <div class="divide-y divide-brand-100/60">

                <!-- Section 1: Explanation -->
                <div>
                    <button
                        type="button"
                        @click="toggleSection('explanation')"
                        class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-blue-50/50 transition-colors focus:outline-none"
                    >
                        <span class="flex items-center gap-2">
                            <span class="w-5 h-5 flex items-center justify-center rounded-md bg-blue-100 text-blue-600 shrink-0">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-semibold text-blue-700 uppercase tracking-wide">{{ $t('tooltip.explanation') }}</span>
                        </span>
                        <svg
                            class="w-4 h-4 text-surface-400 transition-transform duration-200 shrink-0"
                            :class="{ 'rotate-180': openSections.explanation }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <Transition
                        enter-active-class="transition-all ease-out duration-200 overflow-hidden"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-96"
                        leave-active-class="transition-all ease-in duration-150 overflow-hidden"
                        leave-from-class="opacity-100 max-h-96"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-if="openSections.explanation" class="px-4 pb-4 pt-0.5">
                            <p class="text-sm text-surface-700 leading-relaxed whitespace-pre-wrap">
                                {{ question.tooltip || $t('tooltip.under_construction') }}
                            </p>
                        </div>
                    </Transition>
                </div>

                <!-- Section 2: Good Practice -->
                <div>
                    <button
                        type="button"
                        @click="toggleSection('good')"
                        class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-emerald-50/40 transition-colors focus:outline-none"
                    >
                        <span class="flex items-center gap-2">
                            <span class="w-5 h-5 flex items-center justify-center rounded-md bg-emerald-100 text-emerald-600 shrink-0">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wide">{{ $t('tooltip.good_practice') }}</span>
                        </span>
                        <svg
                            class="w-4 h-4 text-surface-400 transition-transform duration-200 shrink-0"
                            :class="{ 'rotate-180': openSections.good }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <Transition
                        enter-active-class="transition-all ease-out duration-200 overflow-hidden"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-96"
                        leave-active-class="transition-all ease-in duration-150 overflow-hidden"
                        leave-from-class="opacity-100 max-h-96"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-if="openSections.good" class="px-4 pb-4 pt-0.5">
                            <div class="flex gap-2.5 rounded-lg bg-emerald-50/70 border border-emerald-100 p-3">
                                <span class="text-emerald-500 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </span>
                                <p class="text-sm text-emerald-900 leading-relaxed whitespace-pre-wrap">
                                    {{ question.good_practice_example || $t('tooltip.under_construction') }}
                                </p>
                            </div>
                        </div>
                    </Transition>
                </div>

                <!-- Section 3: Bad Practice -->
                <div>
                    <button
                        type="button"
                        @click="toggleSection('bad')"
                        class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-rose-50/40 transition-colors focus:outline-none"
                    >
                        <span class="flex items-center gap-2">
                            <span class="w-5 h-5 flex items-center justify-center rounded-md bg-rose-100 text-rose-600 shrink-0">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <span class="text-xs font-semibold text-rose-700 uppercase tracking-wide">{{ $t('tooltip.bad_practice') }}</span>
                        </span>
                        <svg
                            class="w-4 h-4 text-surface-400 transition-transform duration-200 shrink-0"
                            :class="{ 'rotate-180': openSections.bad }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <Transition
                        enter-active-class="transition-all ease-out duration-200 overflow-hidden"
                        enter-from-class="opacity-0 max-h-0"
                        enter-to-class="opacity-100 max-h-96"
                        leave-active-class="transition-all ease-in duration-150 overflow-hidden"
                        leave-from-class="opacity-100 max-h-96"
                        leave-to-class="opacity-0 max-h-0"
                    >
                        <div v-if="openSections.bad" class="px-4 pb-4 pt-0.5">
                            <div class="flex gap-2.5 rounded-lg bg-rose-50/70 border border-rose-100 p-3">
                                <span class="text-rose-500 shrink-0 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </span>
                                <p class="text-sm text-rose-900 leading-relaxed whitespace-pre-wrap">
                                    {{ question.bad_practice_example || $t('tooltip.under_construction') }}
                                </p>
                            </div>
                        </div>
                    </Transition>
                </div>

            </div>
        </div>
    </Transition>
</template>
