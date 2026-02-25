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
        <Head title="Verificação de E-mail" />

        <div class="mb-6 text-sm text-surface-600 leading-relaxed">
            Obrigado por se registrar! Antes de começar, você poderia verificar seu
            endereço de email acessando o link que acabamos de enviar? Se você não
            recebeu o email, ficaremos felizes em enviar outro.
        </div>

        <div
            class="mb-6 rounded-md bg-green-50 p-4 text-sm font-medium text-green-800"
            v-if="verificationLinkSent"
        >
            Um novo link de verificação foi enviado para o endereço de email 
            fornecido durante o registro.
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
                    Reenviar Email de Verificação
                </Button>

                <Link
                    :href="route('logout')"
                    method="post"
                    as="button"
                    class="text-sm font-medium text-surface-500 hover:text-surface-700 transition-colors"
                >
                    Sair da conta
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
