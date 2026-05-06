<script setup>
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import useApi from '@/composables/useApi'
import sharedDayService from '@/services/sharedDayService'

const { t } = useI18n()
const router = useRouter()

const { data, error, loading } = useApi(
    () => sharedDayService.getMySharedDays(), true
)
</script>

<template>
    <div class="max-w-3xl mx-auto px-6 py-12">

        <h1 class="text-[28px] font-black text-[#4A4A4A] mb-10 tracking-tighter">
            {{ t('my_shared_days.title') }}
        </h1>

        <div v-if="error" class="flex items-center gap-4 p-5 bg-[#FFF2F0] text-[#E07A5F] rounded-3xl border border-[#FFE5E0] shadow-sm">
            <span class="text-2xl">⚠️</span>
            <p class="font-bold text-[14px] italic">{{ error }}</p>
        </div>

        <div v-else-if="data" class="space-y-6">
            <div v-if="data.length > 0" class="space-y-4">
                <div
                    v-for="item in data"
                    :key="item.date"
                    class="bg-white border-2 border-[#F9F7F2] rounded-[2.5rem] p-6 hover:border-[#5A7D5A]/20 hover:shadow-xl hover:shadow-[#5A7D5A]/5 transition-all duration-300">

                    <div class="flex items-center gap-5 mb-5">
                        <div class="h-14 w-14 bg-[#F9F7F2] rounded-2xl flex flex-col items-center justify-center text-[#D4A373] shrink-0">
                            <span class="text-[10px] font-black uppercase leading-none opacity-80">
                                {{ new Date(item.date).toLocaleDateString('en-US', { month: 'short' }) }}
                            </span>
                            <span class="text-xl font-black leading-none mt-1">
                                {{ new Date(item.date).getDate() }}
                            </span>
                        </div>

                        <div>
                            <p class="font-black text-[17px] text-[#4A4A4A] leading-tight">
                                {{ new Date(item.date).toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }) }}
                            </p>
                            <p class="text-[12px] text-[#A0A0A0] font-bold italic mt-1.5 flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 bg-[#D4A373] rounded-full"></span>
                                {{ t('my_shared_days.shared_to', item.channels.length) }}
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-2.5">
                        <button
                            v-for="channel in item.channels"
                            :key="channel.id"
                            @click="router.push(`/channels/${channel.id}`)"
                            class="flex items-center gap-2 px-4 py-2 bg-[#F1F5F1] hover:bg-[#5A7D5A] hover:text-white text-[#5A7D5A] font-black text-[11px] uppercase tracking-widest rounded-xl transition-all active:scale-95 group">
                            <span class="h-5 w-5 bg-white/50 group-hover:bg-white/20 rounded-lg flex items-center justify-center text-[10px] font-black">
                                {{ channel.name.charAt(0) }}
                            </span>
                            {{ channel.name }}
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 opacity-40 group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-24 bg-[#FDFCFB] border-2 border-dashed border-[#F2E9E4] rounded-[3rem]">
                <div class="w-20 h-20 bg-white rounded-[2rem] shadow-xl shadow-[#D4A373]/5 flex items-center justify-center text-4xl mb-6">📜</div>
                <h3 class="text-[#4A4A4A] text-lg font-black tracking-tight">{{ t('my_shared_days.empty_title') }}</h3>
                <p class="text-[#A0A0A0] text-[14px] font-medium italic mt-2">{{ t('my_shared_days.empty_message') }}</p>
            </div>
        </div>

        <div v-else class="space-y-6">
            <div v-for="i in 3" :key="i" class="h-32 bg-[#F9F7F2] rounded-[2.5rem] animate-pulse"></div>
        </div>

    </div>
</template>