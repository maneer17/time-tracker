<script setup>
import { useNotificationStore } from '@/stores/notificationStore'
import NotificationItem from '@/components/notifications/NotificationItem.vue'

const notificationStore = useNotificationStore()
</script>

<template>
    <div class="max-w-2xl mx-auto py-8 px-4 space-y-4">

        <!-- Header -->
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[#2D3436]">Notifications</h1>
            <button
                v-if="notificationStore.notifications.length"
                @click="notificationStore.markAllAsRead"
                class="text-sm font-medium text-[#5A7D5A] hover:text-[#466446] transition-colors"
            >
                Mark all as read
            </button>
        </div>

        <!-- Loading -->
        <div v-if="notificationStore.loading" class="space-y-3">
            <div
                v-for="i in 5" :key="i"
                class="h-16 bg-[#F9F7F2] rounded-2xl animate-pulse"
            />
        </div>

        <!-- Notifications List -->
        <div
            v-else-if="notificationStore.notifications.length"
            class="bg-white rounded-3xl border border-[#F0ECE4] overflow-hidden divide-y divide-[#F5F1E8]"
        >
            <NotificationItem
                v-for="notification in notificationStore.notifications"
                :key="notification.id"
                :notification="notification"
            />
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center py-24 text-center"
        >
            <div class="text-5xl mb-4">🔔</div>
            <p class="text-base font-semibold text-[#2D3436]">No notifications yet</p>
            <p class="text-sm text-[#8E9AAF] mt-1">You're all caught up.</p>
        </div>

        <!-- Pagination -->
        <div
            v-if="notificationStore.paginatorData"
            class="flex items-center justify-center gap-2 pt-4"
        >
            <button
                :disabled="!notificationStore.paginatorData.meta?.links?.find(l => l.label.includes('Previous'))?.url"
                @click="notificationStore.goToPage(notificationStore.paginatorData.meta.current_page - 1)"
                class="px-4 py-2 rounded-xl text-sm font-medium text-[#8E9AAF] hover:bg-[#F0F4F8] disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            >
                Previous
            </button>

            <span class="text-sm text-[#8E9AAF]">
                Page {{ notificationStore.paginatorData.meta?.current_page }}
                of {{ notificationStore.paginatorData.meta?.last_page }}
            </span>

            <button
                :disabled="!notificationStore.paginatorData.meta?.links?.find(l => l.label.includes('Next'))?.url"
                @click="notificationStore.goToPage(notificationStore.paginatorData.meta.current_page + 1)"
                class="px-4 py-2 rounded-xl text-sm font-medium text-[#8E9AAF] hover:bg-[#F0F4F8] disabled:opacity-40 disabled:cursor-not-allowed transition-all"
            >
                Next
            </button>
        </div>

    </div>
</template>