<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps({
    chartData: { type: Object, required: true },
    chartOptions: {
        type: Object,
        default: () => ({
            indexAxis: 'y',  
            responsive: true,
            plugins: {
                legend: { display: true }
            }
        })
    }
})

const { locale } = useI18n()

// In Arabic the bars grow from the right, the category labels sit on the right,
// and the legend/tooltips read right-to-left.
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
        scales: {
            ...base.scales,
            x: { ...base.scales?.x, reverse: true },
            y: { ...base.scales?.y, position: 'right' },
        },
    }
})
</script>

<template>
    <Bar :data="props.chartData" :options="options" />
</template>