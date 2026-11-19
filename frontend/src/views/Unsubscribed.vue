<script setup>
import { computed } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'

const { t, te } = useI18n()
const route = useRoute()

// Which kind of email was switched off, in the reader's language when we know it
const typeLabel = computed(() => {
    const key = `settings.emailNotifications.types.${route.query.type}`
    return te(key) ? t(key) : ''
})
</script>

<template>
    <div class="min-h-screen flex items-center justify-center paper-bg px-4">
        <div class="w-full max-w-md text-center space-y-4">
            <h1 class="text-2xl font-semibold tracking-tight">{{ t('unsubscribed.title') }}</h1>
            <p class="text-[#3E5660]">
                {{ typeLabel ? t('unsubscribed.message_type', { type: typeLabel }) : t('unsubscribed.message') }}
            </p>
            <router-link :to="{ name: 'Home' }"
                         class="inline-block px-6 py-2.5 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors">
                {{ t('unsubscribed.back') }}
            </router-link>
        </div>
    </div>
</template>
