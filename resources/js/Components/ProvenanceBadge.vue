<script setup>
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import Card from '@/Components/Card.vue';

const props = defineProps({
    isSelfAssessment: {
        type: Boolean,
        default: false
    },
    softwareVersion: {
        type: String,
        default: null
    },
    userCount: {
        type: Number,
        default: 0
    },
    inspectionCount: {
        type: Number,
        default: 0
    },
    inspectionDate: {
        type: String,
        default: null
    },
    consensusModel: {
        type: String,
        default: null
    },
    showTitle: {
        type: Boolean,
        default: true
    }
});

const { t } = useI18n();

const getConsensusModelLabel = (model) => {
    if (!model) return '';
    return t(`labels.consensus_model.${model}`, model);
};

const auditLabel = computed(() => {
    return props.isSelfAssessment 
        ? t('settings.self_assessment', 'Autoavaliação') 
        : t('settings.external_audit', 'Auditoria Externa');
});
</script>

<template>
    <Card class="p-6 bg-white border border-surface-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full opacity-5 pointer-events-none"
             :class="isSelfAssessment ? 'bg-amber-500' : 'bg-emerald-500'"></div>
        
        <div class="space-y-4">
            <h3 v-if="showTitle" class="text-xs font-bold text-surface-400 uppercase tracking-widest mb-1">
                {{ t('directory.provenance_title', 'Proveniência da Avaliação') }}
            </h3>
            
            <!-- Audit Type Badge/Card -->
            <div class="flex items-center gap-3 p-3.5 rounded-2xl border"
                 :class="isSelfAssessment 
                    ? 'bg-amber-50/70 border-amber-200/80 text-amber-900' 
                    : 'bg-emerald-50/70 border-emerald-200/80 text-emerald-900'">
                
                <span class="shrink-0 p-2 rounded-xl" :class="isSelfAssessment ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'">
                    <!-- Warning Icon for self-assessment -->
                    <svg v-if="isSelfAssessment" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <!-- Check Icon for external audit -->
                    <svg v-else class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                
                <div class="flex flex-col min-w-0">
                    <span class="text-xs font-bold uppercase tracking-wider leading-snug">
                        {{ isSelfAssessment ? t('directory.provenance_self', 'Autoavaliação') : t('directory.provenance_external', 'Auditoria Externa') }}
                    </span>
                    <span class="text-[11px] opacity-80 font-medium truncate">{{ auditLabel }}</span>
                </div>
            </div>

            <!-- Details List/Grid -->
            <div class="grid grid-cols-1 gap-2.5 text-xs">
                <!-- Software Version -->
                <div class="p-3 bg-surface-50/80 rounded-xl border border-surface-100 flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider shrink-0">
                        {{ t('directory.provenance_software', 'Versão do Software') }}
                    </span>
                    <span class="font-bold text-surface-800 text-right truncate">
                        {{ softwareVersion || t('round.no_software_version', 'Não informada') }}
                    </span>
                </div>

                <!-- Participation -->
                <div class="p-3 bg-surface-50/80 rounded-xl border border-surface-100 flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider shrink-0">
                        {{ t('directory.provenance_participation', 'Participação') }}
                    </span>
                    <span class="font-bold text-surface-800 text-right">
                        {{ userCount }} {{ userCount === 1 ? t('directory.evaluator_singular', 'Avaliador') : t('directory.evaluator_plural', 'Avaliadores') }}
                        <span class="text-[10px] text-surface-500 font-medium block">
                            ({{ inspectionCount }} {{ inspectionCount === 1 ? t('directory.inspection_singular', 'inspeção') : t('directory.inspection_plural', 'inspeções') }})
                        </span>
                    </span>
                </div>
                
                <!-- Consensus Model -->
                <div v-if="consensusModel" class="p-3 bg-surface-50/80 rounded-xl border border-surface-100 flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider shrink-0 flex items-center gap-1">
                        {{ t('directory.provenance_consensus_model', 'Modelo de Consenso') }}
                    </span>
                    <span class="font-bold text-surface-800 text-right">
                        {{ getConsensusModelLabel(consensusModel) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Date Footer -->
        <div v-if="inspectionDate" class="pt-3 mt-4 border-t border-surface-100 text-[11px] text-surface-400 font-medium flex items-center justify-between">
            <span class="text-[10px] uppercase font-bold text-surface-400 tracking-wider">{{ t('directory.provenance_closed_at', 'Concluído em') }}</span>
            <span class="font-semibold text-surface-600">{{ inspectionDate }}</span>
        </div>
    </Card>
</template>
