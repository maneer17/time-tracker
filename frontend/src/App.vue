<template>
  <div class="app">
    <div class="sidebar">
      <h2>History</h2>
      <HistorySidebar />
      <router-link to="/add" class="add-btn">Add New Entry</router-link>
    </div>
    <div class="main">
      <router-view />
    </div>
  </div>
</template>

<script setup>
import HistorySidebar from './components/HistorySidebar.vue'
</script>

<style>
.app {
  display: flex;
  min-height: 100vh;
}

.sidebar {
  width: 300px;
  background: #f8f9fa;
  border-right: 1px solid #dee2e6;
  padding: 20px;
}

.main {
  flex: 1;
  padding: 20px;
}

.add-btn {
  display: block;
  width: 100%;
  padding: 10px;
  background: #007bff;
  color: white;
  text-align: center;
  text-decoration: none;
  border-radius: 5px;
  margin-top: 20px;
}

.add-btn:hover {
  background: #0056b3;
}
</style>