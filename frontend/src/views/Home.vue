<script setup>
import { useFetch} from '@/composables/useTimeEntries'
import { ref, computed } from 'vue'
import { useI18n } from 'vue-i18n'
import History from './History.vue'
import ListView from '@/components/ListView.vue'

const { t } = useI18n()

const url = ref('/api/time-entries')
const title = ref(`${t('home.today_entries')}`)
const showHistory = ref(false)

const historyButtonText = computed(() =>
  showHistory.value ? `← ${t('home.hide')}` : `${t('home.history')} →`
)
const { data: entries, error } = useFetch(url)
const { data: dates, error: datesError } = useFetch('/api/time-entries?history=true')

const toggleHistory = () => {
  showHistory.value = !showHistory.value
}

const goToDate = (date) => {
  url.value = `/api/time-entries?date=${date}`
  title.value = `${t('home.entries_of')} ${date}`
}

const todayEntries = () => {
  url.value = '/api/time-entries'
  title.value = t('home.today_entries')
}
</script>

<template>
  <div class="flex min-h-screen relative">
    <!-- Main -->
    <div class="flex-1 p-8" :class="{ 'mr-[280px]': showHistory }">
      <div class="flex gap-2 mb-6 flex-wrap">
        <button 
          @click="todayEntries"
          class="px-5 py-2.5 border border-[#ddd] bg-white rounded cursor-pointer text-[0.9rem] transition-all duration-200 hover:bg-[#f5f5f5] hover:border-[#999]"
        >{{ $t('home.today_entries') }}</button>
        <button 
          @click="toggleHistory"
          class="ml-auto px-5 py-2.5 border border-[#ddd] bg-white rounded cursor-pointer text-[0.9rem] transition-all duration-200 hover:bg-[#f5f5f5] hover:border-[#999]"
        >{{ historyButtonText }}</button>
      </div>

      <div v-if="error" class="p-4 bg-[#ffeeee] text-[#cc3333] rounded border border-[#ffcccc]">
        {{ $t('home.error') }}: {{ error }}
      </div>
      <div v-else-if="entries">
        <h1 class="mt-0 mb-6 text-[#333] text-[1.8rem] font-semibold">{{ title }}</h1>
        <div class="flex gap-8 items-start">
          <ListView :entries="entries" />
        </div>
      </div>
      <div v-else class="text-center py-8 text-[#666]">{{ $t('home.loading') }}</div>
    </div>

    <!-- Sidebar -->
    <div 
      class="fixed right-0 top-[60px] w-[280px] h-[calc(100vh-60px)] bg-white border-l border-[#ddd] p-6 overflow-y-auto shadow-[-2px_0_8px_rgba(0,0,0,0.1)] transition-transform duration-300"
      :class="showHistory ? 'translate-x-0' : 'translate-x-full'"
    >
      <h3 class="mt-0 mb-4 text-[#333] text-[1.2rem]">{{ $t('home.history') }}</h3>
      <div v-if="datesError" class="p-4 bg-[#ffeeee] text-[#cc3333] rounded border border-[#ffcccc]">
        {{ $t('home.error_loading_dates') }}: {{ datesError }}
      </div>
      <div v-else-if="!dates || dates.length === 0" class="p-4 text-[#999] text-center">
        {{ $t('home.no_dates') }}
      </div>
      <History v-else :dates="dates" @click-date="goToDate" />
    </div>
  </div>
</template>