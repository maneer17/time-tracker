<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import reportService from '@/services/reportService'
import BarChart from '@/components/charts/BarChart.vue'
import HorizontalBarChart from '@/components/charts/HorizontalBarChart.vue'
import DoughnutChart from '@/components/charts/DoughnutChart.vue'

const { t } = useI18n()

const to = ref(new Date().toISOString().split('T')[0])
const from = ref(new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0])
const isRefresh = ref(false)

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

// THE NEW COLOR PALETTE
const COLORS = [
    '#5A7D5A', // Sage
    '#D4A373', // Soft Brown
    '#E07A5F', // Coral
    '#8E9AAF', // Muted Blue/Grey
    '#D4E2D4', // Light Sage
    '#F2E9E4'  // Rose Cream
]

const totalTimeChartData = computed(() => ({
    labels: data.value.total_time_by_label.map(row => row.label),
    datasets: [{
        data: data.value.total_time_by_label.map(row => row.hours * 60 + row.minutes),
        backgroundColor: '#D4A373', // Soft Brown
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

const avgTimeChartData = computed(() => ({
    labels: data.value.avg_time_per_label.map(row => row.label),
    datasets: [{
        data: data.value.avg_time_per_label.map(row => row.hours * 60 + row.minutes),
        backgroundColor: '#5A7D5A', // Sage
        borderRadius: 12,
    }]
}))

const tooltipMinutesPlugin = {
    plugins: {
        tooltip: {
            backgroundColor: '#4A4A4A',
            padding: 12,
            cornerRadius: 12,
            titleFont: { weight: 'bold' },
            callbacks: {
                label: (context) => {
                    const minutes = context.raw
                    const h = Math.floor(minutes / 60)
                    const m = minutes % 60
                    return ` ${h}h ${m}m`
                }
            }
        }
    }
}
</script>

<template>
    <div class="max-w-5xl mx-auto px-8 py-12">

        <div class="mb-10">
            <h1 class="text-3xl font-black text-[#4A4A4A] tracking-tight">
                {{ t('reports.title') }}
            </h1>
            <p class="text-[15px] text-[#8E9AAF] font-medium mt-1 italic">
                {{ t('reports.subtitle') }}
            </p>
        </div>

        <form @submit.prevent="handleGenerate"
            class="flex flex-wrap items-end gap-6 p-8 bg-white border-2 border-[#F9F7F2] rounded-[2rem] shadow-sm mb-12">

            <div class="flex flex-col gap-2">
                <label class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.15em] px-1">
                    {{ t('reports.from') }}
                </label>
                <input
                    type="date"
                    v-model="from"
                    class="px-5 py-3 bg-[#F9F7F2] border-2 border-transparent rounded-2xl text-[14px] font-bold text-[#4A4A4A] focus:outline-none focus:border-[#5A7D5A] focus:bg-white transition" />
            </div>

            <div class="flex flex-col gap-2">
                <label class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.15em] px-1">
                    {{ t('reports.to') }}
                </label>
                <input
                    type="date"
                    v-model="to"
                    class="px-5 py-3 bg-[#F9F7F2] border-2 border-transparent rounded-2xl text-[14px] font-bold text-[#4A4A4A] focus:outline-none focus:border-[#5A7D5A] focus:bg-white transition" />
            </div>

            <div class="flex items-center gap-4 ml-auto">
                <button
                    type="button"
                    @click="handleRefresh"
                    :disabled="loading"
                    class="px-4 py-3 text-[#A0A0A0] hover:text-[#4A4A4A] text-[13px] font-black uppercase tracking-widest transition disabled:opacity-50">
                    🔄 {{ t('reports.refresh') }}
                </button>
                <button
                    type="submit"
                    :disabled="loading"
                    class="px-8 py-3 bg-[#E07A5F] hover:bg-[#D6684D] text-white text-[13px] font-black uppercase tracking-widest rounded-2xl disabled:opacity-50 transition shadow-xl shadow-[#E07A5F]/20 active:scale-95">
                    {{ loading ? '...' : t('reports.generate') }}
                </button>
            </div>
        </form>

        <div v-if="data" class="space-y-12">

            <section>
                <h2 class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.15em] mb-6 px-2">
                    {{ t('reports.quick_stats.title') }}
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-8 bg-white border-2 border-[#F9F7F2] rounded-[2.5rem] hover:shadow-md transition-shadow">
                        <p class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.1em] mb-3">
                            {{ t('reports.quick_stats.total_entries') }}
                        </p>
                        <p class="text-3xl font-black text-[#4A4A4A]">{{ data.quick_stats.total_entries }}</p>
                    </div>
                    
                    <div class="p-8 bg-white border-2 border-[#F9F7F2] rounded-[2.5rem] hover:shadow-md transition-shadow">
                        <p class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.1em] mb-3">
                            {{ t('reports.quick_stats.total_time') }}
                        </p>
                        <p class="text-3xl font-black text-[#4A4A4A]">
                            {{ data.quick_stats.total_hours }}<span class="text-xl text-[#A0A0A0] ml-1">h</span> 
                            {{ data.quick_stats.total_minutes }}<span class="text-xl text-[#A0A0A0] ml-1">m</span>
                        </p>
                    </div>

                    <div class="p-8 bg-[#5A7D5A] rounded-[2.5rem] shadow-2xl shadow-[#5A7D5A]/20">
                        <p class="text-[11px] font-black text-white/70 uppercase tracking-[0.1em] mb-3">
                            {{ t('reports.quick_stats.daily_avg') }}
                        </p>
                        <p class="text-3xl font-black text-white">
                            {{ data.quick_stats.avg_hours_per_day }}<span class="text-xl opacity-60 ml-1">h</span> 
                            {{ data.quick_stats.avg_minutes_per_day }}<span class="text-xl opacity-60 ml-1">m</span>
                        </p>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                <section class="lg:col-span-3 p-10 bg-white border-2 border-[#F9F7F2] rounded-[3rem]">
                    <h2 class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em] mb-8">
                        {{ t('reports.charts.time_distribution') }}
                    </h2>
                    <HorizontalBarChart :chart-data="totalTimeChartData" :chart-options="chartOptions" />
                </section>

                <section class="lg:col-span-2 p-10 bg-white border-2 border-[#F9F7F2] rounded-[3rem]">
                    <h2 class="text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em] mb-8">
                        {{ t('reports.charts.most_used') }}
                    </h2>
                    <div class="w-full max-w-[220px] mx-auto mb-10">
                        <DoughnutChart :chart-data="mostUsedLabelsChartData" />
                    </div>
                    <div class="space-y-4">
                        <div v-for="(row, i) in data.most_used_labels.slice(0, 4)" :key="i" class="flex items-center justify-between">
                            <span class="text-[14px] font-bold text-[#4A4A4A]">{{ row.label }}</span>
                            <span class="text-[13px] font-black text-[#E07A5F] bg-[#FFF2F0] px-3 py-1 rounded-full">{{ row.percentage }}%</span>
                        </div>
                    </div>
                </section>
            </div>

        </div>
    </div>
</template>