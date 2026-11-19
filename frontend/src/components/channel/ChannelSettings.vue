<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import channelService from '@/services/channelService'
import useApi from '@/composables/useApi'

import { inject } from 'vue'

const isOwner = inject('isOwner')
const channel = inject('channel')
const router = useRouter()
const { t } = useI18n()

const confirmDelete = ref(false)
const { loading, error: deleteError, request: deleteRequest } = useApi(
  () => channelService.deleteChannel(channel.value.id)
)

const remove = async () => {
  await deleteRequest()
  if (!deleteError.value) router.push({ name: 'Channels' })
}
</script>

<template>
  <section class="bg-white border border-[#FECACA] rounded-3xl p-5 sm:p-6">
    <h3 class="font-semibold mb-1">{{ t('channel.delete_channel') }}</h3>
    <p class="text-[#5F7480] text-sm mb-4">{{ t('channel.delete_warning') }}</p>

    <template v-if="!confirmDelete">
      <button
        @click="confirmDelete = true"
        class="px-4 py-2 bg-[#FEE2E2] text-[#991B1B] rounded-full hover:bg-[#FECACA] transition-colors text-sm font-medium">
        {{ t('channel.delete_channel') }}
      </button>
    </template>

    <template v-else>
      <p class="text-sm mb-3">{{ t('channel.delete_confirm') }}</p>
      <div class="flex gap-2">
        <button
          @click="remove"
          :disabled="loading"
          class="px-4 py-2 bg-[#991B1B] text-white rounded-full hover:bg-[#7F1D1D] disabled:opacity-50 transition-colors text-sm font-medium">
          {{ loading ? '...' : t('channel.yes') }}
        </button>
        <button
          @click="confirmDelete = false"
          :disabled="loading"
          class="px-4 py-2 border border-[#C0D5D6] rounded-full hover:bg-[#EFECE8] disabled:opacity-50 transition-colors text-sm font-medium">
          {{ t('channel.no') }}
        </button>
      </div>
    </template>
  </section>
</template>
