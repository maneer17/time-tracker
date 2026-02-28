<script setup>
import SingleEntry from '@/views/SingleEntry.vue';
import {ref, onMounted} from 'vue';
import BarChart from './BarChart.vue';
const props = defineProps(['entries']);
</script>

<template>
  <div class="flex gap-8 w-full">
    <div class="flex-1 max-w-[600px]">
      <div v-for="entry in entries" :key="entry.id">
        <SingleEntry :entry="entry"/>
      </div>
    </div>
    
    <div class="flex-none w-[350px] min-w-0">
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