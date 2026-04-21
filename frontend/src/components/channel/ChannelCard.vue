<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
const props = defineProps({
    channel: { type: Object, required: true }
})
</script>

<template>
    <div class="group relative bg-white rounded-[2.5rem] p-8 border-2 border-[#F9F7F2] transition-all duration-500 flex flex-col h-full cursor-pointer hover:shadow-[0_20px_50px_rgba(90,125,90,0.1)] hover:border-[#D4E2D4]">

        <div class="absolute top-6 right-8 h-2.5 w-2.5 rounded-full bg-[#5A7D5A] opacity-40 group-hover:opacity-100 group-hover:animate-pulse"></div>

        <div class="flex items-center gap-5 mb-6">
            <div class="h-16 w-16 rounded-[1.8rem] bg-[#5A7D5A] flex items-center justify-center text-white font-black text-2xl shadow-lg shadow-[#5A7D5A]/20 transition-all duration-500 group-hover:rounded-[1.2rem] group-hover:rotate-3">
                {{ channel.name.charAt(0) }}
            </div>
            
            <div class="flex-1 min-w-0">
                <h3 class="font-black text-[#4A4A4A] text-xl leading-tight truncate transition-colors group-hover:text-[#5A7D5A]">
                    {{ channel.name }}
                </h3>
                <span class="text-[10px] font-bold text-[#A0A0A0] uppercase tracking-widest">Workspace</span>
            </div>
        </div>

        <p class="text-[#6B6B6B] text-sm line-clamp-2 leading-relaxed flex-grow font-medium italic">
            "{{ channel.description || t('channel_card.no_description') }}"
        </p>

        <div class="mt-8 pt-6 border-t border-[#F9F7F2] flex items-center justify-between">
            <div class="flex items-center gap-2 px-4 py-1.5 bg-[#F9F7F2] rounded-full text-[#5A7D5A]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <span class="text-xs font-black">{{ channel.members_count }}</span>
            </div>

            <div class="opacity-0 group-hover:opacity-100 transition-all duration-300 flex items-center text-[#D4A373] font-black text-xs uppercase tracking-[0.2em] gap-2">
                {{ t('channel_card.enter') }}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </div>
        </div>

    </div>
</template>