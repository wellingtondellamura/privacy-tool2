<script setup>
import LegalDocument from '@/Components/LegalDocument.vue';
import pt_BR from '@/legal/privacy.pt_BR.js';
import en from '@/legal/privacy.en.js';
import es from '@/legal/privacy.es.js';

defineProps({
    legal: { type: Object, required: true },
});
</script>

<template>
    <LegalDocument :documents="{ pt_BR, en, es }" title-key="privacy.title" :legal="legal" :app-url="$page.props.app_url" />
</template>
