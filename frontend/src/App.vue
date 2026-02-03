<script setup>
import NavBar from './components/NavBar.vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/auth';
import { onMounted, watch, computed } from 'vue';

const { t, locale } = useI18n({ useScope: 'global' })
const authStore = useAuthStore();

const dir = computed(() => locale.value === 'ar' ? 'rtl' : 'ltr')

onMounted(() => {
  authStore.checkAuth();
  document.documentElement.setAttribute('dir', dir.value);
});

watch(locale, (newLocale) => {
  document.documentElement.setAttribute('dir', newLocale === 'ar' ? 'rtl' : 'ltr');
});
</script>

<template>
  <div class="locale-changer">
    <select v-model="$i18n.locale">
      <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">
        {{ locale }}
      </option>
    </select>
  </div>
  
  <NavBar v-if="authStore.isAuthenticated" />
  
  <div :class="{ content: authStore.isAuthenticated }">
    <router-view />
  </div>
</template>

<style scoped>
.locale-changer {
  position: fixed;
  top: 10px;
  right: 10px;
  z-index: 2000;
}

.locale-changer select {
  padding: 0.5rem 1rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  background: white;
  cursor: pointer;
  font-size: 0.9rem;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.locale-changer select:hover {
  border-color: #007bff;
}

.locale-changer select:focus {
  outline: none;
  border-color: #007bff;
}

.content {
  margin-top: 60px;
}

h1 {
  padding: 2rem 2rem 0;
  margin: 0;
  color: #333;
  font-size: 2rem;
  font-weight: 600;
}
</style>