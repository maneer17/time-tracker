<script setup>
import { useRoute, useRouter } from 'vue-router'
import { ref, watch, inject, provide, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useI18n } from 'vue-i18n'
import channelService from '@/services/channelService'
import useApi from '@/composables/useApi'
import ChannelOverview from '@/components/channel/ChannelOverview.vue'
import ChannelMembers from '@/components/channel/members/ChannelMembers.vue'
import ChannelSettings from '@/components/channel/ChannelSettings.vue'
import ChannelSharedDays from '@/components/channel/shared-days/ChannelSharedDays.vue'
const auth = useAuthStore();
const route = useRoute()
const { t } = useI18n()
const router = useRouter()
const activeTab = ref('overview')

const { data: channel, error, request } = useApi(
  () => channelService.getChannel(route.params.id), true
)
watch(error, (val) => {
    if (val?.status === 403) {
        router.push({ name: 'Channels' })
    }
})
watch(() => route.params.id, () => {
  request()
})
const isOwner = computed(() => channel.value?.user_id === auth.user?.id)
provide('isOwner', isOwner)
provide('channel', channel)

const visibleTabs = computed(() => {
    const tabs = ['overview', 'members', 'sharedDays']
    if (isOwner.value) tabs.push('settings')
    return tabs
})
</script>

<template>
  <div class="max-w-4xl mx-auto px-6 py-12">

    <div class="flex flex-wrap justify-center p-2 bg-[#F9F7F2] rounded-[2.2rem] mb-12 w-full md:w-fit mx-auto border-2 border-[#F2E9E4] shadow-sm">
      <button
        v-for="tab in visibleTabs"
        :key="tab"
        @click="activeTab = tab"
        :class="activeTab === tab
          ? 'bg-[#5A7D5A] text-white shadow-lg shadow-[#5A7D5A]/15'
          : 'text-[#8E9AAF] hover:text-[#4A4A4A] hover:bg-[#F1F5F1]'"
        class="px-8 py-3.5 rounded-[1.8rem] text-[15px] font-black tracking-tight transition-all duration-300 active:scale-95 whitespace-nowrap">
        {{ t(`channel.${tab === 'sharedDays' ? 'shared_days' : tab}`) }}
      </button>
    </div>

    <div v-if="error"
      class="p-6 bg-[#FFF2F0] text-[#E07A5F] rounded-[2.5rem] border-2 border-[#FFE5E0] flex items-center gap-4 shadow-sm">
      <div class="h-12 w-12 bg-white rounded-2xl flex items-center justify-center text-2xl shadow-sm shrink-0">⚠️</div>
      <div class="flex flex-col">
        <span class="text-[12px] uppercase font-black tracking-widest opacity-60">System Alert</span>
        <span class="font-bold text-[16px] leading-tight">{{ t('channel.error') }}: {{ error }}</span>
      </div>
    </div>

    <template v-else-if="channel">
      <div class="transition-all duration-500 ease-out">
        <ChannelOverview
          v-if="activeTab === 'overview'"
          class="border-2 border-[#F9F7F2] bg-white rounded-[3rem] p-2 hover:border-[#5A7D5A]/10 transition-all shadow-sm" />

        <ChannelMembers
          v-else-if="activeTab === 'members'" 
          class="animate-in fade-in slide-in-from-bottom-2 duration-400" />

        <div v-else-if="activeTab === 'sharedDays'"
             class="animate-in fade-in slide-in-from-bottom-2 duration-400">
          <ChannelSharedDays />
        </div>

        <ChannelSettings
          v-else-if="activeTab === 'settings'"
          class="animate-in fade-in slide-in-from-bottom-2 duration-400" />
      </div>
    </template>

    <div v-else class="flex flex-col items-center justify-center py-32 bg-[#FDFCFB] rounded-[3rem] border-2 border-[#F2E9E4]">
      <div class="relative flex items-center justify-center mb-8">
        <div class="absolute animate-ping h-16 w-16 rounded-full bg-[#D4A373]/10"></div>
        <div class="h-12 w-12 border-[5px] border-[#F9F7F2] border-t-[#D4A373] rounded-full animate-spin"></div>
      </div>
      <p class="text-[14px] font-black text-[#A0A0A0] tracking-widest animate-pulse uppercase">
        {{ t('channel.loading') }}
      </p>
    </div>

  </div>
</template>