<script setup>
import { useI18n } from 'vue-i18n'
import { useLocaleFormat } from '@/composables/useLocaleFormat'
import { provide, ref } from 'vue'
import { useRoute } from 'vue-router'
import useApi from '@/composables/useApi'
import sharedDayService from '@/services/sharedDayService'
import CommentsSection from './CommentsSection.vue'
import ExportSharedDay from './ExportSharedDay.vue'
const { t } = useI18n()
const { displayTime, formatDate } = useLocaleFormat()
const route = useRoute()

const { data: sharedDay, error, loading } = useApi(
    () => sharedDayService.getSharedDay(route.params.channel_id, route.params.id), true
)
provide('sharedDay', sharedDay)
import PageShell from '@/components/PageShell.vue'
</script>

<template>
    <PageShell :title="sharedDay ? formatDate(sharedDay.date, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) : ''" narrow>
        <template #badge>
            <span v-if="sharedDay" class="inline-flex px-2.5 py-1 rounded-full bg-[#C0D5D6] text-[#083A4F] text-xs font-semibold tabular-nums whitespace-nowrap">
                {{ sharedDay.total_time.hours }}{{ t('units.hours_short') }} {{ String(sharedDay.total_time.minutes).padStart(2, '0') }}{{ t('units.minutes_short') }}
            </span>
        </template>
        <template #actions>
            <ExportSharedDay v-if="sharedDay" :sharedDayId="sharedDay.id" />
        </template>

        <div v-if="error" class="p-4 bg-[#FEE2E2] text-[#991B1B] rounded-3xl border border-[#FECACA] text-sm font-medium">
            {{ error }}
        </div>

        <template v-else-if="sharedDay">
            <p class="text-sm text-[#5F7480] -mt-2">{{ t('shared_day_view.summary') }}</p>

            <!-- Entries -->
            <div class="bg-white border border-[#D9D4CE] rounded-3xl overflow-hidden divide-y divide-[#D9D4CE]">
                <div
                    v-for="entry in sharedDay.entries"
                    :key="entry.id"
                    class="flex items-center justify-between gap-4 px-5 py-3.5">
                    <div class="min-w-0">
                        <p class="font-medium truncate">{{ entry.time_entry.label }}</p>
                        <p class="text-xs text-[#3E5660] tabular-nums mt-0.5" dir="ltr">
                            {{ displayTime(entry.time_entry.start_time) }} – {{ displayTime(entry.time_entry.end_time) }}
                        </p>
                    </div>
                    <span class="shrink-0 inline-flex px-2.5 py-1 rounded-full bg-[#C0D5D6] text-[#083A4F] text-xs font-semibold tabular-nums">
                        {{ entry.time_entry.time_taken.hours }}{{ t('units.hours_short') }} {{ String(entry.time_entry.time_taken.minutes).padStart(2, '0') }}{{ t('units.minutes_short') }}
                    </span>
                </div>
            </div>

            <!-- Discussion -->
            <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 space-y-4">
                <h3 class="font-semibold">{{ t('shared_day_view.discussion') }}</h3>
                <CommentsSection />
            </section>
        </template>

        <div v-else class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-16 bg-[#EFECE8] rounded-3xl animate-pulse"></div>
        </div>
    </PageShell>
</template>
