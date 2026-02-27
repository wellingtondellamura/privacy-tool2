<script setup>
import Button from '@/Components/Button.vue';

</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-surface-900">
                Exportar Todos os Dados
            </h2>

            <p class="mt-1 text-sm text-surface-600">
                Baixe todas as informações de todos os projetos em que você participa em um único arquivo ZIP contendo arquivos JSON individuais.
            </p>
        </header>

        <div class="flex items-center gap-4">
            <a :href="route('profile.export-all')" target="_blank">
                <Button variant="outline">
                    Baixar Projetos (ZIP)
                </Button>
            </a>
        </div>
    </section>
</template>
