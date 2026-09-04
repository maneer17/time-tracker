<script setup>
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import NotificationBell from './notifications/NotificationBell.vue'

const { t }     = useI18n()
const authStore = useAuthStore()

const handleLogout = async () => {
    await authStore.logout()
}
</script>

<template>
    <nav class="fixed top-4 left-4 right-4 h-20 bg-white/80 backdrop-blur-xl rounded-[2rem] px-8 z-[1000] shadow-[0_10px_40px_rgba(0,0,0,0.03)] border border-white flex items-center justify-between">
        
        <div class="flex items-center gap-2">
            <router-link
                :to="{ name: 'Home' }"
                class="px-5 py-2.5 rounded-2xl text-[0.9rem] font-bold tracking-tight transition-all duration-300 hover:bg-[#F0F4F8] text-[#8E9AAF] [&.router-link-active]:bg-[#5A7D5A] [&.router-link-active]:text-white [&.router-link-active]:shadow-lg [&.router-link-active]:shadow-[#5A7D5A]/20">
                {{ t('nav.home') }}
            </router-link>
            
            <router-link
                :to="{ name: 'Channels' }"
                class="px-5 py-2.5 rounded-2xl text-[0.9rem] font-bold tracking-tight transition-all duration-300 hover:bg-[#F0F4F8] text-[#8E9AAF] [&.router-link-active]:bg-[#5A7D5A] [&.router-link-active]:text-white">
                {{ t('nav.channels') }}
            </router-link>

            <router-link
                :to="{ name: 'MySharedDays' }"
                class="px-5 py-2.5 rounded-2xl text-[0.9rem] font-bold tracking-tight transition-all duration-300 hover:bg-[#F0F4F8] text-[#8E9AAF] [&.router-link-active]:bg-[#5A7D5A] [&.router-link-active]:text-white">
                {{ t('nav.shared_days') }}
            </router-link>

            <router-link
                :to="{ name: 'Reports' }"
                class="px-5 py-2.5 rounded-2xl text-[0.9rem] font-bold tracking-tight transition-all duration-300 hover:bg-[#F0F4F8] text-[#8E9AAF] [&.router-link-active]:bg-[#5A7D5A] [&.router-link-active]:text-white">
                {{ t('nav.reports') }}
            </router-link>

            <router-link
                :to="{ name: 'Settings' }"
                class="px-5 py-2.5 rounded-2xl text-[0.9rem] font-bold tracking-tight transition-all duration-300 hover:bg-[#F0F4F8] text-[#8E9AAF] [&.router-link-active]:bg-[#5A7D5A] [&.router-link-active]:text-white">
                {{ t('nav.settings') }}
            </router-link>

            <!-- invitations as a normal nav link -->
            <router-link
                :to="{ name: 'MyInvitations' }"
                class="px-5 py-2.5 rounded-2xl text-[0.9rem] font-bold tracking-tight transition-all duration-300 hover:bg-[#F0F4F8] text-[#8E9AAF] [&.router-link-active]:bg-[#5A7D5A] [&.router-link-active]:text-white">
                {{ t('nav.invitations') }}
            </router-link>
        </div>

        <div class="flex items-center gap-3">

            <!-- notification bell -->
            <NotificationBell />

            <div class="w-[1px] h-6 bg-gray-100 mx-2"></div>

            <button
                @click="handleLogout"
                class="px-6 py-2.5 rounded-2xl bg-[#FFF2F2] text-[#AF4E4E] font-bold text-[0.85rem] transition-all duration-300 hover:bg-[#FFDADA] active:scale-95">
                {{ t('nav.sign_out') }}
            </button>
        </div>
    </nav>
</template>