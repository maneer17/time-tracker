<script setup>
import { ref } from 'vue'
import useExport from '@/composables/useExport'
import exportService from '@/services/exportService'
import { useI18n } from 'vue-i18n'

const { loading, error, download } = useExport()
const { t } = useI18n()

const form = ref({
    start_date: '',
    end_date: '',
    file_extension: 'xlsx'
})

const handleExport = () => {
    download(
        exportService.exportTimeEntries,
        form.value,
        'time-entries'
    )
}
</script>

<template>
    <div class="bg-white rounded-3xl p-6 border border-white/50 space-y-4">

        <p class="text-[0.7rem] font-bold text-[#5F7480]">
            {{ t('export.time_entries') }}
        </p>

        <div class="flex gap-3 flex-wrap items-end">

            <div class="flex flex-col gap-1">
                <label class="text-xs text-[#5F7480] font-medium">{{ t('export.from') }}</label>
                <input type="date" v-model="form.start_date"
                    class="px-4 py-2.5 bg-[#FAF9F7] border border-[#D9D4CE] rounded-xl text-sm text-[#083A4F] outline-none focus:ring-2 focus:ring-[#C0D5D6] transition-all" />
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs text-[#5F7480] font-medium">{{ t('export.to') }}</label>
                <input type="date" v-model="form.end_date"
                    class="px-4 py-2.5 bg-[#FAF9F7] border border-[#D9D4CE] rounded-xl text-sm text-[#083A4F] outline-none focus:ring-2 focus:ring-[#C0D5D6] transition-all" />
            </div>

            <div class="flex flex-col gap-1">
                <label class="text-xs text-[#5F7480] font-medium">{{ t('export.format') }}</label>
                <select v-model="form.file_extension"
                    class="px-4 py-2.5 bg-[#FAF9F7] border border-[#D9D4CE] rounded-xl text-sm text-[#083A4F] outline-none focus:ring-2 focus:ring-[#C0D5D6] cursor-pointer transition-all">
                    <option value="xlsx">Excel</option>
                    <option value="csv">CSV</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>

            <button
                @click="handleExport"
                :disabled="loading"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#083A4F] text-white rounded-xl text-sm font-bold tracking-tight hover:bg-[#407E8C] transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed">
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
                {{ loading ? t('export.exporting') : t('export.button') }}
            </button>

        </div>

        <p v-if="error" class="text-xs text-[#991B1B] mt-1">{{ error }}</p>

    </div>
</template>