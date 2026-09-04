<!-- NotificationItem.vue -->
<script setup>
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '@/stores/notificationStore'

dayjs.extend(relativeTime)

const router = useRouter()
const notificationStore = useNotificationStore()

const props = defineProps(['notification'])

const getRoute = (notification) => {
    const { type, data } = notification
    switch (type) {
        case 'channel_invitations':   return '/my-invitations'
        case 'accepted_invitations':  return `/channels/${data.channel_id}`
        case 'new_comments':          return `/channels/${data.channel_id}/shared-days/${data.shared_day_id}`
        case 'new_shared_days':       return `/channels/${data.channel_id}`
        case 'channel_export_ready':  return data.download_url
        default:                      return '/'
    }
}

const handleClick = async () => {
    if (!props.notification.read_at) {
        await notificationStore.markAsRead(props.notification.id)
    }

    // export-ready notifications point to an external backend file URL,
    // not an internal Vue route — router.push() can't handle that,
    // so we branch and open it directly in a new tab instead
    if (props.notification.type === 'channel_export_ready') {
        window.open(props.notification.data.download_url, '_blank')
        return
    }

    router.push(getRoute(props.notification))
}

const handleDelete = async (e) => {
    e.stopPropagation()
    await notificationStore.deleteNotification(props.notification.id)
}

const handleMarkRead = async (e) => {
    e.stopPropagation()
    await notificationStore.markAsRead(props.notification.id)
}
</script>

<template>
    <div
        class="group flex items-start gap-3 px-4 py-3 hover:bg-[#F9F7F2] transition-colors cursor-pointer"
        :class="{ 'bg-[#F0F4F8]/50': !notification.read_at }"
        @click="handleClick"
    >
        <!-- unread dot -->
        <div
            class="mt-1.5 shrink-0 h-2 w-2 rounded-full transition-colors"
            :class="notification.read_at ? 'bg-transparent' : 'bg-[#5A7D5A]'"
        />

        <!-- content -->
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-[#2D3436] leading-snug">
                {{ notification.data?.from }}
                <span class="font-normal text-[#8E9AAF]">{{ notification.data?.message }}</span>
            </p>
            <span class="text-[11px] text-[#A0A0A0] mt-0.5 block">
                {{ dayjs(notification.created_at).fromNow() }}
            </span>
        </div>

        <!-- actions -->
        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0" @click.stop>
            <button
                v-if="!notification.read_at"
                @click="handleMarkRead"
                class="p-1.5 rounded-xl text-[#8E9AAF] hover:bg-[#E2E8F0] hover:text-[#5A7D5A] transition-all"
                title="Mark as read"
            >
                ✓
            </button>
            <button
                @click="handleDelete"
                class="p-1.5 rounded-xl text-[#8E9AAF] hover:bg-[#FFF2F2] hover:text-[#AF4E4E] transition-all"
                title="Delete"
            >
                ✕
            </button>
        </div>
    </div>
</template>