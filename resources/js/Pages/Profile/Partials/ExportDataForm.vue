<script setup>
import Button from '@/Components/Button.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-surface-900">
                {{ $t('profile.export_title') }}
            </h2>

            <p class="mt-1 text-sm text-surface-600">
                {{ $t('profile.export_description') }}
            </p>
        </header>

        <div class="flex items-center gap-4">
            <a :href="route('profile.export-all')" target="_blank">
                <Button variant="outline">
                    {{ $t('profile.export_button') }}
                </Button>
            </a>
        </div>
    </section>
</template>
