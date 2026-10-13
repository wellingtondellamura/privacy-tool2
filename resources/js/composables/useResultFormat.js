import { useI18n } from 'vue-i18n';

/**
 * Shared formatting for scores, medals and statuses.
 * A null score means "Não se aplica" / no applicable answer.
 */
export function useResultFormat() {
    const { t } = useI18n();

    const hasScore = (score) => score !== null && score !== undefined;

    const formatScore = (score, suffix = '%') => (hasScore(score) ? `${score}${suffix}` : t('results.not_applicable_short'));

    const medalKey = (medal) => {
        if (!medal) return null;
        return (typeof medal === 'string' ? medal : medal.name || '').toLowerCase() || null;
    };

    const medalLabel = (medal) => {
        const key = medalKey(medal);
        return key ? t(`results.medal.${key}`) : t('results.medal.no_data');
    };

    const statusLabel = (status) => t(`status.${status}`);

    return { hasScore, formatScore, medalKey, medalLabel, statusLabel };
}
