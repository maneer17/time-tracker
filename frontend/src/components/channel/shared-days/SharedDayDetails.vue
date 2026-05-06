<script setup>
import { useI18n } from 'vue-i18n'
import { provide, ref } from 'vue'
import { useRoute } from 'vue-router'
import useApi from '@/composables/useApi'
import sharedDayService from '@/services/sharedDayService'
import CommentsSection from './CommentsSection.vue'

const { t } = useI18n()
const route = useRoute()

const { data: sharedDay, error, loading } = useApi(
    () => sharedDayService.getSharedDay(route.params.channel_id, route.params.id), true
)
provide('sharedDay', sharedDay)
</script>

<template>
    <div class="max-w-3xl mx-auto py-12 px-6">

        <div v-if="error" class="p-6 bg-[#FFF2F0] text-[#E07A5F] rounded-3xl border border-[#FFE5E0] text-[14px] font-black uppercase tracking-widest flex items-center gap-3">
            <span>⚠️</span> {{ error }}
        </div>

        <div v-else-if="sharedDay" class="space-y-10">

            <header class="flex items-end justify-between border-b-2 border-[#F9F7F2] pb-8">
                <div>
                    <h2 class="text-3xl font-black text-[#4A4A4A] tracking-tight">{{ sharedDay.date }}</h2>
                    <p class="text-[15px] text-[#8E9AAF] font-medium mt-1 italic">{{ t('shared_day_view.summary') }}</p>
                </div>
                <div class="text-right">
                    <span class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em] block mb-2">
                        {{ t('shared_day_view.total_time') }}
                    </span>
                    <span class="text-2xl font-black text-[#5A7D5A]">
                        {{ sharedDay.total_time.hours }}<span class="text-sm opacity-60 ml-0.5">h</span> 
                        {{ sharedDay.total_time.minutes }}<span class="text-sm opacity-60 ml-0.5">m</span>
                    </span>
                </div>
            </header>

            <div class="space-y-4">
                <div
                    v-for="entry in sharedDay.entries"
                    :key="entry.id"
                    class="flex items-center justify-between p-6 bg-white border-2 border-[#F9F7F2] rounded-[2rem] hover:shadow-xl hover:shadow-[#F9F7F2]/50 transition-all group">
                    
                    <div class="flex items-center gap-5">
                        <div class="w-1.5 h-10 bg-[#D4A373] rounded-full group-hover:bg-[#5A7D5A] transition-colors"></div>
                        
                        <div class="flex flex-col">
                            <span class="font-black text-[#4A4A4A] text-lg leading-tight group-hover:text-[#5A7D5A] transition-colors">
                                {{ entry.time_entry.label }}
                            </span>
                            <span class="text-[13px] font-bold text-[#A0A0A0] mt-1">
                                {{ entry.time_entry.start_time }} — {{ entry.time_entry.end_time }}
                            </span>
                        </div>
                    </div>

                    <div class="bg-[#FDFCFB] px-5 py-2.5 rounded-2xl border border-[#F2E9E4]">
                        <span class="text-[14px] font-black text-[#D4A373]">
                            {{ entry.time_entry.time_taken.hours }}<span class="text-[10px] opacity-60 ml-0.5">h</span> 
                            {{ entry.time_entry.time_taken.minutes }}<span class="text-[10px] opacity-60 ml-0.5">m</span>
                        </span>
                    </div>
                </div>
            </div>

            <div class="pt-10 border-t-2 border-[#F9F7F2]">
                <div class="mb-6 px-2">
                    <h3 class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
                        {{ t('shared_day_view.discussion') || 'Discussion' }}
                    </h3>
                </div>
                <CommentsSection />
            </div>

        </div>

        <div v-else class="flex flex-col items-center py-24 space-y-8 animate-pulse">
            <div class="h-10 w-64 bg-[#F9F7F2] rounded-2xl"></div>
            <div class="w-full space-y-4">
                <div v-for="i in 3" :key="i" class="h-24 bg-[#FDFCFB] rounded-[2rem] border border-[#F2E9E4]"></div>
            </div>
        </div>

    </div>
</template>