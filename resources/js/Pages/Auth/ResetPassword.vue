<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    email: {
        type: String,
        required: true,
    },
    token: {
        type: String,
        required: true,
    },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('password.store'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout
        :title="$t('auth.reset_password_title')"
        :description="$t('auth.reset_password_desc_left')"
        illustration-type="reset"
        :right-title="$t('auth.reset_password_title')"
        :loading="form.processing"
    >
        <Head :title="$t('auth.reset_password_title')" />

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

            <div class="pt-2">
                <Button
                    type="submit"
                    variant="primary"
                    :disabled="form.processing"
                    class="w-full cursor-pointer"
                >
                    {{ $t('auth.reset_password_button') }}
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
