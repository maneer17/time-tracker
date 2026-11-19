<script setup>
import { ref } from 'vue'
import useApi from '@/composables/useApi';
import exportService from '@/services/exportService'
import { useToast } from 'vue-toastification'
import { useI18n } from 'vue-i18n'
const toast = useToast()
const { t } = useI18n()
const props = defineProps(['channel']);
const { loading, error, request } = useApi(
  () => exportService.exportChannelData(props.channel.id, form.value)
)

const form = ref({
    file_extension: 'xlsx'
})

const handleExport = async () => {
  await request()
  if(!error.value)toast.success(t('export.download_started'))
}
</script>

<template>
    <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 sm:p-6 space-y-3">
        <h3 class="font-semibold">{{ t('export.channel_data') }}</h3>

        <div class="flex items-center gap-2 flex-wrap">
            <select v-model="form.file_extension" :aria-label="t('export.format')"
                class="rounded-full border border-[#C0D5D6] bg-white px-4 py-2 text-sm text-[#083A4F] outline-none focus:ring-2 focus:ring-[#083A4F]/15 cursor-pointer">
                <option value="pdf">PDF</option>
                <option value="xlsx">XLSX</option>
            </select>

            <button
                @click="handleExport"
                :disabled="loading"
                class="inline-flex items-center gap-2 px-5 py-2 bg-[#083A4F] text-white rounded-full text-sm font-medium hover:bg-[#407E8C] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                <svg v-if="!loading" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <svg v-else class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z" />
                </svg>
                {{ loading ? t('export.exporting') : t('export.button') }}
            </button>
        </div>

        <p v-if="error" class="text-xs text-[#991B1B]">{{ error }}</p>
    </section>
</template>
