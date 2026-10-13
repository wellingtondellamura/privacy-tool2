<script setup>
// Collapsible "how is this score calculated?" box shown on every results page.
const medals = [
    { key: 'gold', range: '91–100', dot: 'bg-amber-400' },
    { key: 'silver', range: '61–90', dot: 'bg-slate-400' },
    { key: 'bronze', range: '41–60', dot: 'bg-orange-700' },
    { key: 'incipient', range: '0–40', dot: 'bg-red-500' },
];
</script>

<template>
    <details class="group bg-white rounded-xl border border-surface-200 px-4 sm:px-6 py-4 text-sm text-surface-700">
        <summary class="cursor-pointer font-semibold text-surface-900 flex items-center justify-between gap-2 list-none">
            <span>{{ $t('score_help.title') }}</span>
            <svg class="w-4 h-4 shrink-0 transition-transform group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
        </summary>

        <div class="mt-4 grid gap-6 md:grid-cols-2">
            <ol class="space-y-2 list-decimal pl-5">
                <li>{{ $t('score_help.step_answers') }}</li>
                <li>{{ $t('score_help.step_category') }}</li>
                <li>{{ $t('score_help.step_section') }}</li>
                <li>{{ $t('score_help.step_global') }}</li>
            </ol>

            <div class="space-y-3">
                <p class="font-medium text-surface-900">{{ $t('score_help.medals_title') }}</p>
                <ul class="grid grid-cols-2 gap-2">
                    <li v-for="medal in medals" :key="medal.key" class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="medal.dot"></span>
                        <span>{{ $t(`results.medal.${medal.key}`) }}: {{ medal.range }}</span>
                    </li>
                </ul>
                <p class="text-xs text-surface-500 leading-relaxed">{{ $t('score_help.divergence') }}</p>
            </div>
        </div>
    </details>
</template>
