<script setup>
import { ref, computed, watch, provide } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleFormat } from '@/composables/useLocaleFormat'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import { usePagination } from '@/composables/usePagination'
import timeEntryService from '@/services/timeEntryService'
import HistorySidebar from '@/components/history/HistorySidebar.vue'
import ShareDayModal from '@/components/time-entries/ShareDayModal.vue'
import TimeEntryList from '@/components/time-entries/TimeEntryList.vue'
import QuickLogForm from '@/components/time-entries/QuickLogForm.vue'
import ExportPanel from '@/components/time-entries/ExportPanel.vue'
import AppPagination from '@/components/AppPagination.vue'

const { t } = useI18n()
const { formatDate } = useLocaleFormat()
const router = useRouter()
const toast = useToast()

const filters = ref({ search: '', date: null, sort: '' })

const { items: entries, paginatorData, loading, error, goToPage, refresh } = usePagination(
    (page) => timeEntryService.getTimeEntries({ page, ...filters.value }),
    { immediate: true }
)

watch(filters, () => refresh(), { deep: true })

provide('refreshEntries', refresh)

const title = ref(t('home.today_entries'))
const showExportPanel = ref(false)
const showShareModal = ref(false)
const selectedShareDate = ref(null)

const totalMinutes = computed(() => {
    if (!entries.value?.length) return 0
    return entries.value.reduce((acc, entry) =>
        acc + (entry.time_taken.hours * 60 + entry.time_taken.minutes), 0
    )
})

const totalLabel = computed(() =>
    `${Math.floor(totalMinutes.value / 60)}${t('units.hours_short')} ${String(totalMinutes.value % 60).padStart(2, '0')}${t('units.minutes_short')}`
)

// The server decides what "today" is in UTC, so the share button asks for the same day.
const todayIso = () => new Date().toISOString().slice(0, 10)

const goToDate = (date) => {
    filters.value.date = date
    title.value = `${t('home.entries_of')} ${formatDate(date, { day: 'numeric', month: 'long', year: 'numeric' })}`
}

const todayEntries = () => {
    filters.value = { search: '', date: null, sort: '' }
    title.value = t('home.today_entries')
}

const handleShareDate = (date) => {
    selectedShareDate.value = date
    showShareModal.value = true
}

const handleShared = () => {
    showShareModal.value = false
    toast.success(t('share_day.shared_success'))
    router.push({ name: 'MySharedDays' })
}

const shareCurrentDay = () => handleShareDate(filters.value.date ?? todayIso())
</script>

<template>
    <div class="min-h-screen flex flex-col">

        <!-- Page header -->
        <header class="bg-white border-b border-[#D9D4CE] px-4 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3 min-w-0">
                <h1 class="text-2xl lg:text-3xl font-bold tracking-tight truncate">{{ title }}</h1>
                <span class="inline-flex px-2.5 py-1 rounded-full bg-[#C0D5D6] text-[#083A4F] text-xs font-semibold tabular-nums whitespace-nowrap">{{ totalLabel }}</span>
            </div>

            <div class="flex items-center gap-2">
                <button
                    @click="shareCurrentDay"
                    :disabled="!entries.length"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors disabled:bg-[#C0D5D6] disabled:text-[#3E5660] disabled:cursor-not-allowed">
                    <!-- box with an arrow leaving it: the usual "share / send out" symbol -->
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 8H7.5A2.25 2.25 0 005.25 10.25v8.25A2.25 2.25 0 007.5 20.75h9a2.25 2.25 0 002.25-2.25v-8.25A2.25 2.25 0 0016.5 8H15m-3-5.25L9 5.75m3-3l3 3m-3-3v11.5" /></svg>
                    {{ t('home.share_day') }}
                </button>
                <button
                    @click="showExportPanel = !showExportPanel"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-medium text-[#3E5660] hover:bg-[#EFECE8] hover:text-[#083A4F] transition-colors"
                    :class="{ 'bg-[#EFECE8] text-[#083A4F]': showExportPanel }">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    {{ t('export.button') }}
                </button>
            </div>
        </header>

        <div class="flex-1 grid grid-cols-1 lg:grid-cols-[1fr_280px]">

            <!-- Main column (below the history strip on small screens) -->
            <section class="order-2 lg:order-1 p-4 lg:p-8 flex flex-col gap-6 min-w-0">

                <ExportPanel v-if="showExportPanel" />

                <QuickLogForm @created="refresh" />

                <!-- Search, count and sort -->
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <input
                        v-model="filters.search"
                        type="search"
                        :placeholder="t('home.search_placeholder')"
                        class="flex-1 min-w-[200px] max-w-md rounded-xl border border-[#C0D5D6] bg-white px-4 py-2.5 text-sm focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" />

                    <div class="flex items-center gap-3">
                        <span class="text-xs text-[#5F7480] tabular-nums">{{ t('home.entries_count', entries.length) }}</span>

                        <select
                            v-model="filters.sort"
                            class="rounded-xl border border-[#C0D5D6] bg-white px-3 py-2.5 text-sm cursor-pointer focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15">
                            <option value="">{{ t('home.sort_default') }}</option>
                            <option value="desc">{{ t('home.sort_oldest') }}</option>
                            <option value="asc">{{ t('home.sort_latest') }}</option>
                        </select>

                        <button
                            @click="todayEntries"
                            class="px-4 py-2.5 rounded-xl bg-[#C0D5D6] text-[#083A4F] text-sm font-medium hover:bg-[#A9C4C6] transition-colors whitespace-nowrap">
                            {{ t('home.today_entries') }}
                        </button>
                    </div>
                </div>

                <!-- Error -->
                <div v-if="error" class="p-5 bg-[#FEE2E2] text-[#991B1B] rounded-3xl border border-[#FECACA]">
                    {{ t('home.error') }}: {{ error }}
                </div>

                <template v-else>
                    <!-- Loading (only on first load, not on page change) -->
                    <div v-if="loading && !entries.length" class="flex flex-col items-center justify-center py-16 text-[#5F7480]">
                        <div class="w-10 h-10 border-4 border-[#C0D5D6] border-t-[#083A4F] rounded-full animate-spin mb-3"></div>
                        <p class="font-medium">{{ t('home.loading') }}</p>
                    </div>

                    <div v-else-if="!entries.length" class="p-8 text-[#5F7480] text-center bg-white border border-dashed border-[#D9D4CE] rounded-3xl">
                        {{ t('home.no_entries') }}
                    </div>

                    <TimeEntryList v-else :entries="entries" />

                    <AppPagination
                        v-if="paginatorData?.meta"
                        :data="paginatorData"
                        @page-change="goToPage" />
                </template>
            </section>

            <HistorySidebar
                class="order-1 lg:order-2"
                :active-date="filters.date ?? todayIso()"
                @select-date="goToDate"
                @share-date="handleShareDate" />
        </div>

        <ShareDayModal
            v-if="showShareModal"
            :date="selectedShareDate"
            @close="showShareModal = false"
            @shared="handleShared" />
    </div>
</template>
