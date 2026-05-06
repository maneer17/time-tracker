<script setup>
import { useI18n } from 'vue-i18n'
import { usePagination } from '@/composables/usePagination'
import timeEntryService from '@/services/timeEntryService'
import HistoryDateItem from './HistoryDateItem.vue'
import AppPagination from '@/components/AppPagination.vue'
const { t } = useI18n()
defineProps(['show'])
const emit = defineEmits(['selectDate', 'shareDate'])


const { items: dates, paginatorData, loading, error, goToPage, refresh } = usePagination(
    (page) => timeEntryService.getTimeEntries({ history: true, page }),
    { immediate: true }
)

</script>

<template>
    <div
        class="fixed right-0 top-[60px] w-[280px] h-[calc(100vh-60px)] bg-white border-l border-[#ddd] p-6 overflow-y-auto shadow-[-2px_0_8px_rgba(0,0,0,0.1)] transition-transform duration-300"
        :class="show ? 'translate-x-0' : 'translate-x-full'">

        <h3 class="mt-0 mb-4 text-[#333] text-[1.2rem]">{{ t('home.history') }}</h3>

        <div v-if="error" class="p-4 bg-[#ffeeee] text-[#cc3333] rounded border border-[#ffcccc]">
            {{ t('home.error_loading_dates') }}: {{ error }}
        </div>

        <template v-else>
            <div v-if="loading && !dates.length" class="space-y-2">
                <div v-for="i in 5" :key="i" class="h-10 bg-gray-100 rounded animate-pulse" />
            </div>

            <div v-else-if="!dates.length" class="p-4 text-[#999] text-center">
                {{ t('home.no_dates') }}
            </div>

            <div v-else class="flex flex-col gap-2">
                <HistoryDateItem
                    v-for="date in dates"
                    :key="date.date"
                    :date="date.date"
                    @click="emit('selectDate', date.date)"
                    @share="emit('shareDate', date.date)" />

                    <AppPagination 
                    v-if="paginatorData.meta" 
                    :data="paginatorData" 
                    @page-change="goToPage" />
            </div>
        </template>

    </div>
</template>