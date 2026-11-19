<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<template>
    <div class="p-8 lg:p-16 text-center text-[#5F7480]">
        <p class="max-w-sm mx-auto">{{ t('channels_page.select_channel') }}</p>
    </div>
</template>
