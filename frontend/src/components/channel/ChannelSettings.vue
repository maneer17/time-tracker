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
  <div class="bg-white border border-red-100 rounded-2xl p-6">
    <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ t('channel.delete_channel') }}</h3>
    <p class="text-gray-500 text-sm mb-4">{{ t('channel.delete_warning') }}</p>

    <template v-if="!confirmDelete">
      <button
        @click="confirmDelete = true"
        class="px-4 py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-100 transition-colors text-sm font-medium">
        {{ t('channel.delete_channel') }}
      </button>
    </template>

    <template v-else>
      <p class="text-gray-700 text-sm mb-3">{{ t('channel.delete_confirm') }}</p>
      <div class="flex gap-2">
        <button
          @click="remove"
          :disabled="loading"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50 transition-colors text-sm font-medium">
          {{ loading ? '...' : t('channel.yes') }}
        </button>
        <button
          @click="confirmDelete = false"
          :disabled="loading"
          class="px-4 py-2 border border-slate-200 rounded-lg hover:bg-slate-50 disabled:opacity-50 transition-colors text-sm font-medium">
          {{ t('channel.no') }}
        </button>
      </div>
    </template>
  </div>
</template>