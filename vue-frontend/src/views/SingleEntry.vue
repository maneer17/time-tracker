<script setup>
import {ref, onMounted} from 'vue';
const props = defineProps(['entry'])
</script>

<template>
  <div class="entry">
    <span class="label">{{ entry.label }}</span>
    <span class="time">{{ entry.start_time }} - {{ entry.end_time }}</span>
    <span class="duration">{{ entry.time_taken }}</span>
  </div>
</template>

<style scoped>
.entry {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  background: white;
  border: 1px solid #e0e0e0;
  border-radius: 6px;
  margin-bottom: 0.8rem;
  transition: all 0.2s;
}

.entry:hover {
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
  border-color: #ccc;
}

.label {
  font-weight: 500;
  color: #333;
  flex: 1;
}

.time {
  color: #666;
  font-size: 0.9rem;
}

.duration {
  color: #007bff;
  font-weight: 600;
  font-size: 0.9rem;
  padding: 0.3rem 0.6rem;
  background: #e7f3ff;
  border-radius: 3px;
}
</style>
