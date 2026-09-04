<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi';
import timeEntryService from '@/services/timeEntryService';

const { t } = useI18n()
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
  <div class="min-h-[80vh] flex items-center justify-center p-6">
    <div class="bg-white rounded-[3rem] shadow-[0_20px_50px_rgba(0,0,0,0.04)] p-10 w-full max-w-[500px] border border-white">
      
      <div class="mb-10 text-center">
        <h2 class="text-3xl font-black text-[#333] tracking-tight">{{ t('addForm.add_entry') }}</h2>
        <p class="text-[#A0A0A0] text-sm mt-2 font-medium">Log your activity for today</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <div>
          <label class="block mb-2 text-xs font-bold text-[#A0A0A0] uppercase tracking-widest px-1">
            {{ t("addForm.label") }}
          </label>
          <input 
            type="text" 
            v-model="formData.label" 
            required
            placeholder="What are you working on?"
            class="w-full px-6 py-4 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[1rem] transition-all focus:outline-none focus:border-[#D4E2D4] focus:bg-white"
          >
          <span v-if="error?.label" class="block mt-2 px-4 text-[#AF4E4E] text-xs font-bold">{{ error.label[0] }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block mb-2 text-xs font-bold text-[#A0A0A0] uppercase tracking-widest px-1">
              {{ t("addForm.start_time") }}
            </label>
            <input 
              type="time" 
              v-model="formData.startTime" 
              required
              class="w-full px-6 py-4 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[1rem] transition-all focus:outline-none focus:border-[#D4E2D4] focus:bg-white"
            >
            <span v-if="error?.start_time" class="block mt-2 px-4 text-[#AF4E4E] text-xs font-bold">{{ error.start_time[0] }}</span>
          </div>

          <div>
            <label class="block mb-2 text-xs font-bold text-[#A0A0A0] uppercase tracking-widest px-1">
              {{ t("addForm.end_time") }}
            </label>
            <input 
              type="time" 
              v-model="formData.endTime" 
              required
              class="w-full px-6 py-4 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[1rem] transition-all focus:outline-none focus:border-[#D4E2D4] focus:bg-white"
            >
            <span v-if="error?.end_time" class="block mt-2 px-4 text-[#AF4E4E] text-xs font-bold">{{ error.end_time[0] }}</span>
          </div>
        </div>

        <div v-if="error?.message" class="p-4 bg-[#FFF2F2] text-[#AF4E4E] text-xs font-bold rounded-2xl border border-[#FFDADA]">
          {{ error.message }}
        </div>

        <div class="pt-4 flex flex-col gap-3">
          <button 
            type="submit"
            :disabled="loading"
            class="w-full py-5 bg-[#5A7D5A] text-white rounded-[1.5rem] font-black text-lg shadow-xl shadow-[#5A7D5A]/20 transition-all hover:bg-[#4a6b4a] hover:-translate-y-1 active:scale-95 disabled:opacity-50 disabled:transform-none"
          >
            {{ loading ? t("addForm.loading") : t("addForm.add_entry") }}
          </button>
          
          <button 
            type="button"
            @click="router.back()"
            class="w-full py-4 text-[#8E9AAF] font-bold text-sm hover:text-[#333] transition-colors"
          >
            {{ t("update.cancel") || 'Go Back' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>