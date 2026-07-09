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
    }
});

const { t } = useI18n();

const getConsensusModelLabel = (model) => {
    switch(model) {
        case 'owner_decides': return t('models.owner_decides', 'Decisão do Coordenador');
        case 'evaluator_convergence': return t('models.evaluator_convergence', 'Convergência de Avaliadores');
        case 'majority_vote': return t('models.majority_vote', 'Voto da Maioria');
        default: return model;
    }
};

const auditLabel = computed(() => {
    return props.isSelfAssessment 
        ? t('settings.self_assessment') 
        : t('settings.external_audit');
});
</script>

<template>
    <Card class="p-6 bg-white border border-surface-200 shadow-sm relative overflow-hidden">
        <div class="absolute top-0 right-0 w-24 h-24 rounded-full opacity-5 pointer-events-none"
             :class="isSelfAssessment ? 'bg-amber-500' : 'bg-emerald-500'"></div>
        
        <h3 class="text-xs font-bold text-surface-400 uppercase tracking-widest mb-4">
            {{ t('directory.provenance_title', 'Proveniência da Avaliação') }}
        </h3>
        
        <div class="space-y-4">
            <!-- Audit Type Badge/Card -->
            <div class="flex items-center gap-3 p-3 rounded-xl border"
                 :class="isSelfAssessment 
                    ? 'bg-amber-50/50 border-amber-200 text-amber-800' 
                    : 'bg-emerald-50/50 border-emerald-200 text-emerald-800'">
                
                <span class="shrink-0">
                    <!-- Warning Icon for self-assessment -->
                    <svg v-if="isSelfAssessment" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <!-- Check Icon for external audit -->
                    <svg v-else class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </span>
                
                <div class="flex flex-col">
                    <span class="text-xs font-bold uppercase tracking-wider leading-none">
                        {{ isSelfAssessment ? t('directory.provenance_self', 'Autoavaliação') : t('directory.provenance_external', 'Auditoria Externa') }}
                    </span>
                    <span class="text-[11px] opacity-80 mt-1 font-medium">{{ auditLabel }}</span>
                </div>
            </div>

            <!-- Details Grid -->
            <div class="grid grid-cols-2 gap-3 text-xs">
                <!-- Software Version -->
                <div class="p-3 bg-surface-50 rounded-xl border border-surface-100 flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-1">
                        {{ t('directory.provenance_software', 'Versão do Software') }}
                    </span>
                    <span class="font-bold text-surface-700 truncate">
                        {{ softwareVersion || t('round.no_software_version', 'Não informada') }}
                    </span>
                </div>

                <!-- Inspections & Evaluators -->
                <div class="p-3 bg-surface-50 rounded-xl border border-surface-100 flex flex-col justify-between">
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-1">
                        {{ t('directory.provenance_participation', 'Participação') }}
                    </span>
                    <span class="font-bold text-surface-700">
                        {{ userCount }} {{ userCount === 1 ? t('directory.evaluator_singular', 'Avaliador') : t('directory.evaluator_plural', 'Avaliadores') }}
                        <span class="text-[10px] text-surface-400 block font-medium">
                            ({{ inspectionCount }} {{ inspectionCount === 1 ? t('directory.inspection_singular', 'inspeção') : t('directory.inspection_plural', 'inspeções') }})
                        </span>
                    </span>
                </div>
                
                <!-- Consensus Model -->
                <div v-if="consensusModel" class="p-3 bg-surface-50 rounded-xl border border-surface-100 flex flex-col justify-between md:col-span-2">
                    <span class="text-[10px] font-bold text-surface-400 uppercase tracking-wider mb-1 flex items-center gap-1">
                        {{ t('directory.provenance_consensus_model', 'Modelo de Consenso') }}
                        <a href="https://mitra.ufca.edu.br" target="_blank" title="Saiba mais sobre os modelos de consenso" class="text-brand-500 hover:text-brand-600">
                            <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </a>
                    </span>
                    <span class="font-bold text-surface-700">
                        {{ getConsensusModelLabel(consensusModel) }}
                    </span>
                </div>
            </div>

            <!-- Date -->
            <div v-if="inspectionDate" class="text-[11px] text-surface-400 font-medium flex items-center justify-end gap-1">
                <svg class="w-3.5 h-3.5 text-surface-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ t('directory.provenance_closed_at', 'Concluído em') }}: {{ inspectionDate }}
            </div>
        </div>
    </Card>
</template>
