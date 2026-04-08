<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import Button from '@/Components/Button.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head :title="$t('auth.verify_email_title')" />

        <div class="mb-6 text-sm text-surface-600 leading-relaxed">
            {{ $t('auth.verify_email_description') }}
        </div>

        <div
            class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800"
            v-if="verificationLinkSent"
        >
            {{ $t('auth.verify_email_sent') }}
        </div>

        <form @submit.prevent="submit">
            <div class="mt-4 flex flex-col space-y-4">
                <Button
                    type="submit"
                    variant="primary"
                    size="lg"
                    class="w-full"
                    :disabled="form.processing"
                >
                    {{ $t('auth.resend_verification') }}
                </Button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm font-medium text-surface-500 hover:text-surface-700 transition-colors"
                >
                    {{ $t('auth.logout_link') }}
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
