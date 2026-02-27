<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Input from '@/Components/Input.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ProjectIcon from '@/Components/ProjectIcon.vue';
import ProjectIconPicker from '@/Components/ProjectIconPicker.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    project: {
        type: Object,
        required: true,
    },
});

const isInviting = ref(false);
const isProjectEditing = ref(false);
const isResendingConfirm = ref(false);
const resendingInvitationId = ref(null);

const inviteForm = useForm({
    email: '',
    role: 'evaluator',
});

const projectForm = useForm({
    name: props.project.name,
    description: props.project.description,
    url: props.project.url,
    icon: props.project.icon,
    color: props.project.color,
});

const toggleProjectEdit = () => {
    isProjectEditing.value = !isProjectEditing.value;
    if (!isProjectEditing.value) {
        projectForm.reset();
    }
};

const updateProject = () => {
    projectForm.put(route('projects.update', props.project.id), {
        onSuccess: () => {
            isProjectEditing.value = false;
        },
    });
};

const submitInvite = () => {
    inviteForm.post(route('projects.invite', props.project.id), {
        onSuccess: () => {
            isInviting.value = false;
            inviteForm.reset();
        },
    });
};

const user = usePage().props.auth.user;

const canManageMembers = props.project.owner_id === user.id;

// We format dates
const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('pt-BR');
};

const translateRole = (role) => {
    const roles = {
        'owner': 'Proprietário',
        'evaluator': 'Avaliador',
        'observer': 'Observador',
    };
    return roles[role] || role;
};

const updateRole = (userId, newRole) => {
    router.put(route('projects.members.update', [props.project.id, userId]), {
        role: newRole
    }, {
        preserveScroll: true
    });
};

const resendInvitation = (invitationId) => {
    resendingInvitationId.value = invitationId;
    isResendingConfirm.value = true;
};

const confirmResend = () => {
    router.post(route('invitations.resend', resendingInvitationId.value), {}, {
        preserveScroll: true,
        onSuccess: () => {
            isResendingConfirm.value = false;
            resendingInvitationId.value = null;
        }
    });
};
</script>

<template>
    <Head :title="project.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-start gap-4">
                <div class="flex-grow">
                    <Breadcrumbs :items="[
                        { label: 'Workspace', url: route('projects.index') },
                        { label: project.name }
                    ]" />
                    
                    <div v-if="!isProjectEditing">
                        <div class="flex items-center gap-3">
                            <ProjectIcon :icon="project.icon" :color="project.color" size="md" />
                            <h2 class="text-2xl font-semibold text-surface-900 tracking-tight">
                                {{ project.name }}
                            </h2>
                            <button v-if="canManageMembers" @click="toggleProjectEdit" class="text-surface-400 hover:text-brand-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-surface-500 max-w-2xl mt-1">{{ project.description || 'Nenhuma descrição fornecida.' }}</p>
                    </div>

                    <div v-else class="mt-4 space-y-4 max-w-2xl bg-surface-50 p-6 rounded-xl border border-surface-200 shadow-inner">
                        <div class="space-y-4">
                            <Input
                                label="Nome do Projeto"
                                v-model="projectForm.name"
                                :error="projectForm.errors.name"
                                required
                            />
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-surface-700">Descrição</label>
                                <textarea
                                    v-model="projectForm.description"
                                    rows="3"
                                    class="block w-full rounded-lg border-surface-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm"
                                    placeholder="Breve descrição do projeto..."
                                ></textarea>
                                <p v-if="projectForm.errors.description" class="text-xs text-red-600 mt-1">{{ projectForm.errors.description }}</p>
                            </div>
                            <Input
                                label="Website (URL)"
                                v-model="projectForm.url"
                                :error="projectForm.errors.url"
                                placeholder="https://..."
                            />
                            <ProjectIconPicker
                                v-model:icon="projectForm.icon"
                                v-model:color="projectForm.color"
                                :error-icon="projectForm.errors.icon"
                                :error-color="projectForm.errors.color"
                                class="py-2"
                            />
                        </div>
                        <div class="flex items-center gap-2 pt-2">
                            <Button size="sm" variant="primary" @click="updateProject" :disabled="projectForm.processing">
                                Salvar Alterações
                            </Button>
                            <Button size="sm" variant="ghost" @click="toggleProjectEdit">
                                Cancelar
                            </Button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <a :href="route('projects.export', project.id)" target="_blank">
                        <Button variant="outline">
                            Exportar JSON
                        </Button>
                    </a>
                    <Button variant="primary" @click="$inertia.post(route('inspections.store', project.id))">
                        Nova Inspeção
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Esquerda: Inspeções e Detalhes da Avaliação -->
                    <div class="md:col-span-2 space-y-6">
                        <Card title="Inspeções">
                            <div v-if="project.inspections.length === 0" class="py-12 text-center text-surface-500">
                                Não há inspeções criadas para este projeto ainda.
                                <br><br>
                                <Button variant="outline" size="sm" @click="$inertia.post(route('inspections.store', project.id))">
                                    Criar Primeira Inspeção
                                </Button>
                            </div>
                            
                            <ul v-else class="divide-y divide-surface-100">
                                <li v-for="inspection in project.inspections" :key="inspection.id" class="py-4 flex justify-between items-center hover:bg-surface-50 transition-colors px-4 -mx-4 rounded-lg cursor-pointer" @click="$inertia.get(route('inspections.show', inspection.id))">
                                    <div>
                                        <p class="text-sm font-medium text-surface-900">
                                            Inspeção #{{ inspection.id }}
                                        </p>
                                        <p class="text-xs text-surface-500">
                                            Criada em {{ formatDate(inspection.created_at) }} • <span class="font-medium text-surface-700">Responsável: {{ inspection.user?.name || 'Sistema' }}</span>
                                        </p>
                                    </div>
                                    <div>
                                        <Badge :variant="inspection.status === 'closed' ? 'success' : (inspection.status === 'active' ? 'brand' : 'surface')">
                                            {{ inspection.status === 'closed' ? 'Concluída' : (inspection.status === 'active' ? 'Ativa' : 'Rascunho') }}
                                        </Badge>
                                    </div>
                                </li>
                            </ul>
                        </Card>
                    </div>

                    <!-- Direita: Membros e Info do Projeto -->
                    <div class="space-y-6">
                        <Card title="Membros do Projeto">
                            <template #header v-if="canManageMembers">
                                <div class="flex justify-between items-center px-6 py-5 border-b border-surface-100 bg-surface-50/50">
                                    <h3 class="text-lg font-medium text-surface-900">Membros</h3>
                                    <Button size="sm" variant="outline" @click="isInviting = !isInviting">
                                        Convidar
                                    </Button>
                                </div>
                            </template>

                            <transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="transform opacity-0 -translate-y-2"
                                enter-to-class="transform opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-150"
                                leave-from-class="transform opacity-100 translate-y-0"
                                leave-to-class="transform opacity-0 -translate-y-2"
                            >
                                <div v-if="isInviting" class="mb-4 bg-brand-50 p-4 rounded-lg border border-brand-100">
                                    <form @submit.prevent="submitInvite" class="space-y-3">
                                        <Input
                                            label="E-mail"
                                            id="invite-email"
                                            type="email"
                                            v-model="inviteForm.email"
                                            required
                                            :error="inviteForm.errors.email"
                                            placeholder="email@exemplo.com"
                                        />
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 mb-1">
                                                Papel
                                            </label>
                                            <select v-model="inviteForm.role" class="block w-full rounded-lg border-surface-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                                                <option value="evaluator">Avaliador</option>
                                                <option value="observer">Observador</option>
                                            </select>
                                        </div>
                                        <div class="flex justify-end pt-2">
                                            <Button type="button" variant="ghost" size="sm" @click="isInviting = false" class="mr-2">
                                                Cancelar
                                            </Button>
                                            <Button type="submit" variant="primary" size="sm" :disabled="inviteForm.processing">
                                                Enviar Convite
                                            </Button>
                                        </div>
                                    </form>
                                </div>
                            </transition>

                            <ul class="divide-y divide-surface-100">
                                <li v-for="member in project.members" :key="member.id" class="py-3 flex items-center justify-between px-6">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-surface-200 flex items-center justify-center text-surface-600 font-bold text-xs ring-2 ring-white">
                                            {{ member.user.name.charAt(0) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-surface-900">{{ member.user.name }}</p>
                                            <p class="text-xs text-surface-500">{{ member.user.email }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <select v-if="canManageMembers && member.role !== 'owner'" 
                                                :value="member.role" 
                                                @change="updateRole(member.user.id, $event.target.value)"
                                                class="text-xs rounded border-surface-200 py-1 pl-2 pr-8 focus:ring-brand-500 focus:border-brand-500 bg-surface-50">
                                            <option value="evaluator">Avaliador</option>
                                            <option value="observer">Observador</option>
                                        </select>
                                        <Badge v-else :variant="member.role === 'owner' ? 'brand' : 'surface'">
                                            {{ translateRole(member.role) }}
                                        </Badge>
                                    </div>
                                </li>
                            </ul>

                            <div v-if="project.invitations && project.invitations.length > 0" class="border-t border-surface-100 bg-surface-50/30">
                                <h4 class="text-xs font-semibold text-surface-500 uppercase tracking-wider px-6 py-3 bg-surface-50 border-b border-surface-100">Convites Pendentes</h4>
                                <ul class="divide-y divide-surface-100">
                                    <li v-for="invitation in project.invitations" :key="invitation.id" class="py-3 flex items-center justify-between px-6 opacity-80 hover:opacity-100 transition-opacity">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full bg-surface-100 border border-dashed border-surface-300 flex items-center justify-center text-surface-500 font-bold text-xs">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                            </div>
                                            <div class="ml-3 flex flex-col items-start gap-1">
                                                <div class="flex items-center gap-2">
                                                    <p class="text-xs font-medium text-surface-900 truncate max-w-[150px]">{{ invitation.email }}</p>
                                                    <div v-if="invitation.has_account" class="group relative flex items-center justify-center">
                                                        <svg class="w-4 h-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        <div class="absolute bottom-full mb-1 min-w-[120px] bg-surface-800 text-white text-[10px] rounded px-2 py-1 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none text-center">
                                                            Usuário já possui conta
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded border border-brand-100">
                                                    Enviado {{ formatDate(invitation.created_at) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <Badge variant="surface" class="!text-[10px]">
                                                {{ translateRole(invitation.role) }}
                                            </Badge>
                                            <div class="flex items-center gap-2 mt-1">
                                                <button v-if="canManageMembers && new Date(invitation.expires_at) < new Date()" @click="resendInvitation(invitation.id)" class="text-[10px] text-brand-600 hover:text-brand-800 underline font-medium">
                                                    Reenviar
                                                </button>
                                                <span v-if="new Date(invitation.expires_at) < new Date()" class="text-[10px] text-red-500 font-medium">
                                                    Expirado
                                                </span>
                                                <span v-else class="text-[10px] text-surface-400">
                                                    Pendente
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </Card>
                        
                        <div v-if="project.url" class="p-4 bg-white rounded-xl border border-surface-200 shadow-sm flex items-center justify-between">
                            <div class="text-sm">
                                <span class="block font-medium text-surface-900">URL do Produto</span>
                                <a :href="project.url" target="_blank" class="text-brand-600 hover:underline truncate max-w-[200px] block">
                                    {{ project.url }}
                                </a>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-surface-400" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                            </svg>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>

    <!-- Modal de Reenvio de Convite -->
    <ConfirmModal
        :show="isResendingConfirm"
        title="Reenviar Convite"
        message="Deseja reenviar este convite? O prazo será estendido por mais 7 dias."
        confirm-text="Sim, Reenviar"
        cancel-text="Cancelar"
        confirm-variant="brand"
        @confirm="confirmResend"
        @close="isResendingConfirm = false"
    />
</template>
