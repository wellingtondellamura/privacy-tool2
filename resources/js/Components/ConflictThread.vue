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
    comments: {
        type: Array,
        default: () => [],
    },
    currentUserId: {
        type: Number,
        required: true,
    },
    isProjectOwner: {
        type: Boolean,
        default: false,
    }
});

const { t } = useI18n();

const form = useForm({
    question_id: props.questionId,
    body: '',
});

const submitComment = () => {
    if (!form.body.trim()) return;
    
    form.post(route('rounds.comments.store', props.roundId), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('body');
        }
    });
};

const deleteComment = (commentId) => {
    if (confirm(t('review.delete_comment_confirm', 'Deseja realmente excluir este comentário?'))) {
        router.delete(route('rounds.comments.destroy', commentId), {
            preserveScroll: true,
        });
    }
};

const formatTime = (timeString) => {
    if (!timeString) return '';
    const date = new Date(timeString);
    return date.toLocaleDateString(t('common.locale_code'), {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <div class="flex flex-col h-full bg-surface-50 rounded-2xl border border-surface-200 overflow-hidden">
        <!-- Header -->
        <div class="px-4 py-3 bg-surface-100/50 border-b border-surface-200 flex items-center justify-between">
            <span class="text-xs font-bold text-surface-600 uppercase tracking-wider">
                {{ t('review.comments_title', 'Discussão / Sensemaking') }}
            </span>
            <span class="px-2 py-0.5 bg-surface-200 text-surface-600 text-[10px] font-bold rounded-full">
                {{ comments.length }}
            </span>
        </div>

        <!-- Comments List -->
        <div class="flex-1 p-4 overflow-y-auto space-y-4 max-h-[300px] min-h-[150px]">
            <div v-if="comments.length === 0" class="h-full flex flex-col items-center justify-center text-center text-xs text-surface-400 py-8">
                <svg class="w-8 h-8 text-surface-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                {{ t('review.no_comments', 'Nenhum comentário enviado ainda. Compartilhe suas observações para buscar consenso.') }}
            </div>

            <div v-for="comment in comments" :key="comment.id" 
                 class="flex flex-col p-3 rounded-xl shadow-sm border text-xs relative group"
                 :class="comment.user_id === currentUserId 
                    ? 'bg-brand-50/40 border-brand-100 ml-6' 
                    : 'bg-white border-surface-200 mr-6'">
                
                <div class="flex items-center justify-between gap-2 mb-1">
                    <span class="font-bold text-surface-800"
                          :class="{ 'text-brand-700': comment.user_id === currentUserId }">
                        {{ comment.user?.name || 'Avaliador' }}
                    </span>
                    <span class="text-[9px] text-surface-400 font-medium">
                        {{ formatTime(comment.created_at) }}
                    </span>
                </div>
                
                <p class="text-surface-700 leading-relaxed whitespace-pre-wrap">{{ comment.body }}</p>
                
                <!-- Action Delete -->
                <button v-if="comment.user_id === currentUserId || isProjectOwner"
                        @click="deleteComment(comment.id)"
                        class="absolute right-2 top-2 opacity-0 group-hover:opacity-100 text-surface-400 hover:text-red-500 transition-opacity p-0.5 rounded hover:bg-surface-100">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </div>

        <!-- Add Comment Form -->
        <form @submit.prevent="submitComment" class="p-3 bg-surface-100 border-t border-surface-200 flex gap-2">
            <textarea
                v-model="form.body"
                rows="1"
                class="flex-grow rounded-xl border border-surface-300 focus:border-brand-500 focus:ring-brand-500 text-xs px-3 py-2 bg-white resize-none"
                :placeholder="t('review.type_comment_placeholder', 'Digite seu comentário...')"
                @keydown.enter.prevent="submitComment"
                :disabled="form.processing"
            ></textarea>
            <Button
                type="submit"
                variant="primary"
                size="sm"
                class="shrink-0"
                :disabled="form.processing || !form.body.trim()"
            >
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </Button>
        </form>
    </div>
</template>
