<script setup>
import { ref } from 'vue'
import { inject } from 'vue'
import { useRouter, RouterLink } from 'vue-router'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import sharedDayService from '@/services/sharedDayService'

const { t } = useI18n()
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
            class="flex items-center justify-between p-6 bg-white border-2 border-[#F9F7F2] rounded-[2rem] hover:border-[#5A7D5A]/30 hover:shadow-xl hover:shadow-[#5A7D5A]/5 transition-all cursor-pointer">

            <div class="flex items-center gap-6">
                <div class="h-14 w-14 bg-[#F9F7F2] rounded-2xl flex flex-col items-center justify-center text-[#D4A373] group-hover:bg-[#5A7D5A] group-hover:text-white transition-all duration-300">
                    <span class="text-[9px] font-black uppercase tracking-tighter leading-none opacity-80">{{ t('shared_day_card.day') }}</span>
                    <span class="text-xl font-black leading-none mt-1">
                        {{ new Date(day.date).getDate() }}
                    </span>
                </div>

                <div class="flex flex-col">
                    <span class="text-[17px] font-black text-[#4A4A4A] group-hover:text-[#5A7D5A] transition-colors leading-tight">
                        {{ new Date(day.date).toLocaleDateString('en-US', { month: 'long', year: 'numeric' }) }}
                    </span>
                    <div class="flex items-center gap-3 mt-1.5">
                        <span class="text-[11px] font-black text-[#5A7D5A] bg-[#F1F5F1] px-3 py-1 rounded-full uppercase tracking-wider">
                            {{ day.total_time.hours }}h {{ day.total_time.minutes }}m
                        </span>
                        <span class="text-[12px] text-[#A0A0A0] font-medium italic">
                            · {{ day.entries_count }} {{ t('shared_day_card.entries') }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3" v-if="isOwner">
                <template v-if="!confirmDelete">
                    <button
                        @click.stop="confirmDelete = true"
                        class="opacity-0 group-hover:opacity-100 transition-all duration-200 p-2 rounded-xl text-[#A0A0A0] hover:text-[#E07A5F] hover:bg-[#FFF2F0]">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14H6L5 6"/>
                            <path d="M9 6V4h6v2"/>
                        </svg>
                    </button>
                </template>

                <template v-else>
                    <div class="flex items-center gap-2 bg-[#FFF2F0] p-1.5 rounded-2xl border border-[#FFE5E0]">
                        <button
                            @click.stop="remove"
                            :disabled="loading"
                            class="px-4 py-1.5 text-[11px] font-black uppercase tracking-widest bg-[#E07A5F] text-white rounded-xl hover:bg-[#D6684D] disabled:opacity-50 transition-all">
                            {{ loading ? '...' : $t('single_entry.yes') }}
                        </button>
                        <button
                            @click.stop="confirmDelete = false"
                            :disabled="loading"
                            class="px-4 py-1.5 text-[11px] font-black uppercase tracking-widest text-[#A0A0A0] hover:text-[#4A4A4A] transition-all">
                            {{ $t('single_entry.no') }}
                        </button>
                    </div>
                </template>

                <div class="h-10 w-10 rounded-full flex items-center justify-center bg-[#F9F7F2] group-hover:bg-[#5A7D5A]/10 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#D4A373] group-hover:text-[#5A7D5A] transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                    </svg>
                </div>
            </div>
        </div>

        <div class="px-4">
            <RouterLink
                :to="`/channels/${day.channel_id}`"
                @click.stop
                class="text-[12px] text-[#A0A0A0] hover:text-[#5A7D5A] font-black uppercase tracking-[0.1em] transition-colors flex items-center gap-2">
                <span class="w-1 h-1 bg-[#D4A373] rounded-full"></span>
                {{ day.channel?.name }}
            </RouterLink>
        </div>
    </div>
</template>