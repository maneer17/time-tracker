```vue
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
  <div 
    class="fixed top-2.5 z-[2000] transition-all duration-300"
    :class="locale === 'ar' ? 'left-2.5' : 'right-2.5'"
  >
    <select 
      v-model="$i18n.locale"
      class="py-2 px-4 border border-[#ddd] rounded bg-white cursor-pointer text-[0.9rem] shadow-sm hover:border-[#007bff] focus:outline-none focus:border-[#007bff]"
    >
      <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">
        {{ locale }}
      </option>
    </select>
  </div>

  <NavBar v-if="authStore.isAuthenticated" />

  <div :class="{ 'mt-[60px]': authStore.isAuthenticated }">
    <router-view />
  </div>
</template>
