<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<template>
    <div class="max-w-2xl mx-auto px-6 py-16">

        <h1 class="text-3xl font-black text-gray-900 mb-8">{{ t('about.title') }}</h1>

        <div class="space-y-6 text-gray-600 leading-relaxed">
            <p>{{ t('about.p1') }}</p>
            <p>{{ t('about.p2') }}</p>
            <p>{{ t('about.p3') }}</p>
        </div>

    </div>
</template>