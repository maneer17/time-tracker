<script setup>
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import timeEntryService from '@/services/timeEntryService'
import HistoryDateItem from './HistoryDateItem.vue'

const { t } = useI18n()
defineProps(['show'])
const emit = defineEmits(['selectDate', 'shareDate'])

const dates = ref([])
const currentPage = ref(1)
const lastPage = ref(1)
const loading = ref(false)
const error = ref(null)

const hasMore = computed(() => currentPage.value < lastPage.value)

const loadDates = async () => {
    if (loading.value) return
    
    loading.value = true
    error.value = null
    try {
        const response = await timeEntryService.getTimeEntries({
            history: true,
            page: currentPage.value
        })
        
        dates.value = [...dates.value, ...response.data.data]
        currentPage.value = response.data.current_page
        lastPage.value = response.data.last_page
    } catch (err) {
        error.value = err.response?.data?.errors ?? err.message
    } finally {
        loading.value = false
    }
}

const loadMore = () => {
    if (!hasMore.value || loading.value) return
    // Increment to the next page
    currentPage.value = currentPage.value + 1
    loadDates()
}

// Initial load
loadDates()
</script>

<template>
    <div
        class="fixed right-0 top-[60px] w-[280px] h-[calc(100vh-60px)] bg-white border-l border-[#ddd] p-6 overflow-y-auto shadow-[-2px_0_8px_rgba(0,0,0,0.1)] transition-transform duration-300"
        :class="show ? 'translate-x-0' : 'translate-x-full'">

        <h3 class="mt-0 mb-4 text-[#333] text-[1.2rem]">{{ t('home.history') }}</h3>

        <!-- Error -->
        <div v-if="error"
            class="p-4 bg-[#ffeeee] text-[#cc3333] rounded border border-[#ffcccc]">
            {{ t('home.error_loading_dates') }}: {{ error }}
        </div>

        <template v-else>

            <!-- Loading skeleton -->
            <div v-if="loading && !dates.length" class="space-y-2">
                <div v-for="i in 5" :key="i" class="h-10 bg-gray-100 rounded animate-pulse"></div>
            </div>

            <!-- Empty -->
            <div v-else-if="!dates.length"
                class="p-4 text-[#999] text-center">
                {{ t('home.no_dates') }}
            </div>

            <!-- List -->
            <div v-else class="flex flex-col gap-2">
                <HistoryDateItem
                    v-for="date in dates"
                    :key="date"
                    :date="date"
                    @click="emit('selectDate', date)"
                    @share="emit('shareDate', date)" />

                <!-- Load More -->
                <button
                    v-if="hasMore"
                    @click="loadMore"
                    :disabled="loading"
                    class="w-full py-2 text-xs font-semibold text-[#007bff] hover:text-[#0056b3] disabled:opacity-50 transition-colors mt-2">
                    {{ loading ? '...' : t('home.load_more') }}
                </button>
            </div>

        </template>

    </div>
</template>