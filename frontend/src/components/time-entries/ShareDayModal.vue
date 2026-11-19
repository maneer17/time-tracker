<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleFormat } from '@/composables/useLocaleFormat'
import timeEntryService from '@/services/timeEntryService'
import channelService from '@/services/channelService'
import sharedDayService from '@/services/sharedDayService'
import useApi from '@/composables/useApi'

const { t } = useI18n()
const { displayTime, formatDate } = useLocaleFormat()
const props = defineProps(['date'])
const emit = defineEmits(['close', 'shared'])

const selectedEntries = ref([])
const selectedChannels = ref([])

const { data: timeEntries, error: entriesError, loading: entriesLoading } = useApi(
    () => timeEntryService.getTimeEntries({ date: props.date }), true
)

const { data: channels, error: channelsError, loading: channelsLoading } = useApi(
    () => channelService.getChannels({ type: 'owned' }), true
)

const { error, loading, request } = useApi(
    () => sharedDayService.createSharedDay({
        channel_ids: selectedChannels.value,
        entry_ids: selectedEntries.value,
        date: props.date
    })
)

const handleSubmit = async () => {
    await request()
    if (!error.value) {
        emit('close')
        emit('shared')
    }
}

// Escape closes the dialog
const onKey = (e) => { if (e.key === 'Escape') emit('close') }
onMounted(() => window.addEventListener('keydown', onKey))
onUnmounted(() => window.removeEventListener('keydown', onKey))

const duration = (entry) =>
    `${entry.time_taken.hours}${t('units.hours_short')} ${String(entry.time_taken.minutes).padStart(2, '0')}${t('units.minutes_short')}`
</script>

<template>
    <div class="fixed inset-0 z-[60] bg-[#083A4F]/50 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4" @click.self="emit('close')">

        <div role="dialog" aria-modal="true" :aria-label="t('share_day.title')"
             class="bg-white w-full max-w-2xl max-h-[92vh] sm:max-h-[88vh] flex flex-col rounded-t-3xl sm:rounded-3xl shadow-2xl overflow-hidden">

            <!-- Header -->
            <header class="flex items-center justify-between gap-3 px-5 sm:px-6 py-4 border-b border-[#D9D4CE]">
                <div class="min-w-0">
                    <h2 class="text-lg font-semibold tracking-tight">{{ t('share_day.title') }}</h2>
                    <p class="text-sm text-[#5F7480]">{{ formatDate(date, { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) }}</p>
                </div>
                <button type="button" @click="emit('close')" :aria-label="t('update.cancel')"
                        class="shrink-0 w-9 h-9 rounded-full flex items-center justify-center text-[#3E5660] hover:bg-[#EFECE8] transition-colors">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </header>

            <!-- Body -->
            <div class="flex-1 overflow-y-auto px-5 sm:px-6 py-5 space-y-6">

                <!-- Entries -->
                <section class="space-y-3">
                    <div class="flex items-center justify-between gap-2">
                        <h3 class="font-semibold text-sm">{{ t('share_day.entries_title') }}</h3>
                        <span v-if="timeEntries?.length"
                              class="text-xs font-semibold px-2.5 py-1 rounded-full bg-[#C0D5D6] text-[#083A4F] tabular-nums">
                            {{ selectedEntries.length }} {{ t('share_day.selected') }}
                        </span>
                    </div>

                    <div v-if="entriesLoading" class="space-y-2">
                        <div v-for="i in 3" :key="i" class="h-14 bg-[#EFECE8] animate-pulse rounded-2xl"></div>
                    </div>

                    <div v-else-if="entriesError" class="p-4 bg-[#FEE2E2] text-[#991B1B] rounded-2xl border border-[#FECACA] text-sm font-medium">
                        {{ t('share_day.error_entries') }}
                    </div>

                    <div v-else-if="!timeEntries?.length" class="py-8 text-center rounded-2xl border border-dashed border-[#D9D4CE] text-sm text-[#5F7480]">
                        {{ t('share_day.no_entries') }}
                    </div>

                    <div v-else class="rounded-2xl border border-[#D9D4CE] divide-y divide-[#D9D4CE] overflow-hidden">
                        <label v-for="entry in timeEntries" :key="entry.id"
                               class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-colors"
                               :class="selectedEntries.includes(entry.id) ? 'bg-[#C0D5D6]/30' : 'hover:bg-[#FAF9F7]'">
                            <span class="relative flex items-center justify-center h-5 w-5 shrink-0">
                                <input type="checkbox" :value="entry.id" v-model="selectedEntries"
                                       class="peer h-5 w-5 rounded-md border border-[#C0D5D6] cursor-pointer appearance-none bg-white checked:bg-[#083A4F] checked:border-[#083A4F] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#083A4F]/30" />
                                <svg class="absolute w-3.5 h-3.5 text-white pointer-events-none hidden peer-checked:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-medium truncate">{{ entry.label }}</span>
                                <span class="block text-xs text-[#3E5660] tabular-nums" dir="ltr">{{ displayTime(entry.start_time) }} – {{ displayTime(entry.end_time) }}</span>
                            </span>
                            <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full bg-[#EFECE8] text-[#083A4F] tabular-nums">{{ duration(entry) }}</span>
                        </label>
                    </div>
                </section>

                <!-- Channels -->
                <section class="space-y-3">
                    <h3 class="font-semibold text-sm">{{ t('share_day.channels_title') }}</h3>

                    <div v-if="channelsLoading" class="space-y-2">
                        <div v-for="i in 2" :key="i" class="h-14 bg-[#EFECE8] animate-pulse rounded-2xl"></div>
                    </div>

                    <div v-else-if="channelsError" class="p-4 bg-[#FEE2E2] text-[#991B1B] rounded-2xl border border-[#FECACA] text-sm font-medium">
                        {{ t('share_day.error_channels') }}
                    </div>

                    <div v-else-if="!channels?.length" class="py-8 text-center rounded-2xl border border-dashed border-[#D9D4CE] text-sm text-[#5F7480]">
                        {{ t('share_day.no_channels') }}
                    </div>

                    <div v-else class="rounded-2xl border border-[#D9D4CE] divide-y divide-[#D9D4CE] overflow-hidden">
                        <label v-for="channel in channels" :key="channel.id"
                               class="flex items-center gap-3 px-4 py-3 cursor-pointer transition-colors"
                               :class="selectedChannels.includes(channel.id) ? 'bg-[#C0D5D6]/30' : 'hover:bg-[#FAF9F7]'">
                            <span class="relative flex items-center justify-center h-5 w-5 shrink-0">
                                <input type="checkbox" :value="channel.id" v-model="selectedChannels"
                                       class="peer h-5 w-5 rounded-md border border-[#C0D5D6] cursor-pointer appearance-none bg-white checked:bg-[#083A4F] checked:border-[#083A4F] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#083A4F]/30" />
                                <svg class="absolute w-3.5 h-3.5 text-white pointer-events-none hidden peer-checked:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </span>
                            <span class="flex-1 min-w-0">
                                <span class="block font-medium truncate">{{ channel.name }}</span>
                                <span v-if="channel.description" class="block text-xs text-[#5F7480] truncate">{{ channel.description }}</span>
                            </span>
                            <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full bg-[#EFECE8] text-[#3E5660] whitespace-nowrap">
                                {{ channel.members_count }} {{ t('share_day.members') }}
                            </span>
                        </label>
                    </div>
                </section>
            </div>

            <!-- Footer -->
            <footer class="px-5 sm:px-6 py-4 border-t border-[#D9D4CE] bg-[#FAF9F7] flex flex-col-reverse sm:flex-row sm:items-center gap-3">
                <div class="flex-1 min-w-0 text-sm">
                    <p v-if="error" class="text-[#991B1B] font-medium">{{ error }}</p>
                    <p v-else-if="selectedEntries.length && selectedChannels.length" class="text-[#3E5660] tabular-nums">
                        {{ t('share_day.entries_count', selectedEntries.length) }} · {{ t('share_day.channels_count', selectedChannels.length) }}
                    </p>
                    <p v-else class="text-[#5F7480]">{{ t('share_day.ready') }}</p>
                </div>

                <div class="flex items-center gap-2 sm:justify-end">
                    <button type="button" @click="emit('close')"
                            class="flex-1 sm:flex-none px-5 py-2.5 rounded-full border border-[#C0D5D6] bg-white text-sm font-medium hover:bg-[#EFECE8] transition-colors">
                        {{ t('update.cancel') }}
                    </button>
                    <button type="button" @click="handleSubmit"
                            :disabled="loading || !selectedEntries.length || !selectedChannels.length"
                            class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors disabled:bg-[#C0D5D6] disabled:text-[#3E5660] disabled:cursor-not-allowed">
                        <span v-if="loading" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        {{ loading ? t('share_day.sharing') : t('share_day.submit') }}
                    </button>
                </div>
            </footer>
        </div>
    </div>
</template>
