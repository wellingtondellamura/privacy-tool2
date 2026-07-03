<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    classification: {
        type: String,
        default: 'low'
    },
    consolidatedAnswer: {
        type: String,
        default: null
    }
});

const { t } = useI18n();

const statusText = computed(() => {
    if (props.consolidatedAnswer) {
        return t(`labels.answer_level.${props.consolidatedAnswer}`, props.consolidatedAnswer);
    }
    
    if (props.classification === 'high' || props.classification === 'medium') {
        return t('review.in_conflict', 'Em Conflito');
    }
    
    return t('review.consensus', 'Consenso');
});

const variant = computed(() => {
    if (props.consolidatedAnswer) {
        return 'success';
    }
    if (props.classification === 'high') {
        return 'error';
    }
    if (props.classification === 'medium') {
        return 'warning';
    }
    return 'neutral';
});
</script>

<template>
    <Badge :variant="variant" class="uppercase tracking-wider text-[9px] font-bold">
        <span class="flex items-center gap-1">
            <span v-if="consolidatedAnswer" class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
            {{ statusText }}
        </span>
    </Badge>
</template>
