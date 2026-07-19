import { createI18n } from 'vue-i18n';
import en from '../locales/en.json';
import ptBR from '../locales/pt_BR.json';
import es from '../locales/es.json';

export function createI18nInstance(locale = 'en') {
    return createI18n({
        legacy: false,
        locale,
        fallbackLocale: 'en',
        messages: { en, pt_BR: ptBR, es },
    });
}
