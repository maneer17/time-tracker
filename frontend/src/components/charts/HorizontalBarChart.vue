<script setup>
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
                legend: { display: false }
            }
        })
    }
})
</script>

<template>
    <Bar :data="props.chartData" :options="props.chartOptions" />
</template>