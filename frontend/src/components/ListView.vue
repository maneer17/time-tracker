<script setup>
import SingleEntry from '@/views/SingleEntry.vue';
import {ref, onMounted} from 'vue';
import BarChart from './BarChart.vue';
const props = defineProps(['entries']);
</script>

<template>
  <div class="list-container">
    <div class="list">
      <div v-for="entry in entries" :key="entry.id">
        <SingleEntry :entry="entry"/>
      </div>
    </div>
    
    <div class="chart-wrapper">
      <BarChart :chart-data="{
        labels: entries.map(row => row.label),
        datasets: [{
          backgroundColor: '#20c997',
          label: 'Entries by label', 
          data: entries.map(row => row.time_taken.hours*60+row.time_taken.minutes)
        }] 
      }" />
    </div>
  </div>
</template>

<style scoped>
.list-container {
  display: flex;
  gap: 2rem;
  width: 100%;
}

.list {
  flex: 1;
  max-width: 600px;
}

.chart-wrapper {
  flex: 0 0 350px;
  min-width: 0;
}
</style>