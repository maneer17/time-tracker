<template>
  <nav class="nav">
    <ol class="list">
      <li class="item">
        <router-link :to="{ name: 'Home' }">Home</router-link>
      </li>
      <li class="item">
        <router-link :to="{ name: 'About' }">About</router-link>
      </li>
      <li class="item">
        <button @click="handleLogout">Sign Out</button>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const handleLogout = () => {
  authStore.logout();
};
</script>

<style scoped>
.nav {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  background: #333;
  padding: 1rem 2rem;
  z-index: 1000;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.list {
  display: flex;
  gap: 0.5rem;
  list-style: none;
  margin: 0;
  padding: 0;
}

.item a,
.item button {
  display: block;
  color: white;
  text-decoration: none;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  background: none;
  border: none;
  cursor: pointer;
  font-size: 0.95rem;
  transition: background 0.2s;
}

.item a:hover,
.item button:hover {
  background: #555;
}

.item a.router-link-active {
  background: #007bff;
}
</style>