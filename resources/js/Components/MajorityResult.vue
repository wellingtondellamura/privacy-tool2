<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    evaluatorResponses: {
        type: Array,
        default: () => [],
    }
});

const { t } = useI18n();

const voteCounts = computed(() => {
    const counts = { high: 0, medium: 0, low: 0, other: 0 };
    props.evaluatorResponses.forEach(r => {
        const ans = r.answer || 'other';
        if (counts[ans] !== undefined) {
            counts[ans]++;
        }
    });
    return counts;
});

const totalVotes = computed(() => {
    return props.evaluatorResponses.length;
});

const winner = computed(() => {
    const counts = voteCounts.value;
    const maxVal = Math.max(counts.high, counts.medium, counts.low, counts.other);
    if (maxVal === 0) return null;

    const candidates = [];
    if (counts.low === maxVal) candidates.push('low');
    if (counts.medium === maxVal) candidates.push('medium');
    if (counts.high === maxVal) candidates.push('high');
    if (counts.other === maxVal) candidates.push('other');

    // Tie-breaker: low > medium > high > other
    if (candidates.includes('low')) return 'low';
    if (candidates.includes('medium')) return 'medium';
    if (candidates.includes('high')) return 'high';
    return 'other';
});

const isTie = computed(() => {
    const counts = voteCounts.value;
    const maxVal = Math.max(counts.high, counts.medium, counts.low, counts.other);
    if (maxVal === 0) return false;
    
    let matchCount = 0;
    if (counts.low === maxVal) matchCount++;
    if (counts.medium === maxVal) matchCount++;
    if (counts.high === maxVal) matchCount++;
    if (counts.other === maxVal) matchCount++;

    return matchCount > 1;
});

const getPercentage = (count) => {
    if (totalVotes.value === 0) return 0;
    return Math.round((count / totalVotes.value) * 100);
};
</script>

<template>
    <div class="p-4 bg-white rounded-2xl border border-surface-200 shadow-sm space-y-4">
        <div>
            <h4 class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-1">
                {{ t('review.resolution_model_majority', 'Resolução: Voto Majoritário') }}
            </h4>
            <p class="text-[11px] text-surface-400">
                {{ t('review.majority_vote_desc', 'A nota final é calculada automaticamente pela maioria dos votos. Empates são decididos em favor da nota mais conservadora (menor).') }}
            </p>
        </div>

        <!-- Votes Breakdown -->
        <div class="space-y-2">
            <div v-for="opt in ['high', 'medium', 'low', 'other']" :key="opt" class="space-y-1">
                <div class="flex justify-between items-center text-xs">
                    <span class="font-semibold text-surface-700 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full"
                              :class="opt === 'high' ? 'bg-emerald-500' :
                                      opt === 'medium' ? 'bg-amber-500' :
                                      opt === 'low' ? 'bg-rose-500' : 'bg-surface-400'"></span>
                        {{ t(`labels.answer_level.${opt}`, opt) }}
                    </span>
                    <span class="text-surface-500 font-bold">
                        {{ voteCounts[opt] }} {{ voteCounts[opt] === 1 ? t('review.vote_singular', 'voto') : t('review.vote_plural', 'votos') }}
                        ({{ getPercentage(voteCounts[opt]) }}%)
                    </span>
                </div>
                <div class="w-full bg-surface-100 h-2 rounded-full overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500"
                         :class="opt === 'high' ? 'bg-emerald-500' :
                                 opt === 'medium' ? 'bg-amber-500' :
                                 opt === 'low' ? 'bg-rose-500' : 'bg-surface-400'"
                         :style="{ width: `${getPercentage(voteCounts[opt])}%` }"></div>
                </div>
            </div>
        </div>

        <!-- Result Badge Alert -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3 bg-surface-50 rounded-xl border border-surface-100 text-xs">
            <div>
                <span class="font-bold text-surface-700 block">
                    {{ t('review.computed_winner', 'Resultado Calculado:') }}
                </span>
                <span v-if="isTie" class="text-[10px] text-amber-700 font-semibold block mt-0.5">
                    ⚠ {{ t('review.tie_broken_conservative', 'Empate resolvido pela nota mais conservadora.') }}
                </span>
            </div>
            
            <div class="flex items-center gap-1.5">
                <Badge :variant="winner === 'high' ? 'success' : winner === 'medium' ? 'warning' : winner === 'low' ? 'error' : 'neutral'">
                    {{ winner ? t(`labels.answer_level.${winner}`, winner) : t('review.no_votes', 'Sem Votos') }}
                </Badge>
            </div>
        </div>
    </div>
</template>
