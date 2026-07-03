<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import Checkbox from '@/Components/Checkbox.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
    profile: '',
    affiliation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.register_title')" />

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <Input
                    :label="$t('auth.name')"
                    id="name"
                    type="text"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    :error="form.errors.name"
                    :placeholder="$t('auth.name_placeholder')"
                />
            </div>

            <div>
                <Input
                    :label="$t('auth.email_professional')"
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    :error="form.errors.email"
                    :placeholder="$t('auth.email_placeholder')"
                />
            </div>

            <div>
                <Input
                    :label="$t('auth.password')"
                    id="password"
                    type="password"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                    :error="form.errors.password"
                    placeholder="••••••••"
                    :tooltip="$t('auth.password_tooltip')"
                />
            </div>

            <div>
                <Input
                    :label="$t('auth.password_confirm')"
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    :error="form.errors.password_confirmation"
                    placeholder="••••••••"
                    :tooltip="$t('auth.password_tooltip')"
                />
            </div>

            <!-- Optional: Profile & Affiliation -->
            <div class="space-y-4 pt-2 pb-1">
                <div class="flex items-center gap-2">
                    <div class="h-px flex-1 bg-surface-100"></div>
                    <span class="text-xs font-medium text-surface-400 uppercase tracking-wider px-1">
                        {{ $t('auth.profile_section') }}
                    </span>
                    <div class="h-px flex-1 bg-surface-100"></div>
                </div>

                <!-- Profile type select -->
                <div>
                    <label for="profile" class="block text-sm font-medium text-surface-700 mb-1">
                        {{ $t('auth.profile_label') }}
                    </label>
                    <select
                        id="profile"
                        v-model="form.profile"
                        class="block w-full rounded-lg border-surface-300 shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:text-sm transition-colors duration-smooth bg-white text-surface-700"
                    >
                        <option value="" disabled>{{ $t('auth.profile_placeholder') }}</option>
                        <option value="student">{{ $t('auth.profile_student') }}</option>
                        <option value="professional">{{ $t('auth.profile_professional') }}</option>
                        <option value="researcher">{{ $t('auth.profile_researcher') }}</option>
                    </select>
                    <p v-if="form.errors.profile" class="mt-1.5 text-sm text-red-600">
                        {{ form.errors.profile }}
                    </p>
                </div>

                <!-- Affiliation text input -->
                <div>
                    <Input
                        :label="$t('auth.affiliation_label')"
                        id="affiliation"
                        type="text"
                        v-model="form.affiliation"
                        :error="form.errors.affiliation"
                        :placeholder="$t('auth.affiliation_placeholder')"
                    />
                </div>
            </div>

            <!-- Terms acceptance -->

            <div class="flex flex-col gap-1.5">
                <div class="flex items-start gap-3">
                    <Checkbox
                        id="terms"
                        :checked="form.terms"
                        @update:checked="form.terms = $event"
                        class="mt-0.5 shrink-0"
                    />
                    <label for="terms" class="text-sm text-surface-600 leading-relaxed cursor-pointer select-none">
                        {{ $t('auth.terms_accept') }}
                        <a
                            :href="route('terms.use')"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-brand-600 hover:text-brand-500 underline underline-offset-2 transition-colors duration-smooth"
                        >
                            {{ $t('auth.terms_of_use') }}
                        </a>
                        {{ $t('auth.and') }}
                        <a
                            :href="route('privacy.policy')"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="font-medium text-brand-600 hover:text-brand-500 underline underline-offset-2 transition-colors duration-smooth"
                        >
                            {{ $t('auth.privacy_policy') }}
                        </a>.
                    </label>
                </div>
                <p v-if="form.errors.terms" class="text-sm text-red-600 pl-7">
                    {{ form.errors.terms }}
                </p>
            </div>

            <div class="mt-4">
                <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    class="w-full"
                    :disabled="form.processing || !form.terms"
                >
                    {{ $t('auth.register_title') }}
                </Button>
            </div>

            <div class="mt-4 text-center">
                <p class="text-sm text-surface-500">
                    {{ $t('auth.has_account') }}
                    <Link
                        :href="route('login')"
                        class="font-medium text-brand-600 hover:text-brand-500 transition-colors duration-smooth"
                    >
                        {{ $t('auth.login_title') }}
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
