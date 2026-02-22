import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/**
 * Translation helper composable.
 * Usage:
 *   const { t, locale } = useTranslation();
 *   t('dashboard')  // returns translated string
 */
export function useTranslation() {
    const page = usePage();

    const locale = computed(() => page.props.locale ?? 'en');

    const translations = computed(() => page.props.translations ?? {});

    /**
     * Translate a key. Returns the key itself if no translation found.
     */
    function t(key, replacements = {}) {
        let text = translations.value[key] ?? key;
        // Simple placeholder replacement: :name => value
        Object.entries(replacements).forEach(([k, v]) => {
            text = text.replace(new RegExp(`:${k}`, 'g'), v);
        });
        return text;
    }

    return { t, locale };
}
