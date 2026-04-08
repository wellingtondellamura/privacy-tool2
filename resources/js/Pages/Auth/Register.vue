<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
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
                />
            </div>

            <div class="mt-4">
                <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    class="w-full"
                    :disabled="form.processing"
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
