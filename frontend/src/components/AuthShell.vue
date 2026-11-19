<script setup>
import { useRoute } from 'vue-router'
import journalImg from '@/assets/landing/journal.webp'

defineProps({ title: String, text: String })

const route = useRoute()
const tabBase = 'px-3 py-2 rounded-full text-sm font-medium text-center transition-colors'
</script>

<template>
  <div class="min-h-screen grid grid-cols-1 lg:grid-cols-2 paper-bg text-[#083A4F]">

    <!-- Illustration panel (desktop) -->
    <aside class="hidden lg:flex flex-col justify-between gap-8 p-10">
      <router-link :to="{ name: 'Landing' }" class="flex items-center gap-2 w-fit">
        <span class="w-10 h-10 rounded-xl bg-[#083A4F] text-white flex items-center justify-center">
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6v6l4 2M12 21a9 9 0 100-18 9 9 0 000 18z" /></svg>
        </span>
        <span class="font-semibold">Time Tracker</span>
      </router-link>

      <div class="max-w-xl w-full ms-auto flex flex-col gap-6">
        <img :src="journalImg" alt="" width="1280" height="853"
             class="blend-vignette w-full aspect-[3/2] object-cover" />
        <div class="flex flex-col gap-2">
          <h1 class="text-2xl font-semibold tracking-tight text-balance">{{ title }}</h1>
          <p class="text-sm text-[#3E5660] text-pretty">{{ text }}</p>
        </div>
      </div>

      <span></span>
    </aside>

    <!-- Form column -->
    <main class="flex items-center justify-center lg:justify-start p-4 sm:p-10">
      <div class="w-full max-w-md p-2 sm:p-4 flex flex-col gap-6">

        <router-link :to="{ name: 'Landing' }" class="lg:hidden flex items-center gap-2 w-fit">
          <span class="w-8 h-8 rounded-lg bg-[#083A4F] text-white flex items-center justify-center">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6v6l4 2M12 21a9 9 0 100-18 9 9 0 000 18z" /></svg>
          </span>
          <span class="font-semibold text-sm">Time Tracker</span>
        </router-link>

        <!-- Sign in / create account switch -->
        <nav class="grid grid-cols-2 gap-1 p-1 rounded-full bg-[#083A4F]/[0.07]">
          <router-link :to="{ name: 'Login' }" :class="[tabBase, route.name === 'Login' ? 'bg-white text-[#083A4F] shadow-sm' : 'text-[#3E5660] hover:text-[#083A4F]']">
            {{ $t('login.login') }}
          </router-link>
          <router-link :to="{ name: 'Register' }" :class="[tabBase, route.name === 'Register' ? 'bg-white text-[#083A4F] shadow-sm' : 'text-[#3E5660] hover:text-[#083A4F]']">
            {{ $t('register.create_account') }}
          </router-link>
        </nav>

        <slot />
      </div>
    </main>
  </div>
</template>
