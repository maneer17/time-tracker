<script setup>
import useFetch from '@/composables/fetch'
import fetchDates from '@/composables/history'
import { ref, computed } from 'vue'
import ListView from '@/components/ListView.vue'
import History from './History.vue'
import AddForm from './AddForm.vue'

const url = ref('/api/time-entries')
const title = ref('Time Entries for Today')
const showHistory = ref(false)
const showForm = ref(false)

const historyButtonText = computed(() => showHistory.value ? '← Hide' : 'History →')

const { data: entries, error } = useFetch(url)

const { dates, err: datesError } = fetchDates('/api/time-entries/history')

const toggleHistory = () => {
  showHistory.value = !showHistory.value
}

const goToDate = (date) => {
  url.value = `/api/time-entries/by-date/${date}`
  title.value = `Time Entries of ${date}`
}

const todayEntries = ()=>{
    url.value = '/api/time-entries'
    title.value = 'Time Entries of Today'
}

const renderForm=()=>{
    showForm.value = !showForm.value
}
</script>

<template>
  <div class="container" :class="{ 'sidebar-open': showHistory }">
    <div class="main">
      <div class="actions">
        <button @click="renderForm" v-if="url=='/api/time-entries'" class="btn primary">
          Add New Entry
        </button>
        <button @click="todayEntries" class="btn">Today's Entries</button>
        <button @click="toggleHistory" class="btn toggle">
          {{ historyButtonText }}
        </button>
      </div>

      <div v-if="showForm" class="form-wrap">
        <AddForm @data-submitted="renderForm"/>
      </div>

      <div v-if="error" class="error">Oops! Error encountered: {{ error }}</div>
      <div v-else-if="entries">
        <h1>{{ title }}</h1>
        <ListView :entries="entries" />
      </div>
      <div v-else class="loading">Loading entries...</div>
    </div>

    <div class="sidebar" :class="{ open: showHistory }">
      <h3>History</h3>
      <div v-if="datesError" class="error">Error loading dates: {{ datesError }}</div>
      <div v-else-if="!dates || dates.length === 0" class="empty">No dates available</div>
      <History v-else :dates="dates" @click-date="goToDate" />
    </div>
  </div>
</template>

<style scoped>
.container {
  display: flex;
  min-height: 100vh;
  position: relative;
}

.main {
  flex: 1;
  padding: 2rem;
  transition: margin-right 0.3s ease;
}

.container.sidebar-open .main {
  margin-right: 280px;
}

.actions {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
}

.btn {
  padding: 0.6rem 1.2rem;
  border: 1px solid #ddd;
  background: white;
  border-radius: 4px;
  cursor: pointer;
  font-size: 0.9rem;
  transition: all 0.2s;
}

.btn:hover {
  background: #f5f5f5;
  border-color: #999;
}

.btn.primary {
  background: #007bff;
  color: white;
  border-color: #007bff;
}

.btn.primary:hover {
  background: #0056b3;
  border-color: #0056b3;
}

.btn.toggle {
  margin-left: auto;
}

.form-wrap {
  background: #f9f9f9;
  padding: 1.5rem;
  border-radius: 6px;
  margin-bottom: 1.5rem;
  border: 1px solid #e0e0e0;
}

.error {
  padding: 1rem;
  background: #fee;
  color: #c33;
  border-radius: 4px;
  border: 1px solid #fcc;
}

.loading {
  text-align: center;
  padding: 2rem;
  color: #666;
}

.empty {
  padding: 1rem;
  color: #999;
  text-align: center;
}

h1 {
  margin: 0 0 1.5rem 0;
  color: #333;
  font-size: 1.8rem;
  font-weight: 600;
}

.sidebar {
  position: fixed;
  right: 0;
  top: 60px;
  width: 280px;
  height: calc(100vh - 60px);
  background: white;
  border-left: 1px solid #ddd;
  padding: 1.5rem;
  transform: translateX(100%);
  transition: transform 0.3s ease;
  overflow-y: auto;
  box-shadow: -2px 0 8px rgba(0,0,0,0.1);
}

.sidebar.open {
  transform: translateX(0);
}

.sidebar h3 {
  margin: 0 0 1rem 0;
  color: #333;
  font-size: 1.2rem;
}
</style>
