<script setup>
import { ref } from 'vue'
import { useI18n } from 'vue-i18n'
import timeEntryService from '@/services/timeEntryService'
import channelService from '@/services/channelService'
import sharedDayService from '@/services/sharedDayService'
import useApi from '@/composables/useApi'

const { t } = useI18n()
const props = defineProps(['date'])
const emit = defineEmits(['close', 'shared'])

const selectedEntries = ref([])
const selectedChannels = ref([])

const { data: timeEntries, error: entriesError, loading: entriesLoading } = useApi(
    () => timeEntryService.getTimeEntries({ date: props.date }), true
)

const { data: channels, error: channelsError, loading: channelsLoading } = useApi(
    () => channelService.getChannels(), true
)

const { data, error, loading, request } = useApi(
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
</script>

<template>
    <div class="fixed inset-0 bg-[#F9F7F2] z-[60] flex flex-col font-sans">

        <header class="flex items-center justify-between px-8 py-6 bg-white/70 backdrop-blur-xl sticky top-0 z-10 border-b border-white/50 shadow-sm">
            <div class="flex items-center gap-6">
                <button
                    @click="emit('close')"
                    class="p-3 hover:bg-[#F0F4F8] rounded-2xl transition-all text-[#8E9AAF] active:scale-95">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h2 class="text-2xl font-black text-[#333] tracking-tight">{{ t('share_day.title') }}</h2>
            </div>
            <div class="px-5 py-2 bg-[#FEF6E4] rounded-xl">
                <span class="text-xs font-bold text-[#D4A373] uppercase tracking-widest">
                    {{ date }}
                </span>
            </div>
        </header>

        <form @submit.prevent="handleSubmit" class="flex-1 overflow-y-auto">
            <div class="max-w-4xl mx-auto p-8 md:p-12 space-y-16">

                <section>
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-[0.7rem] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
                            {{ t('share_day.entries_title') }}
                        </h3>
                        <div v-if="timeEntries?.length"
                            class="text-xs font-bold px-4 py-1.5 bg-[#E8F0E8] text-[#5A7D5A] rounded-full shadow-sm">
                            {{ selectedEntries.length }} {{ t('share_day.selected') }}
                        </div>
                    </div>

                    <div v-if="entriesLoading" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div v-for="i in 4" :key="i" class="h-28 bg-white/50 animate-pulse rounded-[2rem]"></div>
                    </div>

                    <div v-else-if="entriesError"
                        class="p-6 bg-[#FFF2F2] text-[#AF4E4E] rounded-[2rem] border border-[#FFDADA] text-sm font-medium">
                        {{ t('share_day.error_entries') }}
                    </div>

                    <div v-else-if="!timeEntries?.length"
                        class="text-center py-24 bg-white/40 rounded-[3rem] border-4 border-dashed border-white/60">
                        <p class="text-[#BCBCBC] font-bold">{{ t('share_day.no_entries') }}</p>
                    </div>

                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <label
                            v-for="entry in timeEntries"
                            :key="entry.id"
                            class="group relative flex items-start gap-5 p-6 rounded-[2rem] border-2 transition-all cursor-pointer"
                            :class="selectedEntries.includes(entry.id)
                                ? 'border-[#5A7D5A] bg-white shadow-xl shadow-[#5A7D5A]/5 scale-[1.02]'
                                : 'border-transparent bg-white/60 hover:bg-white hover:shadow-md'">
                            <div class="relative flex items-center justify-center h-6 w-6 mt-1">
                                <input
                                    type="checkbox"
                                    :value="entry.id"
                                    v-model="selectedEntries"
                                    class="peer h-6 w-6 rounded-xl border-2 border-[#D4E2D4] text-[#5A7D5A] focus:ring-0 transition-all cursor-pointer appearance-none bg-white checked:bg-[#5A7D5A] checked:border-[#5A7D5A]" />
                                <svg class="absolute w-4 h-4 text-white pointer-events-none hidden peer-checked:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-[#333] text-lg truncate tracking-tight">{{ entry.label }}</p>
                                <p class="text-xs font-bold text-[#A0A0A0] mt-1.5 uppercase tracking-wider">
                                    {{ entry.start_time }} — {{ entry.end_time }}
                                </p>
                            </div>
                        </label>
                    </div>
                </section>

                <section>
                    <div class="mb-8">
                        <h3 class="text-[0.7rem] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
                            {{ t('share_day.channels_title') }}
                        </h3>
                    </div>

                    <div v-if="channelsLoading" class="space-y-4">
                        <div v-for="i in 2" :key="i" class="h-24 bg-white/50 animate-pulse rounded-[2rem]"></div>
                    </div>

                    <div v-else-if="!channels?.owned?.length"
                        class="text-center py-20 bg-white/40 rounded-[3rem] border-4 border-dashed border-white/60">
                        <p class="text-[#BCBCBC] font-bold">{{ t('share_day.no_channels') }}</p>
                    </div>

                    <div v-else class="grid grid-cols-1 gap-4">
                        <label
                            v-for="channel in channels.owned"
                            :key="channel.id"
                            class="group flex items-center gap-6 p-6 rounded-[2.5rem] border-2 transition-all cursor-pointer"
                            :class="selectedChannels.includes(channel.id)
                                ? 'border-[#8E9AAF] bg-white shadow-xl shadow-[#8E9AAF]/5 scale-[1.01]'
                                : 'border-transparent bg-white/60 hover:bg-white hover:shadow-md'">
                            <div class="relative flex items-center justify-center h-6 w-6">
                                <input
                                    type="checkbox"
                                    :value="channel.id"
                                    v-model="selectedChannels"
                                    class="peer h-6 w-6 rounded-xl border-2 border-[#D4E2D4] text-[#8E9AAF] focus:ring-0 transition-all cursor-pointer appearance-none bg-white checked:bg-[#8E9AAF] checked:border-[#8E9AAF]" />
                                <svg class="absolute w-4 h-4 text-white pointer-events-none hidden peer-checked:block" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                            <div class="flex-1">
                                <p class="font-bold text-[#333] text-lg tracking-tight">{{ channel.name }}</p>
                                <p class="text-sm text-[#A0A0A0] font-medium truncate mt-0.5">{{ channel.description }}</p>
                            </div>
                            <div class="bg-[#F0F4F8] px-4 py-2 rounded-2xl">
                                <span class="text-[10px] font-black text-[#8E9AAF] uppercase tracking-widest whitespace-nowrap">
                                    {{ channel.members_count }} {{ t('share_day.members') }}
                                </span>
                            </div>
                        </label>
                    </div>
                </section>

                <div class="h-32"></div>
            </div>
        </form>

        <footer class="p-8 bg-white/80 backdrop-blur-2xl border-t border-white shadow-[0_-10px_40px_rgba(0,0,0,0.02)] sticky bottom-0">
            <div class="max-w-4xl mx-auto flex flex-col md:flex-row items-center gap-6">

                <div v-if="error?.message"
                    class="flex-1 text-sm text-[#AF4E4E] font-bold bg-[#FFF2F2] px-6 py-4 rounded-2xl border border-[#FFDADA]">
                    {{ error.message }}
                </div>

                <div v-else class="flex-1 hidden md:block">
                    <p class="text-[0.7rem] font-black text-[#BCBCBC] uppercase tracking-[0.2em] mb-1">
                        {{ t('share_day.ready') }}
                    </p>
                    <p class="text-[#333] font-bold" v-if="selectedEntries.length && selectedChannels.length">
                        <span class="text-[#5A7D5A]">{{ selectedEntries.length }} entries</span>
                        <span class="mx-2 text-[#D4E2D4]">/</span>
                        <span class="text-[#8E9AAF]">{{ selectedChannels.length }} channels</span>
                    </p>
                </div>

                <button
                    @click="handleSubmit"
                    :disabled="loading || !selectedEntries.length || !selectedChannels.length"
                    class="w-full md:w-auto px-12 py-5 bg-[#5A7D5A] hover:bg-[#4a6b4a] disabled:bg-[#E8F0E8] disabled:text-[#A0A0A0] text-white font-black rounded-[2rem] transition-all shadow-2xl shadow-[#5A7D5A]/20 flex items-center justify-center gap-4 active:scale-95">
                    <div v-if="loading" class="w-5 h-5 border-3 border-white/30 border-t-white rounded-full animate-spin"></div>
                    <span class="tracking-tight">{{ loading ? t('share_day.sharing') : t('share_day.submit') }}</span>
                </button>

            </div>
        </footer>

    </div>
</template>