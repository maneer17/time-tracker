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
import PageShell from '@/components/PageShell.vue'
</script>

<template>
  <PageShell :title="t('addForm.add_entry')" narrow>
    <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 sm:p-6">
      <p class="text-sm text-[#5F7480] mb-5">{{ t('addForm.subtitle') }}</p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label class="block mb-1.5 text-xs font-medium">{{ t("addForm.label") }}</label>
          <input type="text" v-model="formData.label" required :placeholder="t('addForm.label_placeholder')" class="w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#5F7480]/70 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15">
          <span v-if="error?.label" class="block mt-1 text-xs text-[#991B1B]">{{ error.label[0] }}</span>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <label class="block mb-1.5 text-xs font-medium">{{ t("addForm.start_time") }}</label>
            <input type="time" v-model="formData.startTime" required class="w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#5F7480]/70 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15">
            <span v-if="error?.start_time" class="block mt-1 text-xs text-[#991B1B]">{{ error.start_time[0] }}</span>
          </div>
          <div>
            <label class="block mb-1.5 text-xs font-medium">{{ t("addForm.end_time") }}</label>
            <input type="time" v-model="formData.endTime" required class="w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#5F7480]/70 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15">
            <span v-if="error?.end_time" class="block mt-1 text-xs text-[#991B1B]">{{ error.end_time[0] }}</span>
          </div>
        </div>

        <div v-if="error?.message" class="p-3 bg-[#FEE2E2] text-[#991B1B] text-sm font-medium rounded-xl border border-[#FECACA]">
          {{ error.message }}
        </div>

        <div class="pt-2 flex flex-col gap-2">
          <button type="submit" :disabled="loading" class="w-full py-3 rounded-full bg-[#083A4F] text-white text-sm font-medium transition-colors hover:bg-[#407E8C] disabled:opacity-50">
            {{ loading ? t("addForm.loading") : t("addForm.add_entry") }}
          </button>
          <button type="button" @click="router.back()" class="w-full py-2.5 rounded-full text-sm font-medium text-[#3E5660] hover:bg-[#EFECE8] transition-colors">{{ t("update.cancel") }}</button>
        </div>
      </form>
    </section>
  </PageShell>
</template>
