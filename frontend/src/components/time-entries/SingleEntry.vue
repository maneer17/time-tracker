<script setup>
import { ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import timeEntryService from '@/services/timeEntryService'
import { useLocaleFormat } from '@/composables/useLocaleFormat'

const props = defineProps(['entry'])
const { displayTime } = useLocaleFormat()
const { t } = useI18n()
const refreshEntries = inject('refreshEntries')

const formatTime = (timeStr) => {
    const [time, rawPeriod] = timeStr.split(' ')
    const period = rawPeriod.toUpperCase()
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
  () => timeEntryService.updateTimeEntry({
    label: formData.value.label,
    start_time: formData.value.startTime,
    end_time: formData.value.endTime,
  }, props.entry.id)
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

const duration = () => {
    const { hours, minutes } = props.entry.time_taken
    return `${hours}${t('units.hours_short')} ${String(minutes).padStart(2, '0')}${t('units.minutes_short')}`
}

const field = 'w-full rounded-xl border border-[#C0D5D6] bg-white px-3 py-2.5 text-sm text-[#083A4F] focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15'
const iconBtn = 'inline-flex items-center justify-center w-9 h-9 rounded-lg text-[#3E5660] transition-colors hover:bg-[#EFECE8] hover:text-[#083A4F]'
</script>

<template>
  <article class="px-4 md:px-5 py-3.5 hover:bg-[#FAF9F7] transition-colors">

    <!-- Row -->
    <div v-if="!editing" class="grid grid-cols-[1fr_auto] md:grid-cols-[1fr_170px_120px_96px] gap-x-4 gap-y-2 items-center">
      <h3 class="col-span-2 md:col-span-1 font-medium truncate">{{ entry.label }}</h3>

      <div class="flex items-center gap-3 md:contents">
        <span class="text-xs text-[#3E5660] tabular-nums" dir="ltr">{{ displayTime(entry.start_time) }} – {{ displayTime(entry.end_time) }}</span>
        <span class="justify-self-start inline-flex px-2.5 py-1 rounded-full bg-[#C0D5D6] text-[#083A4F] text-xs font-semibold tabular-nums whitespace-nowrap">{{ duration() }}</span>
      </div>

      <div class="flex items-center justify-end gap-1">
        <template v-if="!confirmDelete">
          <button type="button" @click="editing = true" :aria-label="t('single_entry.edit')" :title="t('single_entry.edit')" :class="iconBtn">
            <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
          </button>
          <button type="button" @click="confirmDelete = true" :aria-label="t('single_entry.delete')" :title="t('single_entry.delete')" :class="[iconBtn, 'hover:!bg-[#FEE2E2] hover:!text-[#991B1B]']">
            <svg class="w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="3 6 5 6 21 6" /><path d="M19 6l-1 14H6L5 6" /><path d="M10 11v6M14 11v6" /><path d="M9 6V4h6v2" /></svg>
          </button>
        </template>

        <div v-else class="flex items-center gap-2 whitespace-nowrap">
          <span class="text-xs font-semibold text-[#991B1B]">{{ t('single_entry.confirm_delete') }}</span>
          <button type="button" @click="remove" :disabled="loading" class="px-3 py-1.5 rounded-lg bg-[#991B1B] text-white text-xs font-semibold hover:bg-[#7F1D1D]">
            {{ loading ? '...' : t('single_entry.yes') }}
          </button>
          <button type="button" @click="confirmDelete = false" :disabled="loading" class="px-3 py-1.5 rounded-lg bg-[#EFECE8] text-[#3E5660] text-xs font-semibold">
            {{ t('single_entry.no') }}
          </button>
        </div>
      </div>
    </div>

    <!-- Edit form -->
    <form v-else @submit.prevent="update" class="space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <label class="flex flex-col gap-1.5">
          <span class="text-xs font-medium">{{ t('update.label') }}</span>
          <input type="text" v-model="formData.label" required :class="field">
        </label>
        <label class="flex flex-col gap-1.5">
          <span class="text-xs font-medium">{{ t('update.start_time') }}</span>
          <input type="time" v-model="formData.startTime" required :class="field">
        </label>
        <label class="flex flex-col gap-1.5">
          <span class="text-xs font-medium">{{ t('update.end_time') }}</span>
          <input type="time" v-model="formData.endTime" required :class="field">
        </label>
      </div>

      <div class="flex gap-3">
        <button type="submit" :disabled="updatingLoading" class="flex-1 py-2.5 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors disabled:opacity-60">
          {{ updatingLoading ? t('update.loading') : t('update.update') }}
        </button>
        <button type="button" @click="editing = false" class="px-6 py-2.5 rounded-full bg-[#EFECE8] text-[#3E5660] text-sm font-medium hover:bg-[#E5E1DD] transition-colors">
          {{ t('update.cancel') }}
        </button>
      </div>
    </form>
  </article>
</template>
