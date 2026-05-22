import { ref, onUnmounted, watch } from 'vue'
import { usePagination } from './usePagination'
import useApi from './useApi'
import notificationService from '@/services/notificationService'

export function useNotification(userId) {

    const { items: notifications, paginatorData, loading, error, goToPage } = usePagination(
        (page) => notificationService.getNotifications(page)
    )

    const { data: unreadCount, request: fetchUnreadCount } = useApi(
        () => notificationService.getUnreadCount()
    )

    fetchUnreadCount()

    let channel = null

    const subscribe = (id) => {
        if (!id) return

        channel = window.Echo.private(`App.Models.User.${id}`)
            .notification((notification) => {
                console.log('SUBSCRIBING USER ID:', userId)
                notifications.value.unshift(notification)

                if (unreadCount.value) {
                    unreadCount.value.count++
                }
            })
    }

    //wait for userId 
    watch(
        () => userId,
        (id) => {
            if (!id) return
            subscribe(id)
        },
        { immediate: true }
    )

    onUnmounted(() => {
        if (userId) {
            window.Echo.leave(`App.Models.User.${userId}`)
        }
    })

    const markAsRead = async (id) => {
        await notificationService.markAsRead(id)

        const notification = notifications.value.find(n => n.id === id)
        if (notification && !notification.read_at) {
            notification.read_at = new Date().toISOString()
            if (unreadCount.value) unreadCount.value.count--
        }
    }

    const markAllAsRead = async () => {
        await notificationService.markAllAsRead()

        notifications.value.forEach(n => {
            if (!n.read_at) n.read_at = new Date().toISOString()
        })

        if (unreadCount.value) unreadCount.value.count = 0
    }

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
        unreadCount,
        markAsRead,
        markAllAsRead,
        deleteNotification,
    }
}