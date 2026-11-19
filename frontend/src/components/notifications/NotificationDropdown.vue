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
    <div class="w-80 max-h-[500px] flex flex-col bg-white rounded-3xl border border-[#F0ECE4] overflow-hidden">

        <!-- Header -->
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#F3EEE4] bg-[#FAF9F7]">
            <h3 class="text-base font-semibold text-[#083A4F]">
                {{ $t('notifications.title') }}
            </h3>
            <button
                v-if="notificationStore.notifications.length"
                @click="handleMarkAllAsRead"
                class="text-xs font-medium text-[#083A4F] hover:text-[#407E8C] transition-colors"
            >
                {{ $t('notifications.mark_all_read') }}
            </button>
        </div>

        <!-- Notification List -->
        <div
            v-if="notificationStore.notifications.length"
            class="flex-1 overflow-y-auto divide-y divide-[#F3EEE4]"
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
            <div class="text-4xl mb-3"></div>
            <p class="text-sm font-medium text-[#083A4F]">{{ $t('notifications.empty_title') }}</p>
            <p class="text-xs text-[#5F7480] mt-1">{{ $t('notifications.empty_hint') }}</p>
        </div>

        <!-- Footer -->
        <div
            v-if="notificationStore.notifications.length"
            class="px-5 py-3 border-t border-[#F3EEE4] bg-[#FAF9F7]"
        >
            <button
                @click="goToNotificationsPage"
                class="w-full text-xs font-medium text-center text-[#083A4F] hover:text-[#407E8C] transition-colors"
            >
                {{ $t('notifications.see_all') }}
            </button>
        </div>
    </div>
</template>