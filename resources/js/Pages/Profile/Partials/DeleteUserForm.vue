<script setup>
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';
import { useI18n } from 'vue-i18n';
import Input from '@/Components/Input.vue';
import Button from '@/Components/Button.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const { t } = useI18n();
const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({ password: '' });

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-base font-semibold text-red-700">{{ $t('profile.delete_title') }}</h2>
            <p class="mt-1 text-sm text-surface-500">{{ $t('profile.delete_warning') }}</p>
        </div>

        <Button variant="danger" @click="confirmUserDeletion">
            {{ $t('profile.delete_button') }}
        </Button>

        <ConfirmModal
            :show="confirmingUserDeletion"
            :title="$t('profile.delete_confirm_title')"
            :message="$t('profile.delete_confirm_description')"
            :confirm-text="$t('profile.delete_button')"
            :cancel-text="$t('common.cancel')"
            confirm-variant="danger"
            @confirm="deleteUser"
            @close="closeModal"
        >
            <template #default>
                <div class="mt-4">
                    <Input
                        id="delete-password"
                        ref="passwordInput"
                        :label="$t('profile.password')"
                        v-model="form.password"
                        type="password"
                        :placeholder="$t('profile.password')"
                        :error="form.errors.password"
                        @keyup.enter="deleteUser"
                    />
                </div>
            </template>
        </ConfirmModal>
    </section>
</template>
