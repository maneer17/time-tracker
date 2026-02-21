<script setup>
import { deleteEntry } from '@/composables/useTimeEntries';
const removeEntry = (entry)=> {
  deleteEntry(`/api/time-entries/${entry.id}`)
}
const props = defineProps(['entry'])
</script>

<template>
  <div class="flex items-center gap-4 p-4 bg-white border border-[#e0e0e0] rounded-md mb-3 transition-all duration-200 hover:shadow-md hover:border-[#ccc]">
    <span class="font-medium text-[#333] flex-1">{{ entry.label }}</span>
    <span class="text-[#666] text-[0.9rem]">{{ entry.start_time }} - {{ entry.end_time }}</span>
    <span class="text-[#007bff] font-semibold text-[0.9rem] py-1 px-2.5 bg-[#e7f3ff] rounded">
      {{ $t('single_entry.hour', entry.time_taken.hours) }} 
      ,
      {{ $t('single_entry.minute', entry.time_taken.minutes) }}
    </span>
    <button @click ="removeEntry(entry)">remove entry</button>
  </div>
</template>
