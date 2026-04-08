import { useI18n } from 'vue-i18n';

export function useFormatDate() {
    const { locale } = useI18n();

    const formatDate = (date) => {
        if (!date) return '';
        const loc = locale.value === 'pt_BR' ? 'pt-BR' : 'en-US';
        return new Date(date).toLocaleDateString(loc);
    };

    return { formatDate };
}
