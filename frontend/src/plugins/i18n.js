import { createI18n } from 'vue-i18n'
import en from '@/locales/en.json'
import ar from '@/locales/ar.json'

const SUPPORTED_LOCALES = ['en', 'ar']
const STORAGE_KEY = 'locale'

const readSavedLocale = () => {
    try {
        return localStorage.getItem(STORAGE_KEY)
    } catch {
        return null
    }
}

// Saved choice first, then the browser's language, then English.
const detectLocale = () => {
    const saved = readSavedLocale()
    if (SUPPORTED_LOCALES.includes(saved)) return saved

    const browser = (navigator.language || '').slice(0, 2).toLowerCase()
    return SUPPORTED_LOCALES.includes(browser) ? browser : 'en'
}

/** Keep <html lang> and <html dir> in sync so RTL applies before the first paint. */
export const applyDocumentLocale = (locale) => {
    document.documentElement.setAttribute('lang', locale)
    document.documentElement.setAttribute('dir', locale === 'ar' ? 'rtl' : 'ltr')
}

export const persistLocale = (locale) => {
    try {
        localStorage.setItem(STORAGE_KEY, locale)
    } catch {
        // storage can be unavailable (private mode); the choice just won't persist
    }
}

// Arabic has six plural forms. Messages with six variants use them
// (zero | one | two | few 3-10 | many 11-99 | other); others keep vue-i18n's default rule.
export const arabicPlural = (choice, choicesLength) => {
    const n = Math.abs(choice)

    if (choicesLength === 6) {
        if (n === 0) return 0
        if (n === 1) return 1
        if (n === 2) return 2
        const lastTwoDigits = n % 100
        if (lastTwoDigits >= 3 && lastTwoDigits <= 10) return 3
        return lastTwoDigits >= 11 ? 4 : 5
    }

    if (choicesLength === 2) return n ? (n > 1 ? 1 : 0) : 1
    return n ? Math.min(n, 2) : 0
}

export const i18n = createI18n({
    legacy: false,
    locale: detectLocale(),
    fallbackLocale: 'en',
    pluralRules: { ar: arabicPlural },
    messages: { en, ar }
})

applyDocumentLocale(i18n.global.locale.value)
