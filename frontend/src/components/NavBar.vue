<script setup>
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import invitationService from '@/services/invitationService'
import useApi from '@/composables/useApi'

const { t } = useI18n()
const authStore = useAuthStore()

const { data: invitations } = useApi(
    () => invitationService.getMyInvitations(), true
)

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
        </div>

        <div class="flex items-center gap-3">
            <router-link
                :to="{ name: 'MyInvitations' }"
                class="relative p-2.5 rounded-2xl bg-[#F0F4F8] text-[#8E9AAF] hover:bg-[#E2E8F0] transition-all duration-300 [&.router-link-active]:bg-[#FEF6E4] [&.router-link-active]:text-[#D4A373]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <span
                    v-if="invitations?.length > 0"
                    class="absolute -top-1 -right-1 h-5 w-5 bg-[#AF4E4E] text-white text-[10px] font-black rounded-full flex items-center justify-center ring-4 ring-white">
                    {{ invitations.length > 9 ? '9+' : invitations.length }}
                </span>
            </router-link>

            <div class="w-[1px] h-6 bg-gray-100 mx-2"></div>

            <button
                @click="handleLogout"
                class="px-6 py-2.5 rounded-2xl bg-[#FFF2F2] text-[#AF4E4E] font-bold text-[0.85rem] transition-all duration-300 hover:bg-[#FFDADA] active:scale-95">
                {{ t('nav.sign_out') }}
            </button>
        </div>
    </nav>
</template>