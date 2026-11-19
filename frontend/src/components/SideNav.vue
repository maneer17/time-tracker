<script setup>
import { computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import NotificationBell from './notifications/NotificationBell.vue'
import LocaleSwitcher from './LocaleSwitcher.vue'

const { t } = useI18n()
const authStore = useAuthStore()

const handleLogout = async () => {
    await authStore.logout()
}

const items = [
    { name: 'Home', key: 'nav.home', icon: 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Channels', key: 'nav.channels', icon: 'M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z' },
    { name: 'MySharedDays', key: 'nav.shared_days', icon: 'M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z' },
    { name: 'Reports', key: 'nav.reports', icon: 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z' },
    { name: 'MyInvitations', key: 'nav.invitations', icon: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' },
    { name: 'Settings', key: 'nav.settings', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z' },
    { name: 'About', key: 'nav.about', icon: 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
]

const initials = computed(() =>
    (authStore.user?.name ?? '?').split(' ').filter(Boolean).slice(0, 2).map((w) => w[0]).join('').toUpperCase()
)

const railLink = 'flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium text-[#3E5660] transition-colors hover:bg-[#EFECE8] hover:text-[#083A4F] [&.router-link-active]:bg-[#C0D5D6] [&.router-link-active]:text-[#083A4F]'
const tabLink = 'flex-1 min-w-[76px] flex flex-col items-center gap-0.5 py-2 text-[11px] font-medium text-[#3E5660] [&.router-link-active]:text-[#083A4F]'
</script>

<template>
    <!-- Desktop rail -->
    <aside class="hidden lg:flex fixed inset-y-0 start-0 w-60 z-40 flex-col justify-between gap-6 bg-white border-e border-[#D9D4CE] p-4">
        <div class="flex flex-col gap-6">
            <div class="flex items-center justify-between px-2 pt-1">
                <router-link :to="{ name: 'Home' }" class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-[#083A4F] text-white flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6v6l4 2M12 21a9 9 0 100-18 9 9 0 000 18z" /></svg>
                    </span>
                    <span class="font-semibold text-[#083A4F] whitespace-nowrap">Time Tracker</span>
                </router-link>
                <NotificationBell align="start" />
            </div>

            <nav class="flex flex-col gap-1">
                <router-link v-for="item in items" :key="item.name" :to="{ name: item.name }" :class="railLink">
                    <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path :d="item.icon" /></svg>
                    {{ t(item.key) }}
                </router-link>
            </nav>
        </div>

        <div class="flex flex-col gap-4 pt-4 border-t border-[#D9D4CE]">
            <LocaleSwitcher flat class="self-start" />

            <div class="flex items-center gap-3 px-1">
                <span class="w-9 h-9 rounded-full bg-[#C0D5D6] text-[#083A4F] flex items-center justify-center text-xs font-bold shrink-0">{{ initials }}</span>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-semibold text-[#083A4F] truncate">{{ authStore.user?.name }}</p>
                    <p class="text-xs text-[#5F7480] truncate">{{ authStore.user?.email }}</p>
                </div>
            </div>

            <button
                @click="handleLogout"
                class="w-full px-4 py-2.5 rounded-xl bg-[#FEE2E2] text-[#991B1B] font-semibold text-sm transition-colors hover:bg-[#FECACA]">
                {{ t('nav.sign_out') }}
            </button>
        </div>
    </aside>

    <!-- Mobile top bar -->
    <header class="lg:hidden sticky top-0 z-30 bg-white border-b border-[#D9D4CE] px-4 py-3 flex items-center justify-between gap-2">
        <router-link :to="{ name: 'Home' }" class="flex items-center gap-2">
            <span class="w-8 h-8 rounded-lg bg-[#083A4F] text-white flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 6v6l4 2M12 21a9 9 0 100-18 9 9 0 000 18z" /></svg>
            </span>
            <span class="font-semibold text-[#083A4F] whitespace-nowrap hidden sm:inline">Time Tracker</span>
        </router-link>
        <div class="flex items-center gap-2">
            <LocaleSwitcher flat />
            <NotificationBell />
            <button @click="handleLogout" class="text-xs font-semibold text-[#991B1B] px-2 py-1.5 rounded-lg hover:bg-[#FEE2E2] whitespace-nowrap">
                {{ t('nav.sign_out') }}
            </button>
        </div>
    </header>

    <!-- Mobile bottom navigation -->
    <nav class="lg:hidden fixed bottom-0 inset-x-0 z-30 bg-white border-t border-[#D9D4CE] flex overflow-x-auto">
        <router-link v-for="item in items" :key="item.name" :to="{ name: item.name }" :class="tabLink">
            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path :d="item.icon" /></svg>
            <span class="whitespace-nowrap">{{ t(item.key) }}</span>
        </router-link>
    </nav>
</template>
