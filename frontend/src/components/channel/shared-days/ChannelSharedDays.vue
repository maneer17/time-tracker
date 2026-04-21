<script setup>
import { inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { useRouter } from 'vue-router'
import useApi from '@/composables/useApi'
import sharedDayService from '@/services/sharedDayService'
import SharedDayCard from './SharedDayCard.vue'

const { t } = useI18n()
const toast = useToast()
const channel = inject('channel')

const { data, error,request } = useApi(
    () => sharedDayService.getChannelSharedDays(channel.value.id), true
)

const refreshPage = () => {
    toast.success(t('channel_shared_days.removed'))
    request()
}
</script>

<template>
    <div class="max-w-3xl mx-auto py-6">

        <div v-if="error" class="flex items-center gap-3 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-100 shadow-sm">
            <span class="text-xl">⚠️</span>
            <p class="font-medium text-sm">{{ error }}</p>
        </div>

        <div v-else-if="data" class="space-y-4">
            <div v-if="data.length > 0" class="grid gap-4">
                <SharedDayCard
                    v-for="sharedDay in data"
                    :key="sharedDay.id"
                    :day="sharedDay"
                    @deleted="refreshPage" />
            </div>

            <div v-else class="flex flex-col items-center justify-center py-20 bg-gray-50/50 border-2 border-dashed border-gray-200 rounded-3xl">
                <div class="w-16 h-16 bg-white rounded-2xl shadow-sm flex items-center justify-center text-2xl mb-4">📅</div>
                <h3 class="text-gray-900 font-bold">{{ t('channel_shared_days.empty_title') }}</h3>
                <p class="text-gray-400 text-sm mt-1">{{ t('channel_shared_days.empty_message') }}</p>
            </div>
        </div>

        <div v-else class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-20 bg-gray-100 rounded-2xl animate-pulse"></div>
        </div>

    </div>
</template>