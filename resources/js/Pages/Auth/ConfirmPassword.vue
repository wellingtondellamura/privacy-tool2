<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout
        :title="$t('auth.confirm_password_title')"
        :description="$t('auth.confirm_password_desc_left')"
        illustration-type="confirm"
        :right-title="$t('auth.confirm_password_title')"
        :loading="form.processing"
    >
        <Head :title="$t('auth.confirm_password_title')" />

        <div class="mb-6 text-sm text-surface-600 leading-relaxed">
            {{ $t('auth.confirm_password_description') }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <Input
                :label="$t('auth.password')"
                id="password"
                type="password"
                v-model="form.password"
                required
                autocomplete="current-password"
                autofocus
                :error="form.errors.password"
                placeholder="••••••••"
            />

            <div class="pt-2 flex justify-end">
                <Button
                    type="submit"
                    variant="primary"
                    :disabled="form.processing"
                    class="w-full cursor-pointer"
                >
                    {{ $t('common.confirm') }}
                </Button>
            </div>
        </form>
    </GuestLayout>
</template>
