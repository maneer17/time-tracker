<script setup>
import SideNav from './components/SideNav.vue';
import LocaleSwitcher from './components/LocaleSwitcher.vue';
import { useI18n } from 'vue-i18n';
import { useAuthStore } from '@/stores/auth';
import { onMounted, watch } from 'vue';
import { applyDocumentLocale, persistLocale } from '@/plugins/i18n';
const { locale } = useI18n({ useScope: 'global' })
const authStore = useAuthStore();

onMounted(() => {
  authStore.checkAuth();
});

// lang/dir are applied at startup in the i18n plugin; here we follow user changes.
watch(locale, (newLocale) => {
  applyDocumentLocale(newLocale);
  persistLocale(newLocale);
});
</script>

<template>
  <div class="min-h-screen bg-[#FAF9F7] font-sans antialiased text-[#083A4F]">
    <!-- Signed-out pages get a floating language switch; signed-in pages have it in the side navigation. -->
    <LocaleSwitcher v-if="!authStore.isAuthenticated" class="fixed bottom-6 end-6 z-[2000]" />

    <SideNav v-if="authStore.isAuthenticated" />

    <main :class="{ 'lg:ps-60 pb-20 lg:pb-0': authStore.isAuthenticated }">
      <div :class="{ 'px-4 py-6 lg:px-8 lg:py-8': authStore.isAuthenticated && !$route.meta.fullWidth }">
        <div :class="{ 'max-w-7xl mx-auto': !$route.meta.fullWidth }">
          <router-view v-slot="{ Component }">
            <transition name="fade" mode="out-in">
              <component :is="Component" />
            </transition>
          </router-view>
        </div>
      </div>
    </main>
  </div>
</template>
