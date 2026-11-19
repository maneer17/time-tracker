<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Doughnut } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, ArcElement)

const props = defineProps({
    chartData: { type: Object, required: true },
    chartOptions: {
        type: Object,
        default: () => ({
            responsive: true,
            plugins: {
                legend: { display: true }
            }
        })
    }
})

const { locale } = useI18n()

// Legend and tooltips read right-to-left in Arabic.
const options = computed(() => {
    const base = props.chartOptions
    if (locale.value !== 'ar') return base

    return {
        ...base,
        plugins: {
            ...base.plugins,
            legend: { ...base.plugins?.legend, rtl: true, textDirection: 'rtl' },
            tooltip: { ...base.plugins?.tooltip, rtl: true, textDirection: 'rtl' },
        },
    }
})
</script>

<template>
    <Doughnut :data="props.chartData" :options="options" />
</template>