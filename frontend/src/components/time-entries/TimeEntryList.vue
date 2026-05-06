<script setup>
import SingleEntry from './SingleEntry.vue';
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
const props = defineProps(['entries']);
</script>

<template>
  <div class="flex flex-col gap-8 w-full">
    <div>
      <router-link :to="{ name: 'AddEntry' }"
        class="inline-flex items-center justify-center bg-[#5A7D5A] hover:bg-[#4a6b4a] text-white px-8 py-4 rounded-[1.5rem] transition-all duration-300 shadow-sm hover:shadow-lg active:scale-95 text-sm font-bold tracking-tight">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        {{ t('home.add_new_entry') }}
      </router-link>
    </div>

    <div class="flex gap-8 w-full">
      <div class="flex-1 max-w-[800px] space-y-4">
        <div v-for="entry in entries" :key="entry.id" 
          class="transition-all duration-300 hover:translate-x-1">
          <SingleEntry :entry="entry"/>
        </div>
      </div>
    </div>
  </div>
</template>