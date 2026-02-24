<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';

defineProps({
    projects: {
        type: Array,
        required: true,
    },
    pendingInvitations: {
        type: Array,
        default: () => [],
    }
});

const isCreatingNode = ref(false);

const form = useForm({
    name: '',
    description: '',
    url: '',
});

const submit = () => {
    form.post(route('projects.store'), {
        onSuccess: () => {
            isCreatingNode.value = false;
            form.reset();
        },
    });
};

const acceptInvitation = (token) => {
    router.post(route('invitations.accept', token), {}, { preserveScroll: true });
};

const declineInvitation = (id) => {
    router.delete(route('invitations.destroy', id), { preserveScroll: true });
};
</script>

<template>
    <Head title="Meu Workspace" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="text-2xl font-semibold text-surface-900 tracking-tight">
                    Meu Workspace
                </h2>
                <Button @click="isCreatingNode = true" variant="primary">
                    Novo Projeto
                </Button>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Create Project Form Form -->
                <transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="transform opacity-0 scale-95"
                    enter-to-class="transform opacity-100 scale-100"
                    leave-active-class="transition ease-in duration-75"
                    leave-from-class="transform opacity-100 scale-100"
                    leave-to-class="transform opacity-0 scale-95"
                >
                    <Card v-if="isCreatingNode" class="mb-8" title="Criar Novo Projeto" description="Um projeto pode ser um software, aplicativo ou site a ser inspecionado.">
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <Input
                                    label="Nome do Projeto"
                                    id="name"
                                    type="text"
                                    v-model="form.name"
                                    required
                                    autofocus
                                    :error="form.errors.name"
                                    placeholder="Sistema XYZ"
                                />
                            </div>
                            
                            <div>
                                <Input
                                    label="Descrição (Opcional)"
                                    id="description"
                                    type="text"
                                    v-model="form.description"
                                    :error="form.errors.description"
                                    placeholder="Breve descrição sobre o contexto..."
                                />
                            </div>

                            <div>
                                <Input
                                    label="URL do Produto (Opcional)"
                                    id="url"
                                    type="url"
                                    v-model="form.url"
                                    :error="form.errors.url"
                                    placeholder="https://exemplo.com"
                                />
                            </div>

                            <div class="flex justify-end gap-3 pt-4">
                                <Button type="button" variant="ghost" @click="isCreatingNode = false">
                                    Cancelar
                                </Button>
                                <Button type="submit" variant="primary" :disabled="form.processing">
                                    Criar Projeto
                                </Button>
                            </div>
                        </form>
                    </Card>
                </transition>

                <!-- Pending Invitations -->
                <div v-if="pendingInvitations?.length > 0" class="mb-8">
                    <h3 class="text-lg font-medium text-surface-900 mb-4 border-b border-surface-200 pb-2">Convites Pendentes</h3>
                    <div class="space-y-4">
                        <Card v-for="invitation in pendingInvitations" :key="invitation.id" class="flex flex-col sm:flex-row sm:items-center justify-between !p-4 border-brand-200 bg-brand-50/30">
                            <div>
                                <h4 class="text-base font-medium text-surface-900 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Projeto: {{ invitation.project.name }}
                                </h4>
                                <p class="text-sm text-surface-500 mt-1 pl-7">
                                    Papel de acesso: <span class="font-medium text-surface-700 capitalize">{{ invitation.role === 'evaluator' ? 'Avaliador' : (invitation.role === 'observer' ? 'Observador' : invitation.role) }}</span>
                                </p>
                            </div>
                            <div class="flex gap-3 mt-4 sm:mt-0">
                                <Button @click="declineInvitation(invitation.id)" variant="ghost" class="!text-red-600 hover:!bg-red-50">
                                    Recusar
                                </Button>
                                <Button @click="acceptInvitation(invitation.token)" variant="primary">
                                    Aceitar Convite
                                </Button>
                            </div>
                        </Card>
                    </div>
                </div>

                <!-- Project List -->
                <div v-if="projects.length === 0 && !isCreatingNode" class="text-center py-20 bg-white rounded-2xl border border-surface-200 shadow-sm">
                    <svg class="mx-auto h-12 w-12 text-surface-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <h3 class="mt-4 text-sm font-medium text-surface-900">Nenhum projeto</h3>
                    <p class="mt-1 text-sm text-surface-500">Comece criando um novo projeto para gerenciar inspeções.</p>
                    <div class="mt-6">
                        <Button @click="isCreatingNode = true" variant="primary">
                            Novo Projeto
                        </Button>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <Card v-for="project in projects" :key="project.id" class="flex flex-col h-full hover:-translate-y-1 transition-transform duration-smooth">
                        <div class="flex-grow">
                            <h3 class="text-lg font-medium text-surface-900 group-hover:text-brand-600 transition-colors">
                                <Link :href="route('projects.show', project.id)" class="focus:outline-none">
                                    <span class="absolute inset-0" aria-hidden="true" />
                                    {{ project.name }}
                                </Link>
                            </h3>
                            <p class="mt-2 text-sm text-surface-500 line-clamp-2">
                                {{ project.description || 'Sem descrição.' }}
                            </p>
                            <p v-if="project.url" class="mt-2 text-xs text-brand-600 truncate">
                                {{ project.url }}
                            </p>
                        </div>
                        <template #footer>
                            <div class="flex items-center justify-between text-xs text-surface-500">
                                <span>{{ project.inspections_count }} inspeções</span>
                                <span>Criado por {{ project.owner.name }}</span>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
