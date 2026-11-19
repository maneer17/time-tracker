<script setup>
import { ref } from 'vue'
import { inject } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useLocaleFormat } from '@/composables/useLocaleFormat'
import useApi from '@/composables/useApi'
import sharedDayService from '@/services/sharedDayService'

const { t } = useI18n()
const { formatDate } = useLocaleFormat()
const router = useRouter()
const props = defineProps(['day'])
const emit = defineEmits(['deleted'])
const isOwner = inject('isOwner')
const confirmDelete = ref(false)

const { loading, error, request } = useApi(
    () => sharedDayService.removeSharedDay(props.day.channel_id, props.day.id)
)

const remove = async () => {
    await request()
    if (!error.value) emit('deleted', props.day.id)
}
</script>

<template>
    <div class="group flex flex-col gap-2">
        <div
            @click="router.push(`/channels/${day.channel_id}/shared-days/${day.id}`)"
            class="flex items-center justify-between p-6 bg-white border border-[#FAF9F7] rounded-3xl hover:border-[#083A4F]/30 transition-all cursor-pointer">

            <div class="flex items-center gap-6">
                <div class="h-14 w-14 bg-[#FAF9F7] rounded-2xl flex flex-col items-center justify-center text-[#A58D66] group-hover:bg-[#083A4F] group-hover:text-white transition-all duration-300">
                    <span class="text-[9px] font-semibold leading-none opacity-80">{{ t('shared_day_card.day') }}</span>
                    <span class="text-xl font-semibold leading-none mt-1">
                        {{ new Date(day.date).getUTCDate() }}
                    </span>
                </div>

                <div class="flex flex-col">
                    <span class="text-[17px] font-semibold text-[#083A4F] group-hover:text-[#083A4F] transition-colors leading-tight">
                        {{ formatDate(day.date, { month: 'long', year: 'numeric' }) }}
                    </span>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-xs font-semibold text-[#083A4F] bg-[#EEF3F3] px-3 py-1 rounded-full">
                            {{ day.total_time.hours }}{{ t('units.hours_short') }} {{ day.total_time.minutes }}{{ t('units.minutes_short') }}
                        </span>
                        <span class="text-[12px] text-[#5F7480] font-medium">
                            · {{ day.entries_count }} {{ t('shared_day_card.entries') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3" v-if="isOwner">
                <template v-if="!confirmDelete">
                    <button
                        @click.stop="confirmDelete = true"
                        class="opacity-0 group-hover:opacity-100 transition-all duration-200 p-2 rounded-xl text-[#5F7480] hover:text-[#991B1B] hover:bg-[#FEE2E2]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M9 6V4h6v2"/>
                        </svg>
                    </button>
                </template>

                <template v-else>
                    <div class="flex items-center gap-2 bg-[#FEE2E2] p-1.5 rounded-2xl border border-[#FECACA]">
                        <button
                            @click.stop="remove"
                            :disabled="loading"
                            class="px-4 py-1.5 text-xs font-semibold bg-[#991B1B] text-white rounded-xl hover:bg-[#7F1D1D] disabled:opacity-50 transition-all">
                            {{ loading ? '...' : $t('single_entry.yes') }}
                        </button>
                        <button
                            @click.stop="confirmDelete = false"
                            :disabled="loading"
                            class="px-4 py-1.5 text-xs font-semibold text-[#5F7480] hover:text-[#083A4F] transition-all">
                            {{ $t('single_entry.no') }}
                        </button>
                    </div>
                </template>

                <div class="h-10 w-10 rounded-full flex items-center justify-center bg-[#FAF9F7] group-hover:bg-[#083A4F]/10 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#A58D66] group-hover:text-[#083A4F] transition-transform group-hover:translate-x-1 rtl:-scale-x-100 rtl:group-hover:-translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="px-4">
            <RouterLink
                :to="`/channels/${day.channel_id}`"
                @click.stop
                class="text-[12px] text-[#5F7480] hover:text-[#083A4F] font-semibold transition-colors flex items-center gap-2">
                <span class="w-1 h-1 bg-[#A58D66] rounded-full"></span>
                {{ day.channel?.name }}
            </RouterLink>
        </div>
    </div>
</template>