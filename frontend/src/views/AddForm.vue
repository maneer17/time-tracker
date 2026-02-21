<script setup>
import { ref } from 'vue'
import { Store } from '@/composables/useTimeEntries'
import { useRouter } from 'vue-router' 

const router = useRouter() 
const formData = ref({
  label: '',
  startTime: '',
  endTime: '',
});
const errors = ref(null)

const handleSubmit = async () => {
    errors.value = null
    const result = await Store('/api/time-entries', {
        label: formData.value.label,
        start_time: formData.value.startTime,
        end_time: formData.value.endTime
    })

    if (result.success) {
        router.push({ name: 'Home' })
    } else if (result.errors) {
        errors.value = result.errors
    } else {
        errors.value = { message: ['Something went wrong'] }
    }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[#f5f5f5] p-8">
    <div class="bg-white rounded-lg shadow-md p-8 w-full max-w-[500px]">
      
      <div class="mb-6">
        <h2 class="m-0 text-[#333] text-2xl font-semibold">{{ $t('addForm.add_entry') }}</h2>
      </div>

      <form @submit.prevent="handleSubmit" class="max-w-[500px]">
        <div class="mb-5">
          <label class="block mb-1.5 text-[#555] text-[0.9rem] font-medium">{{ $t("addForm.label") }}</label>
          <input 
            type="text" 
            v-model="formData.label" 
            required
            class="w-full px-2.5 py-2 border border-[#ddd] rounded text-[0.95rem] transition-colors duration-200 box-border focus:outline-none focus:border-[#007bff]"
          >
          <span v-if="errors?.label" class="block mt-1 text-[#cc3333] text-[0.85rem]">{{ errors.label[0] }}</span>
        </div>

        <div class="mb-5">
          <label class="block mb-1.5 text-[#555] text-[0.9rem] font-medium">{{ $t("addForm.start_time") }}</label>
          <input 
            type="time" 
            v-model="formData.startTime" 
            required
            class="w-full px-2.5 py-2 border border-[#ddd] rounded text-[0.95rem] transition-colors duration-200 box-border focus:outline-none focus:border-[#007bff]"
          >
          <span v-if="errors?.start_time" class="block mt-1 text-[#cc3333] text-[0.85rem]">{{ errors.start_time[0] }}</span>
        </div>

        <div class="mb-5">
          <label class="block mb-1.5 text-[#555] text-[0.9rem] font-medium">{{ $t("addForm.end_time") }}</label>
          <input 
            type="time" 
            v-model="formData.endTime" 
            required
            class="w-full px-2.5 py-2 border border-[#ddd] rounded text-[0.95rem] transition-colors duration-200 box-border focus:outline-none focus:border-[#007bff]"
          >
          <span v-if="errors?.end_time" class="block mt-1 text-[#cc3333] text-[0.85rem]">{{ errors.end_time[0] }}</span>
        </div>

        <div v-if="errors?.message" class="mb-4 px-2.5 py-2 text-[#cc3333] text-[0.85rem] bg-[#ffeeee] border border-[#ffcccc] rounded">
          {{ errors.message[0] }}
        </div>

        <button 
          type="submit"
          class="w-full py-3 px-6 bg-[#28a745] text-white border-none rounded cursor-pointer text-base transition-colors duration-200 mt-2 hover:bg-[#218838]"
        >{{ $t("addForm.add_entry") }}</button>
      </form>
    </div>
  </div>
</template>