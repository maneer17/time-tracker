<script setup>
import NavBar from './components/NavBar.vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/auth';

const { t, locale } = useI18n({ useScope: 'global' })
const authStore = useAuthStore();
import { onMounted } from 'vue';
onMounted(() => {
  authStore.checkAuth();
});
</script>

<template>
  <NavBar v-if="authStore.isAuthenticated" />
  <div :class="{ content: authStore.isAuthenticated }">
    <h1 v-if="authStore.isAuthenticated">{{ $t("message.hello") }}</h1>
    <router-view />
  </div>
</template>

<style scoped>
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
