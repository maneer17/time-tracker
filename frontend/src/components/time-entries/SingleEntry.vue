<script setup>
import { ref, inject } from 'vue'
import useApi from '@/composables/useApi'
import timeEntryService from '@/services/timeEntryService'

const props = defineProps(['entry'])
const refreshEntries = inject('refreshEntries')

const formatTime = (timeStr) => {
    const [time, period] = timeStr.split(' ')
    let [hours, minutes] = time.split(':').map(Number)

    if (period === 'PM' && hours !== 12) hours += 12
    if (period === 'AM' && hours === 12) hours = 0

    return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}`
}

const formData = ref({
    label:     props.entry.label,
    startTime: formatTime(props.entry.start_time),
    endTime:   formatTime(props.entry.end_time),
})

const confirmDelete = ref(false)
const editing = ref(false)

const { loading, error, request } = useApi(
  () => timeEntryService.deleteTimeEntry(props.entry.id)
)
const { loading: updatingLoading, error: updatingError, request: updatingRequest } = useApi(
  () => timeEntryService.updateTimeEntry(formData, props.entry.id)
)

const remove = async () => {
    await request()
    if (!error.value) refreshEntries()
}

const update = async () => {
    await updatingRequest()
    if (!updatingError.value) {
        refreshEntries()
        editing.value = false
    }
}
</script>

<template>
  <div class="p-6 bg-white rounded-[2rem] mb-4 transition-all duration-300 shadow-[0_4px_20px_rgba(0,0,0,0.03)] border border-white hover:shadow-[0_8px_30px_rgba(0,0,0,0.06)] group relative">
    
    <div v-if="!editing" class="flex items-center justify-between w-full">
      <div class="flex flex-col gap-1 flex-1">
        <span class="font-bold text-[#333] text-lg tracking-tight">{{ entry.label }}</span>
        <span class="text-[#A0A0A0] text-sm font-medium">{{ entry.start_time }} — {{ entry.end_time }}</span>
      </div>

      <div class="flex items-center gap-4">
        <span class="text-[#5A7D5A] font-bold text-xs py-2 px-4 bg-[#E8F0E8] rounded-xl whitespace-nowrap">
          {{ $t('single_entry.hour', entry.time_taken.hours) }} 
          <span class="opacity-40 mx-1">/</span>
          {{ $t('single_entry.minute', entry.time_taken.minutes) }}
        </span>

        <button 
          @click="editing = true"
          class="p-2 text-[#BCBCBC] hover:text-[#5A7D5A] transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
          </svg>
        </button>
      </div>
    </div>

    <div v-else class="w-full">
      <form @submit.prevent="update" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="block mb-2 text-xs font-bold text-[#A0A0A0] uppercase tracking-wider">{{ $t("update.label") }}</label>
            <input type="text" v-model="formData.label" required class="w-full px-4 py-3 bg-[#F9F7F2] border-none rounded-xl focus:ring-2 focus:ring-[#D4E2D4] outline-none transition-all">
          </div>
          <div>
            <label class="block mb-2 text-xs font-bold text-[#A0A0A0] uppercase tracking-wider">{{ $t("update.start_time") }}</label>
            <input type="time" v-model="formData.startTime" required class="w-full px-4 py-3 bg-[#F9F7F2] border-none rounded-xl focus:ring-2 focus:ring-[#D4E2D4] outline-none transition-all">
          </div>
          <div>
            <label class="block mb-2 text-xs font-bold text-[#A0A0A0] uppercase tracking-wider">{{ $t("update.end_time") }}</label>
            <input type="time" v-model="formData.endTime" required class="w-full px-4 py-3 bg-[#F9F7F2] border-none rounded-xl focus:ring-2 focus:ring-[#D4E2D4] outline-none transition-all">
          </div>
        </div>

        <div class="flex gap-3 pt-2">
          <button type="submit" :disabled="updatingLoading" class="flex-1 py-3 bg-[#5A7D5A] text-white rounded-xl font-bold text-sm shadow-sm hover:bg-[#4a6b4a] transition-all">
            {{ updatingLoading ? $t("update.loading") : $t("update.update") || $t("update.save_changes") }}
          </button>
          <button type="button" @click="editing = false" class="px-6 py-3 bg-[#F0F4F8] text-[#8E9AAF] rounded-xl font-bold text-sm hover:bg-gray-200 transition-all">
            Cancel
          </button>
        </div>
      </form>
    </div>

    <div class="absolute -top-2 -right-2 flex gap-2">
      <template v-if="!confirmDelete">
        <button
          @click="confirmDelete = true"
          class="opacity-0 group-hover:opacity-100 transition-all duration-300 p-2 bg-white shadow-md rounded-full text-[#BCBCBC] hover:text-[#AF4E4E]"
        >
          <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/>
          </svg>
        </button>
      </template>

      <template v-else>
        <div class="flex items-center gap-2 bg-white p-2 rounded-2xl shadow-xl border border-[#FFDADA] animate-in fade-in zoom-in duration-200">
          <span class="text-[0.7rem] font-bold px-2 text-[#AF4E4E]">{{ $t('single_entry.confirm_delete') }}</span>
          <button @click="remove" :disabled="loading" class="px-3 py-1.5 bg-[#AF4E4E] text-white text-[0.7rem] font-bold rounded-lg hover:bg-[#903d3d]">
            {{ loading ? '...' : $t('single_entry.yes') }}
          </button>
          <button @click="confirmDelete = false" :disabled="loading" class="px-3 py-1.5 bg-[#F0F4F8] text-[#8E9AAF] text-[0.7rem] font-bold rounded-lg">
            {{ $t('single_entry.no') }}
          </button>
        </div>
      </template>
    </div>

  </div>
</template>