<script setup>
import { ref } from 'vue'
import useApi from '@/composables/useApi';
import exportService from '@/services/exportService'
import { useToast } from 'vue-toastification'
const toast = useToast()
const props = defineProps(['channel']);
const { loading, error, request } = useApi(
  () => exportService.exportChannelData(props.channel.id, form.value)
)

const form = ref({
    file_extension: 'xlsx'
})

const handleExport = async () => {
  await request()
  if(!error.value)toast.success("Your download will start soon ")
}
</script>

<template>
    <div class="bg-white rounded-[2rem] p-6 shadow-[0_8px_30px_rgb(0,0,0,0.06)] 
                border border-white/50 space-y-4">

        <p class="text-[0.7rem] font-bold text-[#A0A0A0] uppercase tracking-[0.1em]">
            Export This Day
        </p>

        <div class="flex gap-3 flex-wrap items-end">
            <div class="flex flex-col gap-1">
                <label class="text-xs text-[#A0A0A0] font-medium">Format</label>
                <select v-model="form.file_extension"
                    class="px-4 py-2.5 bg-[#F9F7F2] border border-[#E8E8E8] 
                           rounded-xl text-sm text-[#4A4A4A] outline-none 
                           focus:ring-2 focus:ring-[#D4E2D4] cursor-pointer transition-all">
                    <option value="pdf">PDF</option>
                    <option value="xlsx">XLSX</option>
                </select>
            </div>

            <button
                @click="handleExport"
                :disabled="loading"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#5A7D5A] 
                       text-white rounded-xl text-sm font-bold tracking-tight
                       hover:bg-[#4a6b4a] transition-all duration-300 
                       disabled:opacity-50 disabled:cursor-not-allowed">
                <svg v-if="!loading" xmlns="http://www.w3.org/2000/svg"
                    class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <svg v-else class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
                {{ loading ? 'Exporting...' : 'Export' }}
            </button>

        </div>

        <p v-if="error" class="text-xs text-[#AF4E4E] mt-1">{{ error }}</p>

    </div>
</template>