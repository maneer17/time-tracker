<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
defineProps(['date'])
const emit = defineEmits(['click', 'share'])
</script>

<template>
    <div
        @click="emit('click')"
        class="flex items-center justify-between p-4 bg-white/50 rounded-2xl cursor-pointer transition-all duration-300 text-[0.95rem] text-[#4A4A4A] hover:bg-[#E8F0E8] hover:shadow-sm hover:-translate-y-0.5 active:scale-95 group border border-transparent hover:border-white">
        
        <span class="font-medium tracking-tight">{{ date }}</span>
        
        <button
            @click.stop="emit('share')"
            class="text-[0.7rem] font-bold uppercase tracking-wider px-3 py-1.5 bg-[#F0F4F8] text-[#8E9AAF] rounded-xl hover:bg-white hover:text-[#5A7D5A] transition-all duration-300 opacity-0 group-hover:opacity-100 shadow-sm">
            {{ t('history_date_item.share') }}
        </button>
    </div>
</template>