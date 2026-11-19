<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import timeEntryService from '@/services/timeEntryService'

const emit = defineEmits(['created'])
const { t } = useI18n()

const form = ref({ label: '', start: '', end: '' })

const { loading, error, request } = useApi(() =>
  timeEntryService.createTimeEntry({
    label: form.value.label,
    start_time: form.value.start,
    end_time: form.value.end,
  })
)

const minutes = computed(() => {
  if (!form.value.start || !form.value.end) return null
  const [sh, sm] = form.value.start.split(':').map(Number)
  const [eh, em] = form.value.end.split(':').map(Number)
  return eh * 60 + em - (sh * 60 + sm)
})

const preview = computed(() => {
  if (minutes.value === null) return ''
  if (minutes.value <= 0) return t('home.invalid_range')
  const value = `${Math.floor(minutes.value / 60)}${t('units.hours_short')} ${String(minutes.value % 60).padStart(2, '0')}${t('units.minutes_short')}`
  return t('home.duration', { value })
})

const submit = async () => {
  await request()
  if (!error.value) {
    form.value = { label: '', start: '', end: '' }
    emit('created')
  }
}

const field = 'w-full rounded-xl border border-[#C0D5D6] bg-white px-3 py-2.5 text-sm text-[#083A4F] placeholder:text-[#5F7480]/70 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15'
const label = 'text-xs font-medium text-[#083A4F]'
</script>

<template>
  <section class="bg-white border border-[#D9D4CE] rounded-3xl p-4 sm:p-5">
    <div class="flex items-center justify-between gap-2 mb-4">
      <h2 class="font-semibold">{{ t('home.log_time_entry') }}</h2>
      <span class="text-xs font-medium tabular-nums" :class="minutes !== null && minutes <= 0 ? 'text-[#991B1B]' : 'text-[#407E8C]'">{{ preview }}</span>
    </div>

    <form @submit.prevent="submit" class="grid grid-cols-2 lg:grid-cols-[1fr_130px_130px_auto] gap-3 items-end">
      <label class="flex flex-col gap-1.5 col-span-2 lg:col-span-1">
        <span :class="label">{{ t('addForm.label') }}</span>
        <input v-model="form.label" type="text" required :placeholder="t('addForm.label_placeholder')" :class="field" />
        <span v-if="error?.label" class="text-xs text-[#991B1B]">{{ error.label[0] }}</span>
      </label>

      <label class="flex flex-col gap-1.5">
        <span :class="label">{{ t('addForm.start_time') }}</span>
        <input v-model="form.start" type="time" required :class="field" />
        <span v-if="error?.start_time" class="text-xs text-[#991B1B]">{{ error.start_time[0] }}</span>
      </label>

      <label class="flex flex-col gap-1.5">
        <span :class="label">{{ t('addForm.end_time') }}</span>
        <input v-model="form.end" type="time" required :class="field" />
        <span v-if="error?.end_time" class="text-xs text-[#991B1B]">{{ error.end_time[0] }}</span>
      </label>

      <button type="submit" :disabled="loading"
              class="col-span-2 lg:col-span-1 inline-flex items-center justify-center gap-2 px-5 py-2.5 h-[42px] rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors disabled:opacity-60">
        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
        {{ loading ? t('addForm.loading') : t('addForm.add_entry') }}
      </button>
    </form>
  </section>
</template>
