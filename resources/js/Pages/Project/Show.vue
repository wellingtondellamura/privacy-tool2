<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
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

const user = usePage().props.auth.user;
const { t } = useI18n();

const isRoundCreating = ref(false);
const roundForm = useForm({
    name: t('project.round_default_name', { date: new Date().toLocaleDateString(t('common.locale_code')) }),
});

const openRoundCreateModal = () => {
    roundForm.reset();
    roundForm.name = t('project.round_default_name', { date: new Date().toLocaleDateString(t('common.locale_code')) });
    isRoundCreating.value = true;
};

const submitRoundCreate = () => {
    roundForm.post(route('projects.rounds.store', props.project.id), {
        onSuccess: () => {
            isRoundCreating.value = false;
        },
    });
};

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

const canManageMembers = props.project.owner_id === user.id;

// We format dates
const selectedRounds = ref([]);

const toggleRoundSelection = (roundId) => {
    const index = selectedRounds.value.indexOf(roundId);
    if (index > -1) {
        selectedRounds.value.splice(index, 1);
    } else {
        if (selectedRounds.value.length < 2) {
            selectedRounds.value.push(roundId);
        } else {
            // Replace the oldest selection if we already have 2
            selectedRounds.value.shift();
            selectedRounds.value.push(roundId);
        }
    }
};

const canCompare = computed(() => selectedRounds.value.length === 2);

const compareSelectedRounds = () => {
    if (!canCompare.value) return;
    // Route: rounds.comparison {round} {other}
    router.get(route('rounds.comparison', {
        round: selectedRounds.value[0],
        other: selectedRounds.value[1]
    }));
};

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString(t('common.locale_code'));
};

const translateRole = (role) => {
    const roles = {
        'owner': t('workspace.role_owner'),
        'evaluator': t('workspace.role_evaluator'),
        'observer': t('workspace.role_observer'),
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

const latestClosedInspection = computed(() => {
    return props.project.inspections.find(i => i.status === 'closed');
});

// Publication Logic
const isPublishingModalOpen = ref(false);
const currentInspectionToPublish = ref(null);

const publishForm = useForm({
    visibility: 'private',
});

const openPublishModal = (inspection) => {
    currentInspectionToPublish.value = inspection;
    publishForm.visibility = inspection.publication?.visibility || 'private';
    isPublishingModalOpen.value = true;
};

const closePublishModal = () => {
    isPublishingModalOpen.value = false;
    currentInspectionToPublish.value = null;
    publishForm.reset();
};

const submitPublication = () => {
    if (currentInspectionToPublish.value.publication) {
        publishForm.put(route('inspections.publications.update', currentInspectionToPublish.value.id), {
            onSuccess: () => closePublishModal(),
        });
    } else {
        publishForm.post(route('inspections.publish', currentInspectionToPublish.value.id), {
            onSuccess: () => closePublishModal(),
        });
    }
};

const revokePublication = () => {
    publishForm.delete(route('inspections.publications.destroy', currentInspectionToPublish.value.id), {
        onSuccess: () => closePublishModal(),
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
                        { label: $t('nav.workspace'), url: route('projects.index') },
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
                        <p class="text-sm text-surface-500 max-w-2xl mt-1">{{ project.description || $t('project.no_description') }}</p>
                    </div>

                    <div v-else class="mt-4 space-y-4 max-w-2xl bg-surface-50 p-6 rounded-xl border border-surface-200 shadow-inner">
                        <div class="space-y-4">
                            <Input
                                :label="$t('project.edit_name')"
                                v-model="projectForm.name"
                                :error="projectForm.errors.name"
                                required
                            />
                            <div class="space-y-1">
                                <label class="block text-sm font-medium text-surface-700">{{ $t('project.edit_description') }}</label>
                                <textarea
                                    v-model="projectForm.description"
                                    rows="3"
                                    class="block w-full rounded-lg border-surface-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm"
                                    :placeholder="$t('project.edit_description_placeholder')"
                                ></textarea>
                                <p v-if="projectForm.errors.description" class="text-xs text-red-600 mt-1">{{ projectForm.errors.description }}</p>
                            </div>
                            <Input
                                :label="$t('project.edit_url')"
                                v-model="projectForm.url"
                                :error="projectForm.errors.url"
                                :placeholder="$t('project.edit_url_placeholder')"
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
                                {{ $t('project.save_changes') }}
                            </Button>
                            <Button size="sm" variant="ghost" @click="toggleProjectEdit">
                                {{ $t('common.cancel') }}
                            </Button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <a :href="route('projects.export', project.id)" target="_blank">
                        <Button variant="outline">
                            {{ $t('project.export_json') }}
                        </Button>
                    </a>
                    <Button v-if="project.evaluation_rounds.find(r => r.status === 'closed')" variant="outline" class="!bg-brand-50 !text-brand-700 !border-brand-100" @click="$inertia.get(route('rounds.results', project.evaluation_rounds.find(r => r.status === 'closed').id))">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                        {{ $t('project.consolidated_result') }}
                    </Button>
                    <Button v-if="canCompare" variant="outline" class="!border-brand-200 !text-brand-700" @click="compareSelectedRounds">
                        {{ $t('project.compare_rounds', { count: selectedRounds.length }) }}
                    </Button>
                    <Button v-if="canManageMembers" variant="outline" @click="openRoundCreateModal">
                        {{ $t('project.new_round') }}
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Esquerda: Inspeções e Detalhes da Avaliação -->
                    <div class="md:col-span-2 space-y-6">
                        <Card :title="$t('project.rounds_title')">
                            <div v-if="project.evaluation_rounds.length === 0" class="py-12 text-center text-surface-500">
                                {{ $t('project.no_rounds') }}
                                <br><br>
                                <Button v-if="canManageMembers" variant="outline" size="sm" @click="openRoundCreateModal">
                                    {{ $t('project.create_first_round') }}
                                </Button>
                            </div>
                            
                            <ul v-else class="divide-y divide-surface-100">
                                <li v-for="round in project.evaluation_rounds" :key="round.id" 
                                    class="py-4 flex justify-between items-center hover:bg-surface-50 transition-colors px-4 -mx-4 rounded-lg group"
                                    :class="{'bg-brand-50/50': selectedRounds.includes(round.id)}"
                                >
                                    <div class="flex items-center gap-4 flex-grow">
                                        <div v-if="round.status === 'closed'" class="shrink-0">
                                            <input 
                                                type="checkbox" 
                                                :checked="selectedRounds.includes(round.id)"
                                                @click.stop="toggleRoundSelection(round.id)"
                                                class="rounded border-surface-300 text-brand-600 focus:ring-brand-500 cursor-pointer"
                                            />
                                        </div>
                                        <div class="cursor-pointer flex-grow" @click="$inertia.get(route('rounds.show', round.id))">
                                            <div class="flex items-center gap-2">
                                                <p class="text-sm font-medium" :class="selectedRounds.includes(round.id) ? 'text-brand-900' : 'text-surface-900'">
                                                    {{ round.name }}
                                                </p>
                                                <Badge :variant="round.status === 'closed' ? 'success' : (round.status === 'active' ? 'brand' : 'surface')">
                                                    {{ round.status === 'closed' ? $t('round.status_closed') : (round.status === 'active' ? $t('round.status_active') : $t('round.status_draft')) }}
                                                </Badge>
                                                <Badge v-if="round.public_directory" variant="primary" class="text-[10px]">
                                                    {{ $t('round.published_badge', { visibility: round.public_directory.visibility }) }}
                                                </Badge>
                                            </div>
                                            <p class="text-xs text-surface-500">
                                                {{ $t('project.created_at', { date: formatDate(round.created_at) }) }}
                                                <span v-if="round.closed_at"> • {{ $t('project.closed_at', { date: formatDate(round.closed_at) }) }}</span>
                                                <span v-if="round.snapshots?.[0]" class="ml-2 font-medium text-brand-600">
                                                    • Score: {{ round.snapshots[0].payload_json.global_score }}%
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <Button 
                                            size="sm" 
                                            variant="ghost" 
                                            @click.stop="$inertia.get(route('rounds.show', round.id))"
                                        >
                                            {{ $t('common.open') }}
                                        </Button>
                                    </div>
                                </li>
                            </ul>
                        </Card>

                        <Card :title="$t('project.inspections_title')" collapsible="true" collapsed="true">
                            <div v-if="project.inspections.length === 0" class="py-12 text-center text-surface-500">
                                {{ $t('project.no_inspections') }}
                                <br><br>
                                <p class="text-xs">{{ $t('project.no_inspections_description') }}</p>
                            </div>
                            
                            <ul v-else class="divide-y divide-surface-100">
                                <li v-for="inspection in project.inspections" :key="inspection.id" 
                                    class="py-4 flex justify-between items-center hover:bg-surface-50 transition-colors px-4 -mx-4 rounded-lg group"
                                >
                                    <div class="cursor-pointer flex-grow" @click="$inertia.get(route('inspections.show', inspection.id))">
                                        <p class="text-sm font-medium text-surface-900">
                                            {{ $t('project.inspection_label', { id: inspection.sequential_id }) }}
                                        </p>
                                        <p class="text-xs text-surface-500">
                                            {{ $t('project.created_at', { date: formatDate(inspection.created_at) }) }} • <span class="font-medium text-surface-700">{{ $t('project.responsible_label', { name: inspection.user?.name || 'Sistema' }) }}</span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div v-if="inspection.status === 'closed'" class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <Button 
                                                size="xs" 
                                                variant="outline" 
                                                @click.stop="$inertia.get(route('results.individual', inspection.id))"
                                                :title="$t('project.my_result')"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                            </Button>
                                            <Button 
                                                size="xs" 
                                                variant="outline" 
                                                class="!bg-brand-50 !text-brand-700 !border-brand-100" 
                                                @click.stop="$inertia.get(route('results.team', inspection.id))"
                                                :title="$t('project.consolidated_result')"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                                            </Button>                                            
                                        </div>
                                        <Badge :variant="inspection.status === 'closed' ? 'success' : (inspection.status === 'active' ? 'brand' : 'surface')">
                                            {{ inspection.status === 'closed' ? $t('round.status_closed') : (inspection.status === 'active' ? $t('round.status_active') : $t('round.status_draft')) }}
                                        </Badge>
                                    </div>
                                </li>
                            </ul>
                        </Card>
                    </div>

                    <!-- Direita: Membros e Info do Projeto -->
                    <div class="space-y-6">
                        <Card :title="$t('project.members_title')">
                            <template #header v-if="canManageMembers">
                                <div class="flex justify-between items-center px-6 border-surface-100 bg-surface-50/50">
                                    <h3 class="text-lg font-medium text-surface-900">{{ $t('project.members_title') }}</h3>
                                    <Button size="sm" variant="outline" @click="isInviting = !isInviting">
                                        {{ $t('project.invite') }}
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
                                            :label="$t('project.invite_email_label')"
                                            id="invite-email"
                                            type="email"
                                            v-model="inviteForm.email"
                                            required
                                            :error="inviteForm.errors.email"
                                            :placeholder="$t('project.invite_email_placeholder')"
                                        />
                                        <div>
                                            <label class="block text-sm font-medium text-surface-700 mb-1">
                                                {{ $t('project.invite_role_label') }}
                                            </label>
                                            <select v-model="inviteForm.role" class="block w-full rounded-lg border-surface-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm">
                                                <option value="evaluator">{{ $t('workspace.role_evaluator') }}</option>
                                                <option value="observer">{{ $t('workspace.role_observer') }}</option>
                                            </select>
                                        </div>
                                        <div class="flex justify-end pt-2">
                                            <Button type="button" variant="ghost" size="sm" @click="isInviting = false" class="mr-2">
                                                {{ $t('common.cancel') }}
                                            </Button>
                                            <Button type="submit" variant="primary" size="sm" :disabled="inviteForm.processing">
                                                {{ $t('project.send_invitation') }}
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
                                            <option value="evaluator">{{ $t('workspace.role_evaluator') }}</option>
                                            <option value="observer">{{ $t('workspace.role_observer') }}</option>
                                        </select>
                                        <Badge v-else :variant="member.role === 'owner' ? 'brand' : 'surface'">
                                            {{ translateRole(member.role) }}
                                        </Badge>
                                    </div>
                                </li>
                            </ul>

                            <div v-if="project.invitations && project.invitations.length > 0" class="border-t border-surface-100 bg-surface-50/30">
                                <h4 class="text-xs font-semibold text-surface-500 uppercase tracking-wider px-6 py-3 bg-surface-50 border-b border-surface-100">{{ $t('project.pending_invitations') }}</h4>
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
                                                            {{ $t('project.user_has_account') }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <span class="text-[10px] text-brand-600 bg-brand-50 px-1.5 py-0.5 rounded border border-brand-100">
                                                    {{ $t('project.invitation_sent_at', { date: formatDate(invitation.created_at) }) }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex flex-col items-end gap-1">
                                            <Badge variant="surface" class="!text-[10px]">
                                                {{ translateRole(invitation.role) }}
                                            </Badge>
                                            <div class="flex items-center gap-2 mt-1">
                                                <button v-if="canManageMembers && new Date(invitation.expires_at) < new Date()" @click="resendInvitation(invitation.id)" class="text-[10px] text-brand-600 hover:text-brand-800 underline font-medium">
                                                    {{ $t('project.resend') }}
                                                </button>
                                                <span v-if="new Date(invitation.expires_at) < new Date()" class="text-[10px] text-red-500 font-medium">
                                                    {{ $t('project.expired') }}
                                                </span>
                                                <span v-else class="text-[10px] text-surface-400">
                                                    {{ $t('project.pending') }}
                                                </span>
                                            </div>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </Card>
                        
                        <div v-if="project.url" class="p-4 bg-white rounded-xl border border-surface-200 shadow-sm flex items-center justify-between">
                            <div class="text-sm">
                                <span class="block font-medium text-surface-900">{{ $t('project.product_url') }}</span>
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
        :title="$t('project.resend_modal_title')"
        :message="$t('project.resend_modal_message')"
        :confirm-text="$t('project.resend_confirm')"
        :cancel-text="$t('common.cancel')"
        confirm-variant="brand"
        @confirm="confirmResend"
        @close="isResendingConfirm = false"
    />

    <!-- Modal de Criação de Rodada -->
    <ConfirmModal
        :show="isRoundCreating"
        :title="$t('project.new_round_modal_title')"
        :message="$t('project.new_round_modal_message')"
        :confirm-text="$t('project.new_round_create')"
        :cancel-text="$t('common.cancel')"
        confirm-variant="primary"
        :processing="roundForm.processing"
        @confirm="submitRoundCreate"
        @close="isRoundCreating = false"
    >
        <template #default>
            <div class="mt-4">
                <Input
                    :label="$t('project.round_name_label')"
                    v-model="roundForm.name"
                    :error="roundForm.errors.name"
                    required
                    :placeholder="$t('project.round_name_placeholder')"
                    @keyup.enter="submitRoundCreate"
                />
            </div>
        </template>
    </ConfirmModal>

    <!-- Modal de Publicação -->
    <ConfirmModal
        :show="isPublishingModalOpen"
        :title="$t('publication.modal_title')"
        :message="currentInspectionToPublish?.publication 
            ? $t('publication.update_message') 
            : $t('publication.create_inspection_message')"
        :confirm-text="currentInspectionToPublish?.publication ? $t('publication.update_button') : $t('publication.publish_button')"
        :cancel-text="$t('common.cancel')"
        :confirm-variant="currentInspectionToPublish?.publication ? 'brand' : 'primary'"
        @confirm="submitPublication"
        @close="closePublishModal"
    >
        <template #default>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-2">{{ $t('publication.visibility_engagement') }}</label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'private'}">
                            <input type="radio" value="private" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">{{ $t('publication.private_label') }}</span>
                                <span class="block text-[10px] text-surface-500 mt-1">{{ $t('publication.private_members_description') }}</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'score_public'}">
                            <input type="radio" value="score_public" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">{{ $t('publication.score_short_label') }}</span>
                                <span class="block text-[10px] text-surface-500 mt-1">{{ $t('publication.score_short_description') }}</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'full_public'}">
                            <input type="radio" value="full_public" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">{{ $t('publication.full_short_label') }}</span>
                                <span class="block text-[10px] text-surface-500 mt-1">{{ $t('publication.full_short_description') }}</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div v-if="currentInspectionToPublish?.publication" class="pt-4 border-t border-surface-100 flex justify-center">
                    <button type="button" @click="revokePublication" class="text-xs text-red-600 hover:text-red-800 font-medium underline">
                        {{ $t('publication.remove_link') }}
                    </button>
                </div>
            </div>
        </template>
    </ConfirmModal>
</template>
