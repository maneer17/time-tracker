import { defineStore } from 'pinia'
import { watch } from 'vue'
import { usePagination } from '@/composables/usePagination'
import useApi from '@/composables/useApi'
import notificationService from '@/services/notificationService'
import { useAuthStore } from '@/stores/auth'

export const useNotificationStore = defineStore('notifications', () => {

    const { items: notifications, paginatorData, loading, error, goToPage, refresh } = usePagination(
        (page) => notificationService.getNotifications(page)
    )

    // unread count
    const { data: unreadCount, request: fetchUnreadCount } = useApi(
        () => notificationService.getUnreadCount()
    )

    // Echo subscription is never touched by components
    let echoChannel = null

    const subscribe = (userId) => {
        if (!userId || echoChannel) return

        echoChannel = window.Echo.private(`App.Models.User.${userId}`)
            .notification((notification) => {
                notifications.value.unshift(notification)
                if (unreadCount.value) {
                    unreadCount.value.count++
                }
            })
    }

    const unsubscribe = (userId) => {
        if (echoChannel && userId) {
            window.Echo.leave(`App.Models.User.${userId}`)
            echoChannel = null
        }
    }

    // Watch auth state internally — the store owns the full subscription lifecycle:
    // - user logs in  → subscribe to their channel
    // - user logs out → leave old channel immediately
    // - user switches → leave old, join new (handles token refresh edge cases too)
    // Components never call subscribe/unsubscribe manually
    const authStore = useAuthStore()

    watch(
        () => authStore.user?.id,
        (userId, oldUserId) => {
            if (oldUserId !== userId) {
                unsubscribe(oldUserId)   // leave old channel (logout or user switch)
            }
            if (userId) {
                fetchUnreadCount()       // refresh count for the new user
                subscribe(userId)
            }
        },
        { immediate: true }
    )

    // mark single notification as read
    const markAsRead = async (id) => {
        await notificationService.markAsRead(id)

        const notification = notifications.value.find(n => n.id === id)
        if (notification && !notification.read_at) {
            notification.read_at = new Date().toISOString()
            if (unreadCount.value) unreadCount.value.count--
        }
    }

    // mark all as read
    const markAllAsRead = async () => {
        await notificationService.markAllAsRead()

        notifications.value.forEach(n => {
            if (!n.read_at) n.read_at = new Date().toISOString()
        })

        if (unreadCount.value) unreadCount.value.count = 0
    }

    // delete single notification
    const deleteNotification = async (id) => {
        await notificationService.deleteNotification(id)

        const index = notifications.value.findIndex(n => n.id === id)
        if (index !== -1) {
            if (!notifications.value[index].read_at && unreadCount.value) {
                unreadCount.value.count--
            }
            notifications.value.splice(index, 1)
        }
    }

    return {
        notifications,
        paginatorData,
        loading,
        error,
        goToPage,
        refresh,
        unreadCount,
        markAsRead,
        markAllAsRead,
        deleteNotification,
        // subscribe/unsubscribe intentionally NOT exported —
        // the store self-manages via the auth watcher above
    }
})