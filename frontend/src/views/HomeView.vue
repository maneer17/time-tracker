<script setup>
import { ref, computed, watchEffect, provide } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useToast } from 'vue-toastification'
import useApi from '@/composables/useApi'
import timeEntryService from '@/services/timeEntryService'
import HistorySidebar from '@/components/history/HistorySidebar.vue'
import ShareDayModal from '@/components/time-entries/ShareDayModal.vue'
import TimeEntryList from '@/components/time-entries/TimeEntryList.vue'

const { t } = useI18n()
const router = useRouter()
const toast = useToast()

const filters = ref({
    search: '',
    date: null,
    sort: ''
})

const { data: entries, error, request } = useApi(
    () => timeEntryService.getTimeEntries(filters.value), true
)

watchEffect(() => {
    request()
})

provide('refreshEntries', request)

const totalMinutes = computed(() => {
    if (!entries.value?.length) return 0
    return entries.value.reduce((acc, entry) =>
        acc + (entry.time_taken.hours * 60 + entry.time_taken.minutes), 0
    )
})

const title = ref(t('home.today_entries'))
const showHistory = ref(false)
const showShareModal = ref(false)
const selectedShareDate = ref(null)

const historyButtonText = computed(() =>
    showHistory.value ? `← ${t('home.hide')}` : `${t('home.history')} →`
)

const toggleHistory = () => {
    showHistory.value = !showHistory.value
}

const goToDate = (date) => {
    filters.value.date = date
    title.value = `${t('home.entries_of')} ${date}`
}

const todayEntries = () => {
    filters.value.date = null
    filters.value.search = ''
    filters.value.sort = ''
    title.value = t('home.today_entries')
}

const handleShareDate = (date) => {
    selectedShareDate.value = date
    showShareModal.value = true
}

const handleShared = () => {
    showShareModal.value = false
    toast.success('Day shared successfully!')
    router.push({ name: 'MySharedDays' })
}
</script>

<template>
    <div class="flex min-h-screen bg-[#F9F7F2] relative font-sans text-[#4A4A4A]">

        <div class="flex-1 p-10 transition-all duration-500" :class="{ 'mr-[320px]': showHistory }">

            <div class="flex gap-4 mb-10 flex-wrap items-center">
                <input
                    type="text"
                    v-model="filters.search"
                    :placeholder="t('home.search_placeholder')"
                    class="px-5 py-3 bg-white border-none rounded-2xl shadow-sm text-[0.95rem] focus:ring-2 focus:ring-[#D4E2D4] outline-none w-64 transition-all" />

                <select
                    v-model="filters.sort"
                    class="px-5 py-3 bg-white border-none rounded-2xl shadow-sm text-[0.95rem] cursor-pointer focus:ring-2 focus:ring-[#D4E2D4] outline-none transition-all">
                    <option value="">{{ t('home.sort_default') }}</option>
                    <option value="desc">{{ t('home.sort_oldest') }}</option>
                    <option value="asc">{{ t('home.sort_latest') }}</option>
                </select>

                <button
                    @click="todayEntries"
                    class="px-6 py-3 bg-[#E8F0E8] text-[#5A7D5A] font-semibold rounded-2xl hover:bg-[#D4E2D4] transition-all duration-300 shadow-sm">
                    {{ t('home.today_entries') }}
                </button>

                <button
                    @click="toggleHistory"
                    class="ml-auto px-6 py-3 bg-white text-[#8A8A8A] font-medium rounded-2xl shadow-sm hover:shadow-md transition-all duration-300">
                    {{ historyButtonText }}
                </button>
            </div>

            <div v-if="error" class="p-6 bg-[#FFF2F2] text-[#AF4E4E] rounded-[2rem] border border-[#FFDADA]">
                {{ t('home.error') }}: {{ error }}
            </div>

            <div v-else-if="entries" class="space-y-8">
                
                <h2 class="text-3xl font-bold text-[#333] tracking-tight">{{ title }}</h2>

                <div v-if="entries.length > 0" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="p-6 bg-white rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:translate-y-[-4px] transition-transform duration-300">
                        <p class="text-[0.7rem] font-bold text-[#A0A0A0] uppercase tracking-[0.1em] mb-2">
                            {{ t('home.total_time') }}
                        </p>
                        <p class="text-3xl font-bold text-[#333]">
                            {{ Math.floor(totalMinutes / 60) }}<span class="text-lg font-medium text-[#A0A0A0]">h</span> {{ totalMinutes % 60 }}<span class="text-lg font-medium text-[#A0A0A0]">m</span>
                        </p>
                    </div>

                    <div class="p-6 bg-[#F0F4F8] rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:translate-y-[-4px] transition-transform duration-300">
                        <p class="text-[0.7rem] font-bold text-[#8E9AAF] uppercase tracking-[0.1em] mb-2">
                            {{ t('home.total_entries') }}
                        </p>
                        <p class="text-3xl font-bold text-[#333]">{{ entries.length }}</p>
                    </div>

                    <div class="p-6 bg-[#FEF6E4] rounded-[2rem] shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 hover:translate-y-[-4px] transition-transform duration-300">
                        <p class="text-[0.7rem] font-bold text-[#D4A373] uppercase tracking-[0.1em] mb-2">
                            {{ t('home.avg_per_entry') }}
                        </p>
                        <p class="text-3xl font-bold text-[#333]">
                            {{ Math.floor(totalMinutes / entries.length / 60) }}<span class="text-lg font-medium text-[#A0A0A0]">h</span>
                            {{ Math.floor(totalMinutes / entries.length) % 60 }}<span class="text-lg font-medium text-[#A0A0A0]">m</span>
                        </p>
                    </div>
                </div>

                <div class="bg-white/40 p-2 rounded-[2.5rem]">
                    <TimeEntryList :entries="entries" />
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center py-20 text-[#BCBCBC]">
                <div class="w-12 h-12 border-4 border-[#E8F0E8] border-t-[#5A7D5A] rounded-full animate-spin mb-4"></div>
                <p class="font-medium">{{ t('home.loading') }}</p>
            </div>

        </div>

        <HistorySidebar
            :show="showHistory"
            @select-date="goToDate"
            @share-date="handleShareDate" />

        <ShareDayModal
            v-if="showShareModal"
            :date="selectedShareDate"
            @close="showShareModal = false"
            @shared="handleShared" />

    </div>
</template>