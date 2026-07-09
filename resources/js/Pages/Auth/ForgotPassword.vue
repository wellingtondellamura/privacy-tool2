<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout
        :title="$t('auth.forgot_password_title')"
        :description="$t('auth.forgot_password_desc_left')"
        illustration-type="forgot"
        :right-title="$t('auth.forgot_password_title')"
        :loading="form.processing"
    >
        <Head :title="$t('auth.forgot_password_title')" />

        <div class="mb-6 text-sm text-surface-600 leading-relaxed">
            {{ $t('auth.forgot_password_description') }}
        </div>

        <div
            v-if="status"
            class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800"
        >
            {{ status }}
            
            <div class="mt-4">
                <Link
                    href="/"
                    class="inline-flex items-center rounded-lg border border-surface-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-surface-700 shadow-sm transition duration-150 ease-in-out hover:bg-surface-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:ring-offset-2"
                >
                    {{ $t('auth.back_home') }}
                </Link>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <Input
                :label="$t('auth.email')"
                id="email"
                type="email"
                v-model="form.email"
                required
                autofocus
                autocomplete="username"
                :error="form.errors.email"
                :placeholder="$t('auth.email_placeholder')"
            />

            <div class="pt-2 flex items-center justify-between gap-4">
                <Link
                    :href="route('login')"
                    class="text-sm font-medium text-surface-500 hover:text-brand-600 transition-colors"
                >
                    {{ $t('auth.back') }}
                </Link>

                <Button
                    type="submit"
                    variant="primary"
                    :disabled="form.processing"
                    class="cursor-pointer"
                >
                    {{ $t('auth.send_reset_link') }}
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
