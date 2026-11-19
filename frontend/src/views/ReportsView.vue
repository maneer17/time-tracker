<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import reportService from '@/services/reportService'
import HorizontalBarChart from '@/components/charts/HorizontalBarChart.vue'
import DoughnutChart from '@/components/charts/DoughnutChart.vue'
import ReportExportButton from '@/components/charts/ReportExportButton.vue'
import { useLocaleFormat } from '@/composables/useLocaleFormat'
// ↑ added — was missing, component was used but never imported

const { t } = useI18n()
const { formatDate } = useLocaleFormat()

// ── Chart refs ─────────────────────────────────────────────
const totalTimeChartRef = ref(null)
const mostUsedChartRef  = ref(null)

// ── Export state ───────────────────────────────────────────
const capturedCharts = ref(null)
// holds base64 strings after captureCharts() runs
// starts null — nothing captured yet


const captureCharts = () => {
    const totalTimeCanvas = totalTimeChartRef.value?.$el
    const mostUsedCanvas  = mostUsedChartRef.value?.$el
    if (!totalTimeCanvas || !mostUsedCanvas) {
        console.warn('Charts not ready yet')
        return
    }

    capturedCharts.value = {
        total_time_chart: totalTimeCanvas.toDataURL('image/png'),
        most_used_chart:  mostUsedCanvas.toDataURL('image/png'),
    }
}

// ── Date range ─────────────────────────────────────────────
const localDay = (d) => d.toLocaleDateString('en-CA') // YYYY-MM-DD in the user's own timezone
const to   = ref(localDay(new Date()))
const from = ref(localDay(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000)))
const isRefresh = ref(false)

// ── API ────────────────────────────────────────────────────
const { data, error, loading, request } = useApi(
    () => reportService.generateReport({
        from: from.value,
        to: to.value,
        refresh: isRefresh.value
    }), true
)

const handleGenerate = async () => {
    isRefresh.value = false
    await request()
}

const handleRefresh = async () => {
    isRefresh.value = true
    await request()
    isRefresh.value = false
}

// ── Colors ─────────────────────────────────────────────────
const COLORS = [
    '#083A4F',
    '#A58D66',
    '#991B1B',
    '#5F7480',
    '#C0D5D6',
    '#F3EEE4'
]

// ── Chart data ─────────────────────────────────────────────
const totalTimeChartData = computed(() => ({
    labels: data.value.total_time_by_label.map(row => row.label),
    datasets: [{
        label: t('reports.minutes'),
        data: data.value.total_time_by_label.map(row => Number(row.total_minutes)),
        backgroundColor: '#407E8C',
        borderRadius: 12,
    }]
}))

const mostUsedLabelsChartData = computed(() => ({
    labels: data.value.most_used_labels.map(row => row.label),
    datasets: [{
        data: data.value.most_used_labels.map(row => row.percentage),
        backgroundColor: data.value.most_used_labels.map((_, i) => COLORS[i % COLORS.length]),
        borderWidth: 0,
        hoverOffset: 20
    }]
}))
// ↑ removed avgTimeChartData and tooltipMinutesPlugin — unused

// ── Display helpers ────────────────────────────────────────
const hm = (hours, minutes) =>
    `${hours}${t('units.hours_short')} ${String(minutes).padStart(2, '0')}${t('units.minutes_short')}`
const fromMinutes = (total) => hm(Math.floor(total / 60), Math.round(total % 60))

const rangeLabel = computed(() => t('reports.range', {
    from: formatDate(from.value, { month: 'short', day: 'numeric' }),
    to: formatDate(to.value, { month: 'short', day: 'numeric', year: 'numeric' }),
}))

// One row per label: total time, average per entry and share of the whole range.
const labelRows = computed(() => {
    const rows = data.value?.total_time_by_label ?? []
    const sum = rows.reduce((acc, r) => acc + Number(r.total_minutes), 0)
    const avg = Object.fromEntries((data.value?.avg_time_per_label ?? []).map((r) => [r.label, Number(r.avg_minutes)]))

    return rows.map((r) => ({
        label: r.label,
        total: fromMinutes(Number(r.total_minutes)),
        avg: fromMinutes(avg[r.label] ?? 0),
        share: sum ? Math.round((Number(r.total_minutes) / sum) * 1000) / 10 : 0,
    }))
})
</script>

<template>
    <div class="min-h-screen flex flex-col">

        <!-- Page header -->
        <header class="bg-white border-b border-[#D9D4CE] px-4 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">{{ t('reports.title') }}</h1>
                <span v-if="data" class="inline-flex px-2.5 py-1 rounded-full bg-[#C0D5D6] text-[#083A4F] text-xs font-semibold tabular-nums">
                    {{ hm(data.quick_stats.total_hours, data.quick_stats.total_minutes) }}
                </span>
            </div>

            <!-- Export button only appears once a report has been generated -->
            <ReportExportButton
                v-if="data"
                :from="from"
                :to="to"
                :capture-charts="captureCharts"
                :captured-charts="capturedCharts"
            />
        </header>

        <main class="p-4 lg:p-8 flex flex-col gap-6 min-w-0">

            <!-- Date range -->
            <form @submit.prevent="handleGenerate"
                  class="bg-white border border-[#D9D4CE] rounded-3xl p-4 flex flex-wrap items-end justify-between gap-4">
                <div class="flex flex-wrap items-end gap-3">
                    <label class="flex flex-col gap-1.5">
                        <span class="text-xs font-medium">{{ t('reports.from') }}</span>
                        <input type="date" v-model="from"
                               class="rounded-xl border border-[#C0D5D6] bg-white px-3 py-2 text-sm focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" />
                    </label>

                    <label class="flex flex-col gap-1.5">
                        <span class="text-xs font-medium">{{ t('reports.to') }}</span>
                        <input type="date" v-model="to"
                               class="rounded-xl border border-[#C0D5D6] bg-white px-3 py-2 text-sm focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" />
                    </label>

                    <button type="submit" :disabled="loading"
                            class="h-10 px-5 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors disabled:opacity-50">
                        {{ loading ? '...' : t('reports.generate') }}
                    </button>
                </div>

                <button type="button" @click="handleRefresh" :disabled="loading"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-full text-sm font-medium text-[#3E5660] hover:bg-[#EFECE8] hover:text-[#083A4F] transition-colors disabled:opacity-50">
                    <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4v5h5M20 20v-5h-5M5.64 15A8 8 0 0018.36 9M18.36 9A8 8 0 005.64 15" /></svg>
                    {{ t('reports.refresh') }}
                </button>
            </form>

            <!-- Report content — only renders when data exists -->
            <template v-if="data">

                <!-- Quick stats -->
                <section class="grid grid-cols-2 lg:grid-cols-4 gap-3 lg:gap-4 items-stretch">
                    <article class="bg-white border border-[#D9D4CE] rounded-3xl p-4 lg:p-5 flex flex-col justify-between gap-2">
                        <h2 class="text-xs font-medium text-[#3E5660]">{{ t('reports.quick_stats.total_time') }}</h2>
                        <div>
                            <p class="text-xl lg:text-2xl font-semibold tabular-nums">{{ hm(data.quick_stats.total_hours, data.quick_stats.total_minutes) }}</p>
                            <p class="text-xs text-[#5F7480] mt-0.5">{{ rangeLabel }}</p>
                        </div>
                    </article>

                    <article class="bg-white border border-[#D9D4CE] rounded-3xl p-4 lg:p-5 flex flex-col justify-between gap-2">
                        <h2 class="text-xs font-medium text-[#3E5660]">{{ t('reports.quick_stats.total_entries') }}</h2>
                        <div>
                            <p class="text-xl lg:text-2xl font-semibold tabular-nums">{{ data.quick_stats.total_entries }}</p>
                            <p class="text-xs text-[#5F7480] mt-0.5">{{ t('reports.labels_count', data.total_time_by_label.length) }}</p>
                        </div>
                    </article>

                    <article class="bg-white border border-[#D9D4CE] rounded-3xl p-4 lg:p-5 flex flex-col justify-between gap-2">
                        <h2 class="text-xs font-medium text-[#3E5660]">{{ t('reports.active_days') }}</h2>
                        <div>
                            <p class="text-xl lg:text-2xl font-semibold tabular-nums">{{ data.quick_stats.total_days }}</p>
                            <p class="text-xs text-[#5F7480] mt-0.5">{{ t('reports.labels_per_day', { n: data.avg_labels_per_day }) }}</p>
                        </div>
                    </article>

                    <article class="bg-[#083A4F] text-white rounded-3xl p-4 lg:p-5 flex flex-col justify-between gap-2">
                        <h2 class="text-xs font-medium text-white/70">{{ t('reports.quick_stats.daily_avg') }}</h2>
                        <p class="text-xl lg:text-2xl font-semibold tabular-nums">{{ hm(data.quick_stats.avg_hours_per_day, data.quick_stats.avg_minutes_per_day) }}</p>
                    </article>
                </section>

                <!-- Charts -->
                <section class="grid grid-cols-1 lg:grid-cols-[1.4fr_1fr] gap-4 items-stretch">
                    <article class="bg-white border border-[#D9D4CE] rounded-3xl p-5 flex flex-col gap-4">
                        <h2 class="font-semibold">{{ t('reports.charts.time_distribution') }}</h2>
                        <!-- ref → captureCharts() reads this canvas to build the PDF -->
                        <HorizontalBarChart ref="totalTimeChartRef" :chart-data="totalTimeChartData" />
                    </article>

                    <article class="bg-white border border-[#D9D4CE] rounded-3xl p-5 flex flex-col gap-4">
                        <h2 class="font-semibold">{{ t('reports.charts.most_used') }}</h2>
                        <div class="w-full max-w-[240px] mx-auto">
                            <DoughnutChart ref="mostUsedChartRef" :chart-data="mostUsedLabelsChartData" />
                        </div>
                        <ul class="flex flex-col gap-2.5">
                            <li v-for="(row, i) in data.most_used_labels" :key="row.label" class="flex items-center justify-between gap-3">
                                <span class="flex items-center gap-2 min-w-0">
                                    <span class="w-2.5 h-2.5 rounded-full shrink-0" :style="{ background: COLORS[i % COLORS.length] }"></span>
                                    <span class="text-sm truncate">{{ row.label }}</span>
                                </span>
                                <span class="text-xs font-semibold text-[#083A4F] bg-[#C0D5D6] px-2.5 py-1 rounded-full tabular-nums">{{ row.percentage }}%</span>
                            </li>
                        </ul>
                    </article>
                </section>

                <!-- Time by label -->
                <section class="bg-white border border-[#D9D4CE] rounded-3xl overflow-hidden">
                    <h2 class="px-5 py-4 font-semibold border-b border-[#D9D4CE]">{{ t('reports.table_title') }}</h2>

                    <div class="px-5 py-3 bg-[#EFECE8] border-b border-[#D9D4CE] grid grid-cols-[1fr_auto_auto] sm:grid-cols-[1fr_110px_110px_70px] gap-4 text-xs font-medium text-[#3E5660]">
                        <span>{{ t('reports.col_label') }}</span>
                        <span class="hidden sm:block text-end">{{ t('reports.col_avg') }}</span>
                        <span class="text-end">{{ t('reports.col_total') }}</span>
                        <span class="text-end">{{ t('reports.col_share') }}</span>
                    </div>

                    <div class="divide-y divide-[#D9D4CE]">
                        <div v-for="row in labelRows" :key="row.label"
                             class="px-5 py-3 grid grid-cols-[1fr_auto_auto] sm:grid-cols-[1fr_110px_110px_70px] gap-4 items-center text-sm">
                            <span class="truncate">{{ row.label }}</span>
                            <span class="hidden sm:block text-end text-[#3E5660] tabular-nums">{{ row.avg }}</span>
                            <span class="text-end tabular-nums">{{ row.total }}</span>
                            <span class="hidden sm:block text-end font-semibold text-[#083A4F] tabular-nums">{{ row.share }}%</span>
                        </div>
                    </div>
                </section>
            </template>
        </main>
    </div>
</template>
