<script setup>
import { useI18n } from 'vue-i18n'
import { useLocaleFormat } from '@/composables/useLocaleFormat'
const { t } = useI18n()
const { formatDate } = useLocaleFormat()
defineProps({ date: String, active: Boolean })
const emit = defineEmits(['click', 'share'])
</script>

<template>
    <div
        @click="emit('click')"
        class="flex items-center justify-between gap-3 p-3.5 rounded-2xl cursor-pointer text-sm transition-colors border group"
        :class="active
            ? 'bg-[#C0D5D6] border-[#083A4F]/20 text-[#083A4F]'
            : 'bg-white border-[#D9D4CE] text-[#083A4F] hover:bg-[#EFECE8]'">

        <span class="font-medium whitespace-nowrap">{{ formatDate(date, { weekday: 'short', day: 'numeric', month: 'short' }) }}</span>

        <button
            @click.stop="emit('share')"
            class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-white/70 text-[#3E5660] hover:bg-white hover:text-[#083A4F] transition-colors lg:opacity-0 lg:group-hover:opacity-100 focus-visible:opacity-100">
            {{ t('history_date_item.share') }}
        </button>
    </div>
</template>
