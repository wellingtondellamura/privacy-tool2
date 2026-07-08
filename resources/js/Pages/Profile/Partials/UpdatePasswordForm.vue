<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';

const { t } = useI18n();
const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: () => {
            if (form.errors.password) {
                form.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (form.errors.current_password) {
                form.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-base font-semibold text-surface-900">{{ $t('profile.update_password_title') }}</h2>
            <p class="mt-1 text-sm text-surface-500">{{ $t('profile.update_password_description') }}</p>
        </div>

        <form @submit.prevent="updatePassword" class="space-y-5">
            <Input
                id="current-password"
                ref="currentPasswordInput"
                :label="$t('profile.current_password')"
                v-model="form.current_password"
                type="password"
                autocomplete="current-password"
                :error="form.errors.current_password"
            />
            <Input
                id="new-password"
                ref="passwordInput"
                :label="$t('profile.new_password')"
                v-model="form.password"
                type="password"
                autocomplete="new-password"
                :error="form.errors.password"
            />
            <Input
                id="confirm-password"
                :label="$t('profile.confirm_password')"
                v-model="form.password_confirmation"
                type="password"
                autocomplete="new-password"
                :error="form.errors.password_confirmation"
            />

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
