import { computed } from 'vue'
import { useI18n } from 'vue-i18n'

// Latin digits in both languages so numbers match the rest of the interface.
const DATE_LOCALES = { en: 'en-US', ar: 'ar-u-nu-latn' }

const RELATIVE_UNITS = [
    ['year', 31536000],
    ['month', 2592000],
    ['day', 86400],
    ['hour', 3600],
    ['minute', 60],
]

/** Date, time and relative-time formatting that follows the interface language. */
export function useLocaleFormat() {
    const { locale, t } = useI18n()

    const dateLocale = computed(() => DATE_LOCALES[locale.value] ?? DATE_LOCALES.en)

    // Dates from the API are calendar days at midnight UTC, so format them in UTC
    // or the day shifts for users west of UTC.
    const formatDate = (value, options) =>
        new Date(value).toLocaleDateString(dateLocale.value, { timeZone: 'UTC', ...options })

    const formatDateTime = (value) =>
        new Date(value).toLocaleString(dateLocale.value, {
            day: 'numeric', month: 'short', year: 'numeric', hour: 'numeric', minute: '2-digit',
        })

    // The API sends times like "9:30 am"; only the am/pm marker needs translating.
    const displayTime = (value) =>
        String(value ?? '').replace(/\b(am|pm)\b/i, (marker) => t(`time.${marker.toLowerCase()}`))

    const fromNow = (value) => {
        const seconds = Math.round((new Date(value).getTime() - Date.now()) / 1000)
        const formatter = new Intl.RelativeTimeFormat(dateLocale.value, { numeric: 'auto' })

        for (const [unit, size] of RELATIVE_UNITS) {
            if (Math.abs(seconds) >= size) return formatter.format(Math.round(seconds / size), unit)
        }
        return formatter.format(0, 'second')
    }

    return { dateLocale, formatDate, formatDateTime, displayTime, fromNow }
}
