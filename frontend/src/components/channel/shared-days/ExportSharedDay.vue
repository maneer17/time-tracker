<script setup>
import { ref } from 'vue'
import useExport from '@/composables/useExport'
import exportService from '@/services/exportService'
import { useI18n } from 'vue-i18n'

const { loading, error, download } = useExport()
const { t } = useI18n()
const props = defineProps(['sharedDayId']);
const form = ref({
    file_extension: 'xlsx'
})

const handleExport = () => {
    download(
        (params) => exportService.exportSharedDay(props.sharedDayId, params),
        { file_extension: form.value.file_extension },
        'shared-day'
    )
}
</script>

<template>
    <div class="flex flex-col items-end gap-1">
        <div class="flex items-center gap-2">
            <select v-model="form.file_extension" :aria-label="t('export.format')"
                class="rounded-full border border-[#C0D5D6] bg-white px-3 py-2 text-sm text-[#083A4F] outline-none focus:ring-2 focus:ring-[#083A4F]/15 cursor-pointer">
                <option value="xlsx">Excel</option>
                <option value="pdf">PDF</option>
            </select>

            <button
                @click="handleExport"
                :disabled="loading"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-[#C0D5D6] text-sm font-medium text-[#083A4F] hover:bg-[#EFECE8] transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
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
    </div>
</template>
