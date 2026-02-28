<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import useApi from '@/composables/useApi';
import timeEntryService from '@/services/timeEntryService';

const router = useRouter()
const formData = ref({
  label: '',
  startTime: '',
  endTime: '',
});

const { loading, error, request } = useApi(() =>
  timeEntryService.createTimeEntry({
    label: formData.value.label,
    start_time: formData.value.startTime,
    end_time: formData.value.endTime
  })
)

const submit = async () => {
  await request()
  if (!error.value) router.push({ name: 'Home' })
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[#f5f5f5] p-8">
    <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-[500px]">
      
      <div class="mb-6">
        <h2 class="m-0 text-[#333] text-2xl font-semibold">{{ $t('addForm.add_entry') }}</h2>
      </div>

      <form @submit.prevent="submit" class="max-w-[500px]">
        <div class="mb-5">
          <label class="block mb-1.5 text-[#555] text-[0.9rem] font-medium">{{ $t("addForm.label") }}</label>
          <input 
            type="text" 
            v-model="formData.label" 
            required
            class="w-full px-2.5 py-2 border border-[#ddd] rounded text-[0.95rem] transition-colors duration-200 box-border focus:outline-none focus:border-[#007bff]"
          >
          <span v-if="error?.label" class="block mt-1 text-[#cc3333] text-[0.85rem]">{{ error.label[0] }}</span>
        </div>

        <div class="mb-5">
          <label class="block mb-1.5 text-[#555] text-[0.9rem] font-medium">{{ $t("addForm.start_time") }}</label>
          <input 
            type="time" 
            v-model="formData.startTime" 
            required
            class="w-full px-2.5 py-2 border border-[#ddd] rounded text-[0.95rem] transition-colors duration-200 box-border focus:outline-none focus:border-[#007bff]"
          >
          <span v-if="error?.start_time" class="block mt-1 text-[#cc3333] text-[0.85rem]">{{ error.start_time[0] }}</span>
        </div>

        <div class="mb-5">
          <label class="block mb-1.5 text-[#555] text-[0.9rem] font-medium">{{ $t("addForm.end_time") }}</label>
          <input 
            type="time" 
            v-model="formData.endTime" 
            required
            class="w-full px-2.5 py-2 border border-[#ddd] rounded text-[0.95rem] transition-colors duration-200 box-border focus:outline-none focus:border-[#007bff]"
          >
          <span v-if="error?.end_time" class="block mt-1 text-[#cc3333] text-[0.85rem]">{{ error.end_time[0] }}</span>
        </div>

        <div v-if="error?.message" class="mb-4 px-2.5 py-2 text-[#cc3333] text-[0.85rem] bg-[#ffeeee] border border-[#ffcccc] rounded">
          {{ error.message }}
        </div>

        <button 
          type="submit"
          :disabled="loading"
          class="w-full py-3 px-6 bg-[#28a745] text-white border-none rounded cursor-pointer text-base transition-colors duration-200 mt-2 hover:bg-[#218838] disabled:opacity-50 disabled:cursor-not-allowed"
        >{{ loading ? $t("addForm.loading") : $t("addForm.add_entry") }}</button>
      </form>
    </div>
  </div>
</template>