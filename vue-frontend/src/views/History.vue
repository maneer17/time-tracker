<script setup>
const props = defineProps(['dates'])
const emit = defineEmits(['clickDate'])

function dateClick(date) {
  emit('clickDate',date)
}
</script>

<template>
  <div class="dates">
    <div
      v-for="date in dates"
      :key="date"
      class="item"
      @click="dateClick(date)"
    >
      {{ date }}
    </div>
  </div>
</template>

<style scoped>
.dates {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.item {
  padding: 0.8rem;
  background: #f8f9fa;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 0.9rem;
  color: #333;
}

.item:hover {
  background: #007bff;
  color: white;
  border-color: #007bff;
  transform: translateX(-2px);
}

.item:active {
  transform: translateX(-4px);
}
</style>
