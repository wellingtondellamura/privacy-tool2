<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';

defineProps({
    mustVerifyEmail: { type: Boolean },
    status: { type: String },
});

const { t } = useI18n();
const user = usePage().props.auth.user;

const form = useForm({
    name:        user.name,
    email:       user.email,
    affiliation: user.affiliation ?? '',
    profile:     user.profile ?? '',
});
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-base font-semibold text-surface-900">{{ $t('profile.info_title') }}</h2>
            <p class="mt-1 text-sm text-surface-500">{{ $t('profile.info_description') }}</p>
        </div>

        <form @submit.prevent="form.patch(route('profile.update'))" class="space-y-5">
            <!-- Name -->
            <Input
                id="profile-name"
                :label="$t('profile.name')"
                v-model="form.name"
                type="text"
                required
                autofocus
                autocomplete="name"
                :error="form.errors.name"
            />

            <!-- Email -->
            <div>
                <Input
                    id="profile-email"
                    :label="$t('profile.email')"
                    v-model="form.email"
                    type="email"
                    required
                    autocomplete="username"
                    :error="form.errors.email"
                />
                <!-- Unverified email warning -->
                <div v-if="mustVerifyEmail && user.email_verified_at === null" class="mt-2 p-3 rounded-lg bg-amber-50 border border-amber-200 text-sm text-amber-700 flex items-start gap-2">
                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z" />
                    </svg>
                    <span>
                        {{ $t('profile.email_unverified') }}
                        <Link :href="route('verification.send')" method="post" as="button"
                            class="underline font-medium hover:text-amber-900 ml-1">
                            {{ $t('profile.resend_verification') }}
                        </Link>
                    </span>
                </div>
                <div v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-emerald-600">
                    {{ $t('profile.verification_sent') }}
                </div>
            </div>

            <!-- Affiliation -->
            <Input
                id="profile-affiliation"
                :label="$t('auth.affiliation_label')"
                v-model="form.affiliation"
                type="text"
                :placeholder="$t('auth.affiliation_placeholder')"
                :error="form.errors.affiliation"
            />

            <!-- Profile Type -->
            <div class="space-y-1.5">
                <label class="block text-sm font-medium text-surface-700">{{ $t('auth.profile_label') }}</label>
                <div class="grid grid-cols-3 gap-3">
                    <label
                        v-for="option in [
                            { value: 'student',      icon: '🎓', label: $t('auth.profile_student') },
                            { value: 'professional', icon: '💼', label: $t('auth.profile_professional') },
                            { value: 'researcher',   icon: '🔬', label: $t('auth.profile_researcher') },
                        ]"
                        :key="option.value"
                        class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer transition-all duration-150 text-center"
                        :class="form.profile === option.value
                            ? 'border-brand-500 bg-brand-50 text-brand-700'
                            : 'border-surface-200 bg-white text-surface-600 hover:border-surface-300 hover:bg-surface-50'"
                    >
                        <input type="radio" :value="option.value" v-model="form.profile" class="sr-only" />
                        <span class="text-xl">{{ option.icon }}</span>
                        <span class="text-xs font-semibold leading-tight">{{ option.label }}</span>
                    </label>
                </div>
                <p v-if="form.errors.profile" class="text-xs text-red-600 mt-1">{{ form.errors.profile }}</p>
            </div>

            <!-- Submit -->
            <div class="flex items-center gap-4 pt-2">
                <Button type="submit" variant="primary" :disabled="form.processing">
                    {{ $t('common.save') }}
                </Button>
                <Transition enter-active-class="transition ease-out duration-200" enter-from-class="opacity-0 translate-y-1" leave-active-class="transition ease-in duration-150" leave-to-class="opacity-0">
                    <span v-if="form.recentlySuccessful" class="inline-flex items-center gap-1.5 text-sm text-emerald-600 font-medium">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ $t('common.saved') }}
                    </span>
                </Transition>
            </div>
        </form>
    </section>
</template>
