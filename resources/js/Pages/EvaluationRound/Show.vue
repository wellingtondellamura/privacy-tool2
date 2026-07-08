<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Card from '@/Components/Card.vue';
import Button from '@/Components/Button.vue';
import Badge from '@/Components/Badge.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import Input from '@/Components/Input.vue';

const props = defineProps({
    round: {
        type: Object,
        required: true,
    },
    currentUserRole: {
        type: String,
        default: null,
    },
});

const isEditing = ref(false);

const roundForm = useForm({
    name: props.round.name,
    software_version: props.round.software_version || '',
});

const toggleEdit = () => {
    isEditing.value = !isEditing.value;
    if (!isEditing.value) {
        roundForm.reset();
    }
};

const updateRound = () => {
    roundForm.put(route('rounds.update', props.round.id), {
        onSuccess: () => {
            isEditing.value = false;
        },
    });
};

const user = usePage().props.auth.user;
const { t } = useI18n();
const canManage = props.round.project.owner_id === user.id;

// Bug 4 & 5: role-based computed flags
const isDraft = computed(() => props.round.status === 'draft');
const isReviewing = computed(() => props.round.status === 'review');
const isClosed = computed(() => props.round.status === 'closed');

const isObserver = computed(() => props.currentUserRole === 'observer');
const isEvaluator = computed(() => props.currentUserRole === 'evaluator');

// Evaluators can create 1 inspection per round; check if they already have one
const userAlreadyHasInspection = computed(() =>
    props.round.inspections.some(i => i.user?.id === user.id)
);

// Role-based visibility: owner and observer can view any inspection;
// evaluators can only view their own.
const canViewInspection = (inspection) => {
    if (canManage || isObserver.value) return true;
    return inspection.user?.id === user.id;
};

// Rule: every member (including owner) can create at most 1 inspection per round.
// Observers can never create inspections.
const canCreateInspection = computed(() =>
    isDraft.value && !isObserver.value && !userAlreadyHasInspection.value
);

const formatDate = (dateString) => {
    if (!dateString) return '-';
    return new Date(dateString).toLocaleDateString(t('common.locale_code'));
};

const createInspection = () => {
    router.post(route('inspections.store', props.round.project_id), {
        evaluation_round_id: props.round.id
    });
};

const startReview = () => {
    router.post(route('rounds.enter-review', props.round.id));
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
        'draft': t('round.status_draft'),
        'active': t('round.status_active'),
        'review': t('round.status_review'),
        'closed': t('round.status_closed'),
    };
    return map[status] || status;
};

const isRevokingModalOpen = ref(false);

const confirmRevocation = () => {
    isRevokingModalOpen.value = true;
};

const closeRevokeModal = () => {
    isRevokingModalOpen.value = false;
};

const submitRevocation = () => {
    router.delete(route('badges.destroy', props.round.badge.id), {
        onSuccess: () => closeRevokeModal(),
    });
};

const copyToClipboard = (text) => {
    navigator.clipboard.writeText(text).then(() => {
        alert(t('round.copied_clipboard'));
    });
};

</script>

<template>
    <Head :title="round.name" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-start gap-4">
                <div class="flex-grow">
                    <Breadcrumbs :items="[
                        { label: $t('nav.workspace'), url: route('projects.index') },
                        { label: round.project.name, url: route('projects.show', round.project.id) },
                        { label: round.name }
                    ]" />
                    
                    <div v-if="!isEditing">
                        <div class="flex items-center gap-3">
                            <h2 class="text-2xl font-semibold text-surface-900 tracking-tight">
                                {{ round.name }}
                            </h2>
                            <button v-if="canManage && !isClosed" @click="toggleEdit" class="text-surface-400 hover:text-brand-600 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                </svg>
                            </button>
                             <Badge :variant="isClosed ? 'success' : (isReviewing ? 'warning' : (round.status === 'active' ? 'brand' : 'surface'))">
                                 {{ isClosed ? $t('round.status_closed') : (isReviewing ? $t('round.status_review') : (round.status === 'active' ? $t('round.status_active') : $t('round.status_draft'))) }}
                             </Badge>
                            <Badge v-if="round.public_directory" variant="primary">
                                {{ $t('round.published_badge', { visibility: round.public_directory.visibility }) }}
                            </Badge>
                        </div>
                        <p class="text-xs text-surface-500 mt-1 font-semibold flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" />
                            </svg>
                            <span>
                                {{ round.software_version ? $t('round.software_version_label', { version: round.software_version }) : $t('round.no_software_version') }}
                            </span>
                        </p>
                        <p class="text-sm text-surface-500 mt-2">
                            {{ $t('round.management_description') }}
                        </p>
                    </div>

                    <div v-else class="mt-2 space-y-4 max-w-xl bg-surface-50 p-4 rounded-xl border border-surface-200 shadow-inner">
                        <div class="space-y-4">
                            <Input
                                :label="$t('round.edit_name')"
                                v-model="roundForm.name"
                                :error="roundForm.errors.name"
                                required
                            />
                            <Input
                                :label="$t('round.edit_software_version')"
                                v-model="roundForm.software_version"
                                :error="roundForm.errors.software_version"
                                :placeholder="$t('round.edit_software_version_placeholder')"
                            />
                        </div>
                        <div class="flex items-center gap-2 pt-2">
                            <Button size="sm" variant="primary" @click="updateRound" :disabled="roundForm.processing">
                                {{ $t('common.save') }}
                            </Button>
                            <Button size="sm" variant="ghost" @click="toggleEdit">
                                {{ $t('common.cancel') }}
                            </Button>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <!-- Draft state buttons -->
                    <Button v-if="canCreateInspection" variant="primary" @click="createInspection">
                        {{ $t('round.new_inspection') }}
                    </Button>
                    <Button v-if="isDraft && canManage && round.inspections.some(i => i.status === 'closed')" variant="warning" @click="startReview">
                        {{ $t('round.start_review') }}
                    </Button>
                    <Button v-if="isDraft && canManage" variant="warning" @click="$inertia.get(route('rounds.review', round.id))">
                        {{ $t('round.close_round') }}
                    </Button>

                    <!-- Review state buttons -->
                    <Button v-if="isReviewing" :variant="canManage ? 'warning' : 'outline'" @click="$inertia.get(route('rounds.review', round.id))">
                        {{ $t('round.continue_review') }}
                    </Button>

                    <!-- Closed state buttons -->
                    <Button v-if="isClosed && canManage" :variant="round.public_directory ? 'outline' : 'success'" @click="openPublishModal">
                        {{ round.public_directory ? $t('round.adjust_publication') : $t('round.publish_directory') }}
                    </Button>
                    <Button v-if="isClosed" variant="info" @click="$inertia.get(route('rounds.results', round.id))">
                         {{ $t('round.consolidated_result') }}
                    </Button>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-8">
                

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="md:col-span-2 space-y-6">
                        <Card :title="$t('round.inspections_title')">
                            <div v-if="round.inspections.length === 0" class="py-12 text-center text-surface-500">
                                {{ $t('round.no_inspections') }}
                                <br><br>
                                <Button v-if="canCreateInspection" variant="primary" size="sm" @click="createInspection">
                                    {{ $t('round.create_first') }}
                                </Button>
                                <!-- Observer message when no inspections -->
                                <p v-if="isObserver" class="text-xs text-surface-400 mt-2">
                                    {{ $t('round.observer_no_action') }}
                                </p>
                            </div>
                            
                            <div v-if="round.inspections.length > 0">
                            <ul class="divide-y divide-surface-100">
                                    <li v-for="inspection in round.inspections" :key="inspection.id" 
                                        class="py-4 flex justify-between items-center px-4 -mx-4 rounded-lg transition-colors"
                                        :class="canViewInspection(inspection) ? 'hover:bg-surface-50 cursor-pointer group' : 'opacity-75'"
                                    >
                                        <div 
                                            class="flex-grow"
                                            :class="canViewInspection(inspection) ? 'cursor-pointer' : 'cursor-default'"
                                            @click="canViewInspection(inspection) && $inertia.get(route('inspections.show', inspection.id))"
                                        >
                                            <p class="text-sm font-medium text-surface-900">
                                                {{ $t('round.inspection_label', { id: inspection.sequential_id }) }}
                                            </p>
                                            <p class="text-xs text-surface-500">
                                                {{ $t('round.responsible_status', { name: inspection.user?.name || $t('inspection.responsible_system'), status: translateStatus(inspection.status) }) }}
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <!-- Status Badge with semantic color -->
                                            <Badge :variant="inspection.status">
                                                <span class="flex items-center gap-1">
                                                    <!-- dot indicator -->
                                                    <span class="w-1.5 h-1.5 rounded-full"
                                                        :class="{
                                                            'bg-slate-400': inspection.status === 'draft',
                                                            'bg-blue-500': inspection.status === 'active',
                                                            'bg-emerald-500': inspection.status === 'closed',
                                                        }"
                                                    ></span>
                                                    {{ translateStatus(inspection.status) }}
                                                </span>
                                            </Badge>
                                            <!-- Owner / Observer: always see [Ver] -->
                                            <button
                                                v-if="canViewInspection(inspection)"
                                                @click.stop="$inertia.get(route('inspections.show', inspection.id))"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold rounded-lg border transition-all duration-150
                                                    bg-white border-surface-200 text-surface-600 hover:bg-brand-50 hover:border-brand-300 hover:text-brand-700 shadow-sm hover:shadow"
                                            >
                                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                {{ $t('common.view') }}
                                            </button>
                                            <!-- Evaluator viewing someone else's inspection: restricted indicator -->
                                            <span v-else class="inline-flex items-center gap-1 text-[10px] font-medium text-surface-400 bg-surface-100 rounded-full px-2 py-0.5">
                                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                                </svg>
                                                {{ $t('round.restricted_access') }}
                                            </span>
                                        </div>
                                    </li>
                                </ul>

                                <div v-if="round.inspections.some(i => i.status === 'closed')" class="mt-6 pt-6 border-t border-surface-100 flex justify-end">
                                    <Button 
                                        variant="info" 
                                        @click="$inertia.get(route('results.team', round.inspections.filter(i => i.status === 'closed').sort((a,b) => b.id - a.id)[0].id))"
                                    >
                                        {{ $t('round.team_result') }}
                                    </Button>
                                </div>
                            </div>
                        </Card>

                        <!-- Diagnosis Card (Moved) -->
                        <Card v-if="round.diagnosis" class="border-brand-100 shadow-sm overflow-hidden" :title="$t('round.diagnosis_title')">
                            <div class="p-6 text-sm text-surface-700 prose prose-brand max-w-none whitespace-pre-wrap">{{ round.diagnosis }}</div>
                        </Card>


                    </div>

                    <div class="space-y-6">
                        <Card :title="$t('round.info_title')">
                            <div class="space-y-4 px-6 py-4">
                                <div>
                                    <span class="block text-xs font-semibold text-surface-500 uppercase">{{ $t('round.project_label') }}</span>
                                    <Link :href="route('projects.show', round.project.id)" class="text-brand-600 hover:underline text-sm">
                                        {{ round.project.name }}
                                    </Link>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-surface-500 uppercase">{{ $t('round.created_at') }}</span>
                                    <span class="text-surface-900 text-sm">{{ formatDate(round.created_at) }}</span>
                                </div>
                                <div v-if="round.closed_at">
                                    <span class="block text-xs font-semibold text-surface-500 uppercase">{{ $t('round.closed_at') }}</span>
                                    <span class="text-surface-900 text-sm">{{ formatDate(round.closed_at) }}</span>
                                </div>
                                <div>
                                    <span class="block text-xs font-semibold text-surface-500 uppercase">{{ $t('round.inspections_label') }}</span>
                                    <span class="text-surface-900 text-sm">{{ $t('round.inspections_linked', { count: round.inspections.length }) }}</span>
                                </div>
                            </div>
                        </Card>

                        <!-- Project Members Card -->
                        <Card :title="$t('project.members_title')">
                            <ul class="divide-y divide-surface-100">
                                <li v-for="member in round.project.members" :key="member.id" class="py-3 flex items-center justify-between px-6">
                                    <div class="flex items-center">
                                        <div class="h-8 w-8 rounded-full bg-surface-200 flex items-center justify-center text-surface-600 font-bold text-xs ring-2 ring-white">
                                            {{ member.user.name.charAt(0) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-surface-900">{{ member.user.name }}</p>
                                            <p class="text-xs text-surface-500">{{ member.user.email }}</p>
                                        </div>
                                    </div>
                                    <Badge :variant="member.role === 'owner' ? 'brand' : 'surface'">
                                        {{ member.role === 'owner' ? $t('workspace.role_owner') : (member.role === 'evaluator' ? $t('workspace.role_evaluator') : $t('workspace.role_observer')) }}
                                    </Badge>
                                </li>
                            </ul>
                        </Card>

                        <!-- Product URL -->
                        <div v-if="round.project.url" class="p-4 bg-white rounded-xl border border-surface-200 shadow-sm flex items-center justify-between">
                            <div class="text-sm">
                                <span class="block font-medium text-surface-900">{{ $t('project.product_url') }}</span>
                                <a :href="round.project.url" target="_blank" class="text-brand-600 hover:underline break-all block">
                                    {{ round.project.url }}
                                </a>
                            </div>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-surface-400 flex-shrink-0 ml-2" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M11 3a1 1 0 100 2h2.586l-6.293 6.293a1 1 0 101.414 1.414L15 6.414V9a1 1 0 102 0V4a1 1 0 00-1-1h-5z" />
                                <path d="M5 5a2 2 0 00-2 2v8a2 2 0 002 2h8a2 2 0 002-2v-3a1 1 0 10-2 0v3H5V7h3a1 1 0 000-2H5z" />
                            </svg>
                        </div>

                        <!-- Selo Embeddable Card (RN-BADGE-01) -->
                        <Card v-if="isClosed" :title="$t('round.badge_title')">
                            <div class="space-y-4 px-6 py-4">
                                <div v-if="round.public_directory?.visibility === 'private'" class="p-3 bg-amber-50 border border-amber-100 rounded-lg text-xs text-amber-700">
                                    {{ $t('round.badge_private_warning') }}
                                </div>
                                
                                <div v-else-if="!round.badge" class="space-y-4">
                                    <p class="text-xs text-surface-500">
                                        {{ $t('round.badge_description') }}
                                    </p>
                                    <Button variant="success" size="sm" class="w-full" @click="$inertia.post(route('rounds.badge.store', round.id))">
                                        {{ $t('round.generate_badge') }}
                                    </Button>
                                </div>

                                <div v-else class="space-y-4">
                                    <!-- Preview Visual -->
                                    <div class="p-4 bg-surface-50 border border-surface-200 rounded-lg flex flex-col items-center">
                                        <span class="text-[10px] text-surface-400 mb-2 uppercase font-bold">{{ $t('round.badge_preview') }}</span>
                                        
                                        <!-- Mini Mockup of Badge -->
                                        <div class="bg-white border border-surface-200 rounded-lg p-3 shadow-sm text-center min-w-[140px]">
                                            <div class="text-[10px] text-surface-500">{{ round.project.name }}</div>
                                            <div class="font-bold text-sm text-surface-900">{{ round.snapshots[0]?.payload_json?.medal?.name }}</div>
                                            <div class="text-[10px] text-surface-400">Score: {{ round.snapshots[0]?.payload_json?.global_score }}%</div>
                                        </div>
                                    </div>

                                    <!-- Estilo -->
                                    <div>
                                        <label class="block text-[10px] font-semibold text-surface-500 uppercase mb-1">{{ $t('round.badge_style_label') }}</label>
                                        <select 
                                            class="w-full text-xs rounded-md border-surface-300 focus:border-brand-500 focus:ring-brand-500"
                                            :value="round.badge.style"
                                            @change="(e) => $inertia.put(route('badges.style.update', round.badge.id), { style: e.target.value })"
                                        >
                                            <option value="default">{{ $t('round.badge_style_default') }}</option>
                                            <option value="compact">{{ $t('round.badge_style_compact') }}</option>
                                            <option value="minimal">{{ $t('round.badge_style_minimal') }}</option>
                                        </select>
                                    </div>

                                    <!-- Código Embed -->
                                    <div>
                                        <label class="block text-[10px] font-semibold text-surface-500 uppercase mb-1">{{ $t('round.badge_code_label') }}</label>
                                        <div class="relative group">
                                            <pre class="bg-surface-900 text-surface-100 text-[10px] p-2 rounded overflow-x-auto whitespace-pre-wrap leading-relaxed">&lt;script src="{{ $page.props.app_url }}/badge/{{ round.badge.public_token }}.js"&gt;&lt;/script&gt;</pre>
                                            <button 
                                                class="absolute top-1 right-1 p-1 bg-surface-700 hover:bg-surface-600 rounded text-white transition-colors"
                                                @click="copyToClipboard(`<script src='${$page.props.app_url}/badge/${round.badge.public_token}.js'></script>`)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-surface-100">
                                        <button 
                                            v-if="!round.badge.is_active"
                                            class="text-[10px] text-brand-600 hover:underline font-medium"
                                            @click="$inertia.post(route('rounds.badge.store', round.id))"
                                        >
                                            {{ $t('round.reactivate') }}
                                        </button>
                                        <button 
                                            v-else
                                            class="text-[10px] text-red-600 hover:underline font-medium"
                                            @click="confirmRevocation"
                                        >
                                            {{ $t('round.revoke_badge') }}
                                        </button>
                                    </div>
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
        :title="$t('round.publish_round_title')"
        :message="round.public_directory 
            ? $t('publication.update_message') 
            : $t('publication.create_message')"
        :confirm-text="round.public_directory ? $t('publication.update_button') : $t('publication.publish_button')"
        :cancel-text="$t('common.cancel')"
        :confirm-variant="round.public_directory ? 'brand' : 'primary'"
        @confirm="submitPublication"
        @close="closePublishModal"
    >
        <template #default>
            <div class="mt-4 space-y-4 text-left">
                <div>
                    <label class="block text-sm font-medium text-surface-700 mb-2">{{ $t('publication.visibility_label') }}</label>
                    <div class="space-y-2">
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'private'}">
                            <input type="radio" value="private" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">{{ $t('publication.private_label') }}</span>
                                <span class="block text-[10px] text-surface-500 mt-1">{{ $t('publication.private_description') }}</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'score_public'}">
                            <input type="radio" value="score_public" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">{{ $t('publication.score_label') }}</span>
                                <span class="block text-[10px] text-surface-500 mt-1">{{ $t('publication.score_description') }}</span>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 p-3 rounded-lg border border-surface-200 hover:bg-surface-50 cursor-pointer transition-colors" :class="{'bg-brand-50 border-brand-200': publishForm.visibility === 'full_public'}">
                            <input type="radio" value="full_public" v-model="publishForm.visibility" class="mt-1 text-brand-600 focus:ring-brand-500" />
                            <div>
                                <span class="block text-xs font-semibold text-surface-900 leading-none">{{ $t('publication.full_label') }}</span>
                                <span class="block text-[10px] text-surface-500 mt-1">{{ $t('publication.full_description') }}</span>
                            </div>
                        </label>
                    </div>
                </div>
                
                <div v-if="round.public_directory" class="pt-4 border-t border-surface-100 flex justify-center">
                    <button type="button" @click="revokePublication" class="text-xs text-red-600 hover:text-red-800 font-medium underline">
                        {{ $t('publication.remove_link') }}
                    </button>
                </div>
            </div>
        </template>
    </ConfirmModal>

    <ConfirmModal
        :show="isRevokingModalOpen"
        :title="$t('round.revoke_modal_title')"
        :message="$t('round.revoke_modal_message')"
        :confirm-text="$t('round.revoke_confirm')"
        :cancel-text="$t('common.cancel')"
        confirm-variant="danger"
        @confirm="submitRevocation"
        @close="closeRevokeModal"
    />
</template>
