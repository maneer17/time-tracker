<script setup>
import { ref, watch } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import ChannelCard from '@/components/channel/ChannelCard.vue';
import channelService from '@/services/channelService';
import { usePagination } from '@/composables/usePagination';
import AppPagination from '@/components/AppPagination.vue';

const router = useRouter();
const { t } = useI18n();

/**
 * Reactive filters object (better architecture)
 */
const filters = ref({
  type: 'owned'
});

const activeTab = ref('owned');

const {
  items: channels,
  paginatorData,
  loading,
  error,
  goToPage,
  refresh
} = usePagination(
  (page) =>
    channelService.getChannels({
      page,
      ...filters.value
    }),
  { immediate: true }
);

/**
 * Smooth reactive refresh
 */
watch(
  filters,
  () => {
    refresh();
  },
  { deep: true }
);

/**
 * Handle tab switching
 */
const changeTab = (type) => {
  activeTab.value = type;
  filters.value.type = type;
};
</script>

<template>
  <div class="max-w-7xl mx-auto px-6 py-12 min-h-screen">

    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 mb-12">
      <div>
        <h1 class="text-4xl font-black text-[#2D3436] tracking-tight">
          {{ t('channel_list.title') }}
        </h1>
        <p class="text-[#7F8C8D] mt-2 font-medium">
          Manage your communication hubs and workspaces.
        </p>
      </div>

      <router-link
        :to="{ name: 'AddChannel' }"
        class="group flex items-center bg-[#5A7D5A] hover:bg-[#4a6b4a] text-white px-8 py-4 rounded-[1.5rem] transition-all shadow-xl shadow-[#5A7D5A]/20 active:scale-95 font-bold"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          class="h-5 w-5 mr-2 transition-transform group-hover:rotate-90"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="3"
            d="M12 4v16m8-8H4"
          />
        </svg>
        {{ t('channel_list.new_channel') }}
      </router-link>
    </div>

    <!-- Tabs -->
    <div class="inline-flex p-2 bg-[#E2E8F0] rounded-[2rem] mb-10 shadow-inner">
      <button
        @click="changeTab('owned')"
        :class="
          activeTab === 'owned'
            ? 'bg-white text-[#2D3436] shadow-md'
            : 'text-[#64748B] hover:text-[#2D3436]'
        "
        class="px-8 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300"
      >
        {{ t('channel_list.my_channels') }}
      </button>

      <button
        @click="changeTab('joined')"
        :class="
          activeTab === 'joined'
            ? 'bg-white text-[#2D3436] shadow-md'
            : 'text-[#64748B] hover:text-[#2D3436]'
        "
        class="px-8 py-3 rounded-[1.5rem] text-sm font-black transition-all duration-300"
      >
        {{ t('channel_list.member_of') }}
      </button>
    </div>

    <!-- Error -->
    <div
      v-if="error"
      class="p-6 bg-[#FFF2F2] text-[#AF4E4E] rounded-[2.5rem] border-2 border-[#FFDADA] flex items-center gap-4"
    >
      <span class="text-2xl">⚠️</span>
      <span class="font-bold">
        {{ t('channel_list.error') }}: {{ error }}
      </span>
    </div>

    <!-- Loading -->
    <div
      v-else-if="loading && !channels.length"
      class="grid grid-cols-1 md:grid-cols-3 gap-8"
    >
      <div
        v-for="i in 3"
        :key="i"
        class="h-64 bg-white/50 animate-pulse rounded-[3rem] border-2 border-dashed border-gray-200"
      ></div>
    </div>

    <!-- Content -->
    <div v-else>

      <!-- Channels -->
      <div
        v-if="channels.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8"
      >
        <div
          v-for="channel in channels"
          :key="channel.id"
          @click="router.push(`/channels/${channel.id}`)"
          class="group cursor-pointer"
        >
          <ChannelCard
            :channel="channel"
            :class="[
              'h-full border-2 bg-white rounded-[2.5rem] transition-all duration-500',
              activeTab === 'owned'
                ? 'border-[#D1D5DB] group-hover:border-[#5A7D5A] group-hover:shadow-2xl group-hover:shadow-[#5A7D5A]/10'
                : 'border-[#D1D5DB] group-hover:border-[#8E9AAF] group-hover:shadow-2xl group-hover:shadow-[#8E9AAF]/10'
            ]"
          />
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else
        class="flex flex-col items-center justify-center py-24 bg-white/40 rounded-[3.5rem] border-4 border-dashed border-[#D1D5DB]"
      >
        <div class="bg-white p-6 rounded-full shadow-lg mb-6 text-4xl">
          {{ activeTab === 'owned' ? '🪴' : '🤝' }}
        </div>

        <p class="text-[#64748B] font-bold text-lg mb-4">
          {{
            activeTab === 'owned'
              ? t('channel_list.no_owned')
              : t('channel_list.no_joined')
          }}
        </p>

        <router-link
          v-if="activeTab === 'owned'"
          :to="{ name: 'AddChannel' }"
          class="text-[#5A7D5A] font-black hover:text-[#4a6b4a] underline decoration-4 underline-offset-8 transition-all"
        >
          {{ t('channel_list.create_first') }}
        </router-link>
      </div>

      <!-- Pagination -->
      <AppPagination
        v-if="paginatorData.meta"
        :data="paginatorData"
        @page-change="goToPage"
        class="mt-10"
      />

    </div>
  </div>
</template>