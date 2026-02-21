<script setup>
const props = defineProps(['dates'])
const emit = defineEmits(['clickDate'])

function dateClick(date) {
  emit('clickDate', date)
}
</script>

<template>
  <div class="flex flex-col gap-2">
    <div
      v-for="date in dates"
      :key="date"
      @click="dateClick(date)"
      class="p-3 bg-[#f8f9fa] border border-[#e0e0e0] rounded cursor-pointer transition-all duration-200 text-[0.9rem] text-[#333] hover:bg-[#007bff] hover:text-white hover:border-[#007bff] hover:-translate-x-0.5 active:-translate-x-1"
    >
      {{ date }}
    </div>
  </div>
</template>