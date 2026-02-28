<script setup>
import { ref } from 'vue'
import useApi from '@/composables/useApi'
import router from '@/router'
import timeEntryService from '@/services/timeEntryService'

const props = defineProps(['entry'])
const confirmDelete = ref(false)

const { loading, error, request } = useApi(
  () => timeEntryService.deleteTimeEntry(props.entry.id)
)

const remove = async () => {
  await request()
  if (!error.value) router.go(0)
}
</script>

<template>
  <div class="flex items-center gap-4 p-4 bg-white border border-[#e0e0e0] rounded-md mb-3 transition-all duration-200 hover:shadow-md hover:border-[#ccc] group">
    <span class="font-medium text-[#333] flex-1">{{ entry.label }}</span>
    <span class="text-[#666] text-[0.9rem]">{{ entry.start_time }} - {{ entry.end_time }}</span>
    <span class="text-[#007bff] font-semibold text-[0.9rem] py-1 px-2.5 bg-[#e7f3ff] rounded">
      {{ $t('single_entry.hour', entry.time_taken.hours) }} 
      ,
      {{ $t('single_entry.minute', entry.time_taken.minutes) }}
    </span>

    <template v-if="!confirmDelete">
      <button
        @click="confirmDelete = true"
        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 p-1.5 rounded text-[#999] hover:text-[#cc3333] hover:bg-[#ffeeee]"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <polyline points="3 6 5 6 21 6"/>
          <path d="M19 6l-1 14H6L5 6"/>
          <path d="M10 11v6M14 11v6"/>
          <path d="M9 6V4h6v2"/>
        </svg>
      </button>
    </template>

    <template v-else>
      <span class="text-[#555] text-[0.85rem]">{{ $t('single_entry.confirm_delete') }}</span>
      <button
        @click="remove"
        :disabled="loading"
        class="px-2.5 py-1 text-[0.85rem] bg-[#cc3333] text-white rounded hover:bg-[#aa2222] disabled:opacity-50 transition-colors duration-150"
      >{{ loading ? '...' : $t('single_entry.yes') }}</button>
      <button
        @click="confirmDelete = false"
        :disabled="loading"
        class="px-2.5 py-1 text-[0.85rem] border border-[#ddd] rounded hover:bg-[#f5f5f5] disabled:opacity-50 transition-colors duration-150"
      >{{ $t('single_entry.no') }}</button>
    </template>

  </div>
</template>
