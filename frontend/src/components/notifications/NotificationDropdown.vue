<script setup>
import { useNotificationStore } from '@/stores/notificationStore'
import NotificationItem from './NotificationItem.vue'
import { useRouter } from 'vue-router'

const router = useRouter()
const notificationStore = useNotificationStore()

const handleMarkAllAsRead = async () => {
    await notificationStore.markAllAsRead()
}

const goToNotificationsPage = () => {
    router.push({ name: 'Notifications' })
}
</script>

<template>
    <div class="w-80 max-h-[500px] flex flex-col bg-white rounded-3xl shadow-lg border border-[#F0ECE4] overflow-hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#F3EEE6] bg-[#FCFAF6]">
            <h3 class="text-base font-semibold text-[#2D3436]">
                Notifications
            </h3>
            <button
                v-if="notificationStore.notifications.length"
                @click="handleMarkAllAsRead"
                class="text-xs font-medium text-[#5A7D5A] hover:text-[#466446] transition-colors"
            >
                Mark all as read
            </button>
        </div>

        <!-- Notification List -->
        <div
            v-if="notificationStore.notifications.length"
            class="flex-1 overflow-y-auto divide-y divide-[#F5F1E8]"
        >
            <NotificationItem
                v-for="notification in notificationStore.notifications.slice(0, 10)"
                :key="notification.id"
                :notification="notification"
            />
        </div>

        <!-- Empty State -->
        <div
            v-else
            class="flex flex-col items-center justify-center py-12 px-6 text-center"
        >
            <div class="text-4xl mb-3">🔔</div>
            <p class="text-sm font-medium text-[#2D3436]">No notifications yet</p>
            <p class="text-xs text-[#8E9AAF] mt-1">You're all caught up.</p>
        </div>

        <!-- Footer -->
        <div
            v-if="notificationStore.notifications.length"
            class="px-5 py-3 border-t border-[#F3EEE6] bg-[#FCFAF6]"
        >
            <button
                @click="goToNotificationsPage"
                class="w-full text-xs font-medium text-center text-[#5A7D5A] hover:text-[#466446] transition-colors"
            >
                See all notifications
            </button>
        </div>
    </div>
</template>