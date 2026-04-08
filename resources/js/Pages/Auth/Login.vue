<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '', // Pre-fill with user email seeded
    password: '',     // Pre-fill with password seeded
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.login_title')" />

        <div v-if="status" class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <Input
                    :label="$t('auth.email_professional')"
                    id="email"
                    type="email"
                    v-model="form.email"
                    required
                    autofocus
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
                    autocomplete="current-password"
                    :error="form.errors.password"
                    placeholder="••••••••"
                />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-surface-600">{{ $t('auth.remember_me') }}</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-brand-600 hover:text-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2 transition-colors duration-smooth"
                >
                    {{ $t('auth.forgot_password_link') }}
                </Link>
            </div>

            <div>
                <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    class="w-full"
                    :disabled="form.processing"
                >
                    {{ $t('auth.login_title') }}
                </Button>
            </div>
            
            <div class="text-center">
                <p class="text-sm text-surface-500">
                    {{ $t('auth.no_account') }}
                    <Link :href="route('register')" class="font-medium text-brand-600 hover:text-brand-500 transition-colors duration-smooth">
                        {{ $t('nav.register') }}
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>
