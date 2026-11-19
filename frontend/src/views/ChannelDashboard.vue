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
const activeTab = ref('sharedDays')

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
  <div class="p-4 lg:p-8 flex flex-col gap-6 min-w-0">

    <div v-if="error" class="p-5 bg-[#FEE2E2] text-[#991B1B] rounded-3xl border border-[#FECACA]">
      <p class="text-xs font-semibold opacity-70">{{ t('channel.alert_title') }}</p>
      <p class="font-medium">{{ t('channel.error') }}: {{ error }}</p>
    </div>

    <template v-else-if="channel">
      <!-- Channel header -->
      <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 flex flex-col gap-4">
        <div class="flex flex-wrap items-center gap-3">
          <h2 class="text-2xl font-semibold tracking-tight">{{ channel.name }}</h2>
          <span class="inline-flex px-2.5 py-0.5 rounded-full bg-[#C0D5D6] text-[#083A4F] text-xs font-semibold">
            {{ isOwner ? t('channels_page.role_owner') : t('channels_page.role_member') }}
          </span>
          <span class="text-sm text-[#3E5660]">{{ t('channels_page.members_count', channel.members_count ?? 0) }}</span>
        </div>

        <!-- Sections -->
        <div class="flex flex-wrap gap-1 p-1 rounded-full bg-[#E5E1DD] w-fit max-w-full">
          <button
            v-for="tab in visibleTabs"
            :key="tab"
            @click="activeTab = tab"
            :class="activeTab === tab ? 'bg-white text-[#083A4F] shadow-sm' : 'text-[#3E5660] hover:text-[#083A4F]'"
            class="px-4 py-2 rounded-full text-sm font-medium transition-colors whitespace-nowrap">
            {{ t(`channel.${tab === 'sharedDays' ? 'shared_days' : tab}`) }}
          </button>
        </div>
      </section>

      <ChannelOverview v-if="activeTab === 'overview'" />
      <ChannelMembers v-else-if="activeTab === 'members'" />
      <ChannelSharedDays v-else-if="activeTab === 'sharedDays'" />
      <ChannelSettings v-else-if="activeTab === 'settings'" />
    </template>

    <div v-else class="flex flex-col items-center justify-center py-24 text-[#5F7480]">
      <div class="w-10 h-10 border-4 border-[#C0D5D6] border-t-[#083A4F] rounded-full animate-spin mb-3"></div>
      <p class="font-medium">{{ t('channel.loading') }}</p>
    </div>

  </div>
</template>
