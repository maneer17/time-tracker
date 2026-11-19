<script setup>
import { useI18n } from 'vue-i18n'
import { useLocaleFormat } from '@/composables/useLocaleFormat'
import { useRouter } from 'vue-router'
import useApi from '@/composables/useApi'
import sharedDayService from '@/services/sharedDayService'

const { t } = useI18n()
const { formatDate } = useLocaleFormat()
const router = useRouter()

const { data, error, loading } = useApi(
    () => sharedDayService.getMySharedDays(), true
)
import PageShell from '@/components/PageShell.vue'
</script>

<template>
    <PageShell :title="t('my_shared_days.title')" narrow>

        <div v-if="error" class="p-4 bg-[#FEE2E2] text-[#991B1B] rounded-3xl border border-[#FECACA] text-sm font-medium">
            {{ error }}
        </div>

        <div v-else-if="data" class="space-y-4">
            <template v-if="data.length > 0">
                <div
                    v-for="item in data"
                    :key="item.date"
                    class="bg-white border border-[#D9D4CE] rounded-3xl p-5">

                    <div class="flex items-center gap-4 mb-4">
                        <div class="h-12 w-12 bg-[#EFECE8] rounded-2xl flex flex-col items-center justify-center text-[#083A4F] shrink-0">
                            <span class="text-[10px] font-semibold leading-none">{{ formatDate(item.date, { month: 'short' }) }}</span>
                            <span class="text-lg font-semibold leading-none mt-1">{{ new Date(item.date).getUTCDate() }}</span>
                        </div>

                        <div>
                            <p class="font-semibold leading-tight">
                                {{ formatDate(item.date, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }) }}
                            </p>
                            <p class="text-xs text-[#5F7480] mt-1">{{ t('my_shared_days.shared_to', item.channels.length) }}</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <button
                            v-for="channel in item.channels"
                            :key="channel.id"
                            @click="router.push(`/channels/${channel.id}/shared-days/${channel.shared_day_id}`)"
                            class="inline-flex items-center gap-2 px-3.5 py-1.5 bg-[#C0D5D6] hover:bg-[#083A4F] hover:text-white text-[#083A4F] text-sm font-medium rounded-full transition-colors">
                            {{ channel.name }}
                            <svg class="h-3 w-3 rtl:-scale-x-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>
                        </button>
                    </div>
                </div>
            </template>

            <div v-else class="flex flex-col items-center justify-center py-16 bg-white border border-dashed border-[#D9D4CE] rounded-3xl text-center px-6">
                <h3 class="font-semibold">{{ t('my_shared_days.empty_title') }}</h3>
                <p class="text-[#5F7480] text-sm mt-1">{{ t('my_shared_days.empty_message') }}</p>
            </div>
        </div>

        <div v-else class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-28 bg-[#EFECE8] rounded-3xl animate-pulse"></div>
        </div>

    </PageShell>
</template>
