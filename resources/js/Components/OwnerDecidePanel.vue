<script setup>
import { useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Button from '@/Components/Button.vue';

const props = defineProps({
    roundId: {
        type: Number,
        required: true,
    },
    questionId: {
        type: Number,
        required: true,
    },
    consolidatedAnswer: {
        type: String,
        default: null,
    },
    canManage: {
        type: Boolean,
        default: false,
    }
});

const { t } = useI18n();

const form = useForm({
    question_id: props.questionId,
    final_answer: props.consolidatedAnswer || '',
});

const selectAnswer = (ans) => {
    console.log("SELECT ANSWER CLICKED:", ans, "questionId:", props.questionId, "roundId:", props.roundId, "canManage:", props.canManage);
    if (!props.canManage) return;
    
    form.question_id = props.questionId;
    form.final_answer = ans;
    form.post(route('rounds.consolidate.store', props.roundId), {
        preserveScroll: true,
    });
};

const resetConsolidation = () => {
    if (!props.canManage) return;
    
    router.delete(route('rounds.consolidate.destroy', { round: props.roundId, questionId: props.questionId }), {
        preserveScroll: true,
        onSuccess: () => {
            form.final_answer = '';
        }
    });
};
</script>

<template>
    <div class="p-4 bg-white rounded-2xl border border-surface-200 shadow-sm space-y-4">
        <div>
            <h4 class="text-xs font-bold text-surface-500 uppercase tracking-wider mb-1">
                {{ t('review.resolution_model_owner', 'Resolução: Decisão do Dono') }}
            </h4>
            <p class="text-[11px] text-surface-400">
                {{ canManage 
                    ? t('review.resolution_owner_instructions', 'Como dono do projeto, você tem o poder de decidir a nota final para esta questão.') 
                    : t('review.resolution_owner_nonowner_info', 'O dono do projeto definirá a resposta consolidada final.') }}
            </p>
        </div>

        <!-- Decision buttons (Owner Only) -->
        <div v-if="canManage" class="space-y-3">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                <button
                    v-for="opt in ['high', 'medium', 'low', 'other']"
                    :key="opt"
                    type="button"
                    @click="selectAnswer(opt)"
                    class="py-2 px-3 rounded-xl border text-xs font-bold transition-all uppercase tracking-wider flex flex-col items-center justify-center gap-1"
                    :class="consolidatedAnswer === opt
                        ? (opt === 'high' ? 'bg-emerald-50 border-emerald-300 text-emerald-700 shadow-sm' :
                           opt === 'medium' ? 'bg-amber-50 border-amber-300 text-amber-700 shadow-sm' :
                           opt === 'low' ? 'bg-rose-50 border-rose-300 text-rose-700 shadow-sm' :
                           'bg-surface-50 border-surface-300 text-surface-700 shadow-sm')
                        : 'bg-white border-surface-200 text-surface-600 hover:bg-surface-50 hover:border-surface-300'"
                >
                    <span class="text-[9px] opacity-70">
                        {{ opt === 'high' ? '100 pts' : opt === 'medium' ? '50 pts' : opt === 'low' ? '0 pts' : 'N/A' }}
                    </span>
                    <span>
                        {{ t(`labels.answer_level.${opt}`, opt) }}
                    </span>
                </button>
            </div>

            <!-- Reset Button -->
            <div v-if="consolidatedAnswer" class="flex justify-end pt-2 border-t border-surface-100">
                <Button
                    type="button"
                    variant="outline"
                    size="xs"
                    @click="resetConsolidation"
                    class="text-surface-500 hover:text-red-600 hover:border-red-200 transition-colors"
                >
                    {{ t('review.reset_consolidation', 'Desfazer Consolidação (Usar Média)') }}
                </Button>
            </div>
        </div>

        <!-- Display for non-owners -->
        <div v-else class="flex items-center gap-2 p-3 bg-surface-50 rounded-xl border border-surface-100">
            <svg class="w-4 h-4 text-surface-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            <div class="text-[11px]">
                <span class="font-bold text-surface-700 block">
                    {{ consolidatedAnswer 
                        ? t('review.resolution_already_decided', 'Nota definida pelo Dono:') + ' ' + t(`labels.answer_level.${consolidatedAnswer}`, consolidatedAnswer)
                        : t('review.resolution_pending', 'Aguardando decisão final do Dono.') }}
                </span>
            </div>
        </div>
    </div>
</template>
