<script setup>
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
                legend: { display: false }
            }
        })
    }
})
</script>

<template>
    <Doughnut :data="props.chartData" :options="props.chartOptions" />
</template>