<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<template>
    <div class="min-h-[60vh] flex items-center justify-center px-6">
        <div class="max-w-md text-center">
            <p class="text-8xl font-bold text-[#083A4F]/20 mb-4" dir="ltr">404</p>
            <h1 class="text-2xl font-semibold tracking-tight text-[#083A4F] mb-3">{{ t('not_found.title') }}</h1>
            <p class="text-[#5F7480] leading-relaxed mb-8">{{ t('not_found.message') }}</p>
            <router-link
                :to="{ name: 'Home' }"
                class="inline-block px-6 py-2.5 rounded-full bg-[#083A4F] text-white text-sm font-medium transition-colors hover:bg-[#407E8C]">
                {{ t('not_found.back_home') }}
            </router-link>
        </div>
    </div>
</template>
