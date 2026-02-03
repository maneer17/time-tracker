<script setup>
import SingleEntry from '@/views/SingleEntry.vue';
import {ref, onMounted} from 'vue';
const props = defineProps(['entries']);
</script>

<template>
  <div class="list">
    <div v-for="entry in entries" :key="entry.id">
      <SingleEntry :entry="entry"/>
    </div>
  </div>
</template>

<style scoped>
.list {
  max-width: 900px;
}
</style>
