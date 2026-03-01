<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    round: {
        type: Object,
        required: true,
    },
});

const user = usePage().props.auth.user;
const canManage = props.round.project.owner_id === user.id;

const isDraft = computed(() => props.round.status === 'draft');
const isClosed = computed(() => props.round.status === 'closed');

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString('pt-BR');
};

const createInspection = () => {
    router.post(route('inspections.store', props.round.project_id), {
        evaluation_round_id: props.round.id
    });
};

const closeForm = useForm({});

const isClosingConfirmOpen = ref(false);
const isPublishingModalOpen = ref(false);

const publishForm = useForm({
    visibility: props.round.public_directory?.visibility || 'private',
});

const openPublishModal = () => {
    isPublishingModalOpen.value = true;
};

const closePublishModal = () => {
    isPublishingModalOpen.value = false;
};

const submitPublication = () => {
    if (props.round.public_directory) {
        publishForm.put(route('rounds.publications.update', props.round.id), {
            onSuccess: () => closePublishModal(),
        });
    } else {
        publishForm.post(route('rounds.publish', props.round.id), {
            onSuccess: () => closePublishModal(),
        });
    }
};

const revokePublication = () => {
    publishForm.delete(route('rounds.publications.destroy', props.round.id), {
        onSuccess: () => closePublishModal(),
    });
};
const translateStatus = (status) => {
    const map = {
        'draft': 'Rascunho',
        'active': 'Ativa',
        'closed': 'Concluída',
    };
    return map[status] || status;
};

</script>

<template>
    <Head :title="round.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-start gap-4">
                <div class="flex-grow">
                    <Breadcrumbs :items="[
                        { label: 'Workspace', url: route('projects.index') },
                        { label: round.project.name, url: route('projects.show', round.project.id) },
                        { label: round.name }
                    ]" />
                    
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-semibold text-surface-900 tracking-tight">
                            {{ round.name }}
                        </h2>
                        <Badge :variant="isClosed ? 'success' : (round.status === 'active' ? 'brand' : 'surface')">
                            {{ isClosed ? 'Concluída' : (round.status === 'active' ? 'Ativa' : 'Rascunho') }}
                        </Badge>
                        <Badge v-if="round.public_directory" variant="primary">
                            Publicado ({{ round.public_directory.visibility }})
                        </Badge>
                    </div>
                    <p class="text-sm text-surface-500 mt-1">
                        Gerenciamento da rodada de avaliação técnica e conformidade.
                    </p>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <Button v-if="!isClosed && canManage" variant="primary" @click="createInspection">
                        Nova Inspeção
                    </Button>
                    <Button v-if="!isClosed && canManage" variant="danger" @click="$inertia.get(route('rounds.review', round.id))">
                        Fechar Rodada
                    </Button>
                    <Button v-if="isClosed && canManage" :variant="round.public_directory ? 'primary' : 'outline'" @click="openPublishModal">
                        {{ round.public_directory ? 'Ajustar Publicação' : 'Publicar no Diretório' }}
                    </Button>
                    <Button v-if="isClosed" variant="outline" class="!bg-brand-50 !text-brand-700 !border-brand-100" @click="$inertia.get(route('rounds.results', round.id))">
                         Resultado Consolidado 
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-6">
                        <Card title="Inspeções da Rodada">
                            <div v-if="round.inspections.length === 0" class="py-12 text-center text-surface-500">
                                Não há inspeções nesta rodada ainda.
                                <br><br>
                                <Button v-if="!isClosed && canManage" variant="outline" size="sm" @click="createInspection">
                                    Criar Primeira Inspeção
                                </Button>
                            </div>
                            
                            <div v-if="round.inspections.length > 0">
                                <ul class="divide-y divide-surface-100">
                                    <li v-for="inspection in round.inspections" :key="inspection.id" 
                                        class="py-4 flex justify-between items-center hover:bg-surface-50 transition-colors px-4 -mx-4 rounded-lg group"
                                    >
                                        <div class="cursor-pointer flex-grow" @click="$inertia.get(route('inspections.show', inspection.id))">
                                            <p class="text-sm font-medium text-surface-900">
                                                Inspeção #{{ inspection.sequential_id }}
                                            </p>
                                            <p class="text-xs text-surface-500">
                                                Responsável: {{ inspection.user?.name || 'Sistema' }} • Status: {{ translateStatus(inspection.status) }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <Badge :variant="inspection.status === 'closed' ? 'success' : 'surface'">
                                                {{ translateStatus(inspection.status) }}
                                            </Badge>
                                            <Button size="xs" variant="ghost" @click.stop="$inertia.get(route('inspections.show', inspection.id))">
                                                Ver
                                            </Button>
                                        </div>
                                    </li>
                                </ul>

                                <div v-if="round.inspections.some(i => i.status === 'closed')" class="mt-6 pt-6 border-t border-surface-100 flex justify-end">
                                    <Button 
                                        variant="outline" 
                                        class="!bg-brand-50 !text-brand-700 !border-brand-100" 
                                        @click="$inertia.get(route('results.team', round.inspections.filter(i => i.status === 'closed').sort((a,b) => b.id - a.id)[0].id))"
                                    >
                                        Resultado Equipe
                                    </Button>
                                </div>
                            </div>
                        </Card>

                        <!-- Diagnosis Card (Moved) -->
                        <Card v-if="round.diagnosis" class="border-brand-100 shadow-sm overflow-hidden" title="Diagnóstico Técnico e Análise Geral">
                            <div class="p-6 text-sm text-surface-700 prose prose-brand max-w-none whitespace-pre-wrap">{{ round.diagnosis }}</div>
                        </Card>


                    </div>

                    <div class="space-y-6">
                        <Card title="Informações da Rodada">
                            <div class="space-y-4 px-6 py-4">
                                <div>
                                    <span class="block text-xs font-semibold text-surface-500 uppercase">Projeto</span>
                                    <Link :href="route('projects.show', round.project.id)" class="text-brand-600 hover:underline text-sm">
                                        {{ round.project.name }}
                                    </Link>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-surface-500 uppercase">Data de Criação</span>
                                    <span class="text-surface-900 text-sm">{{ formatDate(round.created_at) }}</span>
                                </div>
                                <div v-if="round.closed_at">
                                    <span class="block text-xs font-semibold text-surface-500 uppercase">Data de Fechamento</span>
                                    <span class="text-surface-900 text-sm">{{ formatDate(round.closed_at) }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-surface-500 uppercase">Inspeções</span>
                                    <span class="text-surface-900 text-sm">{{ round.inspections.length }} vinculadas</span>
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>

    <ConfirmModal
        :show="isPublishingModalOpen"
        title="Publicar Rodada no Diretório"
        :message="round.public_directory 
            ? 'Atualize a visibilidade da publicação ou remova-a do diretório público.' 
            : 'Esta rodada consolidada será publicada no diretório público. Escolha o nível de visibilidade.'"
        :confirm-text="round.public_directory ? 'Atualizar' : 'Publicar'"
        cancel-text="Cancelar"
        :confirm-variant="round.public_directory ? 'brand' : 'primary'"
        @confirm="submitPublication"
        @close="closePublishModal"
    >
        <template #default>
            <div class="mt-4 space-y-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-2">Visibilidade Pública</label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'private'}">
                            <input type="radio" value="private" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">Privado</span>
                                <span class="block text-[10px] text-surface-500 mt-1">Visível apenas internamente no projeto.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'score_public'}">
                            <input type="radio" value="score_public" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">Apenas Score (Consolidado)</span>
                                <span class="block text-[10px] text-surface-500 mt-1">Exibe a pontuação média e medalha no diretório. Detalhes técnicos ocultos.</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'full_public'}">
                            <input type="radio" value="full_public" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">Relatório Consolidado Completo</span>
                                <span class="block text-[10px] text-surface-500 mt-1">Torna o diagnóstico e as médias por seção visíveis publicamente.</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div v-if="round.public_directory" class="pt-4 border-t border-surface-100 flex justify-center">
                    <button type="button" @click="revokePublication" class="text-xs text-red-600 hover:text-red-800 font-medium underline">
                        Remover do Diretório Público
                    </button>
                </div>
            </div>
        </template>
    </ConfirmModal>
</template>
