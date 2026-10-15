<script setup>
import LegalDocument from '@/Components/LegalDocument.vue';
import pt_BR from '@/legal/terms.pt_BR.js';
import en from '@/legal/terms.en.js';
import es from '@/legal/terms.es.js';

defineProps({
    legal: { type: Object, required: true },
});
</script>

<template>
    <LegalDocument :documents="{ pt_BR, en, es }" title-key="terms.title" :legal="legal" :app-url="$page.props.app_url" />
</template>
