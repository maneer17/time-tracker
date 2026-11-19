<script setup>
import { useNotificationStore } from '@/stores/notificationStore'
import NotificationItem from '@/components/notifications/NotificationItem.vue'

const notificationStore = useNotificationStore()
import PageShell from '@/components/PageShell.vue'
</script>

<template>
    <PageShell :title="$t('notifications.title')" narrow>
        <template #actions>
            <button
                v-if="notificationStore.notifications.length"
                @click="notificationStore.markAllAsRead"
                class="px-4 py-2 rounded-full text-sm font-medium text-[#3E5660] hover:bg-[#EFECE8] hover:text-[#083A4F] transition-colors">
                {{ $t('notifications.mark_all_read') }}
            </button>
        </template>

        <!-- Loading -->
        <div v-if="notificationStore.loading" class="space-y-3">
            <div v-for="i in 5" :key="i" class="h-16 bg-[#EFECE8] rounded-2xl animate-pulse" />
        </div>

        <!-- Notifications list -->
        <div
            v-else-if="notificationStore.notifications.length"
            class="bg-white rounded-3xl border border-[#D9D4CE] overflow-hidden divide-y divide-[#D9D4CE]">
            <NotificationItem
                v-for="notification in notificationStore.notifications"
                :key="notification.id"
                :notification="notification" />
        </div>

        <!-- Empty -->
        <div v-else class="flex flex-col items-center justify-center py-16 bg-white border border-dashed border-[#D9D4CE] rounded-3xl text-center px-6">
            <p class="font-semibold">{{ $t('notifications.empty_title') }}</p>
            <p class="text-sm text-[#5F7480] mt-1">{{ $t('notifications.empty_hint') }}</p>
        </div>

        <!-- Pagination -->
        <div v-if="notificationStore.paginatorData" class="flex items-center justify-center gap-2">
            <button
                :disabled="notificationStore.paginatorData.meta?.current_page <= 1"
                @click="notificationStore.goToPage(notificationStore.paginatorData.meta.current_page - 1)"
                class="px-4 py-2 rounded-full text-sm font-medium text-[#3E5660] hover:bg-[#EFECE8] disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                {{ $t('pagination.previous') }}
            </button>

            <span class="text-sm text-[#5F7480]">
                {{ $t('pagination.page_of', {
                    current: notificationStore.paginatorData.meta?.current_page,
                    last: notificationStore.paginatorData.meta?.last_page,
                }) }}
            </span>

            <button
                :disabled="notificationStore.paginatorData.meta?.current_page >= notificationStore.paginatorData.meta?.last_page"
                @click="notificationStore.goToPage(notificationStore.paginatorData.meta.current_page + 1)"
                class="px-4 py-2 rounded-full text-sm font-medium text-[#3E5660] hover:bg-[#EFECE8] disabled:opacity-40 disabled:cursor-not-allowed transition-colors">
                {{ $t('pagination.next') }}
            </button>
        </div>
    </PageShell>
</template>
