<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import ExportDataForm from './Partials/ExportDataForm.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const { t } = useI18n();
const user = usePage().props.auth.user;

const profileLabels = {
    student:      t('auth.profile_student'),
    professional: t('auth.profile_professional'),
    researcher:   t('auth.profile_researcher'),
};
const profileIcons = { student: '🎓', professional: '💼', researcher: '🔬' };
</script>

<template>
    <Head :title="t('profile.title')" />

    <AuthenticatedLayout>
        <template #header>
            <Breadcrumbs :items="[{ label: $t('profile.title') }]" />
            <h2 class="text-2xl font-semibold text-surface-900 tracking-tight mt-1">
                {{ $t('profile.title') }}
            </h2>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 space-y-8">

                <!-- Avatar / Summary card -->
                <div class="bg-white rounded-2xl border border-surface-200 shadow-sm p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
                    <!-- Avatar circle with initials -->
                    <div class="h-20 w-20 rounded-full bg-brand-600 flex items-center justify-center text-white text-3xl font-bold flex-shrink-0 shadow-sm select-none">
                        {{ user.name.charAt(0).toUpperCase() }}
                    </div>
                    <div class="text-center sm:text-left">
                        <p class="text-xl font-semibold text-surface-900">{{ user.name }}</p>
                        <p class="text-sm text-surface-500">{{ user.email }}</p>
                        <div class="mt-2 flex flex-wrap justify-center sm:justify-start gap-2">
                            <span v-if="user.affiliation" class="inline-flex items-center gap-1 text-xs bg-surface-100 text-surface-700 px-2.5 py-1 rounded-full font-medium">
                                🏛 {{ user.affiliation }}
                            </span>
                            <span v-if="user.profile" class="inline-flex items-center gap-1 text-xs bg-brand-50 text-brand-700 px-2.5 py-1 rounded-full font-medium">
                                {{ profileIcons[user.profile] }} {{ profileLabels[user.profile] }}
                            </span>
                            <span v-if="user.email_verified_at" class="inline-flex items-center gap-1 text-xs bg-emerald-50 text-emerald-700 px-2.5 py-1 rounded-full font-medium">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                E-mail verificado
                            </span>
                            <span v-else class="inline-flex items-center gap-1 text-xs bg-amber-50 text-amber-700 px-2.5 py-1 rounded-full font-medium">
                                <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"/></svg>
                                E-mail não verificado
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Two-column grid: main forms + side actions -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <!-- Left: Profile Info + Password -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Profile Information -->
                        <div class="bg-white rounded-2xl border border-surface-200 shadow-sm p-6">
                            <UpdateProfileInformationForm
                                :must-verify-email="mustVerifyEmail"
                                :status="status"
                            />
                        </div>

                        <!-- Password -->
                        <div class="bg-white rounded-2xl border border-surface-200 shadow-sm p-6">
                            <UpdatePasswordForm />
                        </div>
                    </div>

                    <!-- Right: Export + Delete -->
                    <div class="space-y-6">

                        <!-- Export Data -->
                        <div class="bg-white rounded-2xl border border-surface-200 shadow-sm p-6">
                            <ExportDataForm />
                        </div>

                        <!-- Delete Account -->
                        <div class="bg-white rounded-2xl border border-red-100 shadow-sm p-6">
                            <DeleteUserForm />
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
