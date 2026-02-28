<template>
  <div class="h-[250px] max-w-[900px] mt-8">
    <Bar
      id="my-chart-id"
      :options="chartOptions"
      :data="chartData"
    />
  </div>
</template>

<script setup>
import { Bar } from 'vue-chartjs'
import { Chart as ChartJS, Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale } from 'chart.js'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const props = defineProps(['chartData'])

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: { display: false },
    tooltip: { enabled: false }
  },
  scales: {
    x: {
      grid: { display: false },
      ticks: { font: { size: 10 } },
      border: { display: false }
    },
    y: {
      beginAtZero: true,
      grid: { display: true },
      ticks: { font: { size: 9 }, padding: 2 },
      border: { display: true }
    }
  },
  datasets: {
    bar: {
      barPercentage: 0.5,
      categoryPercentage: 0.7
    }
  },
  elements: {
    bar: {
      backgroundColor: 'rgba(59, 130, 246, 0.85)',
      borderRadius: 2,
      borderSkipped: 'bottom',
    }
  }
}
</script>