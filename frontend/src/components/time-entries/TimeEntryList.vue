<script setup>
import SingleEntry from './SingleEntry.vue'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()
defineProps(['entries'])
</script>

<template>
    <div class="bg-white border border-[#D9D4CE] rounded-3xl overflow-hidden">
        <div class="hidden md:grid grid-cols-[1fr_170px_120px_96px] gap-4 px-5 py-3 bg-[#EFECE8] border-b border-[#D9D4CE] text-xs font-medium text-[#3E5660]">
            <span>{{ t('home.col_label') }}</span>
            <span>{{ t('home.col_time') }}</span>
            <span>{{ t('home.col_duration') }}</span>
            <span class="text-end">{{ t('home.col_actions') }}</span>
        </div>

        <div class="divide-y divide-[#D9D4CE]">
            <SingleEntry v-for="entry in entries" :key="entry.id" :entry="entry" />
        </div>
    </div>
</template>
