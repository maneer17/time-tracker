<script setup>
import { inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useToast } from 'vue-toastification'
import { usePagination } from '@/composables/usePagination'
import AppPagination from '@/components/AppPagination.vue'
import sharedDayService from '@/services/sharedDayService'
import SharedDayCard from './SharedDayCard.vue'

const { t } = useI18n()
const toast = useToast()
const channel = inject('channel')


const { items: data, paginatorData, loading, error, goToPage, refresh } = usePagination(
    (page) => sharedDayService.getChannelSharedDays(channel.value.id,{ page }),
    { immediate: true }
)

const refreshPage = () => {
    toast.success(t('channel_shared_days.removed'))
    refresh()
}
</script>

<template>
    <div>

        <div v-if="error" class="p-4 bg-[#FEE2E2] text-[#991B1B] rounded-3xl border border-[#FECACA] text-sm font-medium">
            {{ error }}
        </div>

        <div v-else-if="data" class="space-y-4">
            <div v-if="data.length > 0" class="grid gap-4">
                <SharedDayCard
                    v-for="sharedDay in data"
                    :key="sharedDay.id"
                    :day="sharedDay"
                    @deleted="refreshPage" />
            </div>

            <div v-else class="flex flex-col items-center justify-center py-16 bg-white border border-dashed border-[#D9D4CE] rounded-3xl text-center px-6">
                <h3 class="font-semibold">{{ t('channel_shared_days.empty_title') }}</h3>
                <p class="text-[#5F7480] text-sm mt-1">{{ t('channel_shared_days.empty_message') }}</p>
            </div>

            <AppPagination
                v-if="data.length > 0 && paginatorData?.meta"
                :data="paginatorData"
                @page-change="goToPage" />
        </div>

        <div v-else class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-20 bg-[#EFECE8] rounded-3xl animate-pulse"></div>
        </div>

    </div>
</template>
