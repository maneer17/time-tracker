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
  <div class="min-h-screen bg-[#F9F7F2] font-sans antialiased text-[#333]">
    <div 
      class="fixed bottom-8 z-[2000] transition-all duration-500"
      :class="locale === 'ar' ? 'left-8' : 'right-8'"
    >
      <div class="group relative flex items-center bg-white p-2 rounded-[2rem] shadow-xl border border-white hover:pr-6 transition-all duration-300">
        <div class="h-10 w-10 bg-[#5A7D5A] rounded-full flex items-center justify-center text-white font-black text-xs uppercase">
          {{ locale }}
        </div>
        <select 
          v-model="$i18n.locale"
          class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
        >
          <option v-for="l in $i18n.availableLocales" :key="`locale-${l}`" :value="l">
            {{ l.toUpperCase() }}
          </option>
        </select>
        <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-500 pl-3 text-xs font-black text-[#5A7D5A] uppercase tracking-widest whitespace-nowrap">
          Change Language
        </span>
      </div>
    </div>

    <NavBar v-if="authStore.isAuthenticated" />

    <main :class="{ 'pt-32 px-4 pb-12': authStore.isAuthenticated }">
      <div class="max-w-7xl mx-auto">
        <router-view v-slot="{ Component }">
          <transition 
            name="fade" 
            mode="out-in"
          >
            <component :is="Component" />
          </transition>
        </router-view>
      </div>
    </main>
  </div>
</template>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease, transform 0.3s ease;
}

.fade-enter-from {
  opacity: 0;
  transform: translateY(10px);
}

.fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}


::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #F9F7F2;
}
::-webkit-scrollbar-thumb {
  background: #D4E2D4;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #5A7D5A;
}
</style>