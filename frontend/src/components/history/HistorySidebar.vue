<script setup>
import { useI18n } from 'vue-i18n'
import { usePagination } from '@/composables/usePagination'
import timeEntryService from '@/services/timeEntryService'
import HistoryDateItem from './HistoryDateItem.vue'
import AppPagination from '@/components/AppPagination.vue'
const { t } = useI18n()
// activeDate: the day currently shown on the page (ISO string or YYYY-MM-DD)
const props = defineProps({ activeDate: String })
const emit = defineEmits(['selectDate', 'shareDate'])

const { items: dates, paginatorData, loading, error, goToPage } = usePagination(
    (page) => timeEntryService.getTimeEntries({ history: true, page }),
    { immediate: true }
)

const isActive = (date) => !!props.activeDate && date.slice(0, 10) === props.activeDate.slice(0, 10)
</script>

<template>
    <aside class="bg-white border-t lg:border-t-0 lg:border-s border-[#D9D4CE] p-4 flex flex-col gap-3 min-w-0">

        <h2 class="font-semibold">{{ t('home.history') }}</h2>

        <div v-if="error" class="p-3 bg-[#FEE2E2] text-[#991B1B] rounded-xl text-sm border border-[#FECACA]">
            {{ t('home.error_loading_dates') }}: {{ error }}
        </div>

        <template v-else>
            <div v-if="loading && !dates.length" class="flex lg:flex-col gap-2">
                <div v-for="i in 5" :key="i" class="h-12 w-32 lg:w-auto shrink-0 bg-[#EFECE8] rounded-2xl animate-pulse" />
            </div>

            <div v-else-if="!dates.length" class="p-4 text-[#5F7480] text-center text-sm">
                {{ t('home.no_dates') }}
            </div>

            <div v-else class="flex flex-col gap-3">
                <!-- A horizontal strip on small screens, a column on large ones -->
                <div class="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-1 lg:pb-0">
                    <HistoryDateItem
                        v-for="date in dates"
                        :key="date.date"
                        :date="date.date"
                        :active="isActive(date.date)"
                        class="shrink-0 lg:shrink"
                        @click="emit('selectDate', date.date)"
                        @share="emit('shareDate', date.date)" />
                </div>

                <AppPagination
                    v-if="paginatorData?.meta"
                    class="hidden lg:flex"
                    :data="paginatorData"
                    @page-change="goToPage" />
            </div>
        </template>
    </aside>
</template>
