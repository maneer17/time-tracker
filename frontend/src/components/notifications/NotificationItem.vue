<!-- NotificationItem.vue -->
<script setup>
import { computed } from 'vue'
import { useI18n } from 'vue-i18n'
import { useRouter } from 'vue-router'
import { useNotificationStore } from '@/stores/notificationStore'
import { useLocaleFormat } from '@/composables/useLocaleFormat'

const { t, te } = useI18n()
const { fromNow } = useLocaleFormat()
const router = useRouter()
const notificationStore = useNotificationStore()

const props = defineProps(['notification'])

// Newer notifications carry structured fields, so the sentence is built in the
// user's language. Older ones only stored an English sentence; show that as is.
const sentence = computed(() => {
    const { type, data = {} } = props.notification
    const key = `notifications.messages.${type}`

    if (data.channel_name !== undefined && te(key)) {
        return {
            from: data.from,
            keypath: key,
            // Names and comment text are user content in any language; they are isolated with <bdi>
            // in the template so an English excerpt can't scramble an Arabic sentence (or the reverse).
            params: { channel: data.channel_name, excerpt: data.excerpt ?? '', date: data.shared_day_date ?? '' },
        }
    }

    const alreadyNamed = data.from && data.message?.startsWith(data.from)
    return { from: alreadyNamed ? null : data.from, legacy: data.message }
})

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
        class="group flex items-start gap-3 px-4 py-3 hover:bg-[#FAF9F7] transition-colors cursor-pointer"
        :class="{ 'bg-[#EFECE8]/50': !notification.read_at }"
        @click="handleClick"
    >
        <!-- unread dot -->
        <div
            class="mt-1.5 shrink-0 h-2 w-2 rounded-full transition-colors"
            :class="notification.read_at ? 'bg-transparent' : 'bg-[#083A4F]'"
        />

        <!-- content -->
        <div class="flex-1 min-w-0">
            <p class="text-sm font-medium text-[#083A4F] leading-snug">
                <span v-if="sentence.from"><bdi>{{ sentence.from }}</bdi>{{ ' ' }}</span>
                <span class="font-normal text-[#5F7480]">
                    <i18n-t v-if="sentence.keypath" :keypath="sentence.keypath" tag="span">
                        <template #channel><bdi>{{ sentence.params.channel }}</bdi></template>
                        <template #excerpt><bdi>{{ sentence.params.excerpt }}</bdi></template>
                        <template #date><bdi>{{ sentence.params.date }}</bdi></template>
                    </i18n-t>
                    <template v-else>{{ sentence.legacy }}</template>
                </span>
            </p>
            <span class="text-xs text-[#5F7480] mt-0.5 block">
                {{ fromNow(notification.created_at) }}
            </span>
        </div>

        <!-- actions -->
        <div class="flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0" @click.stop>
            <button
                v-if="!notification.read_at"
                @click="handleMarkRead"
                class="p-1.5 rounded-xl text-[#5F7480] hover:bg-[#E2E8F0] hover:text-[#083A4F] transition-all"
                :title="t('notifications.mark_read')"
            >
                ✓
            </button>
            <button
                @click="handleDelete"
                class="p-1.5 rounded-xl text-[#5F7480] hover:bg-[#FEE2E2] hover:text-[#991B1B] transition-all"
                :title="t('notifications.delete')"
            >
                ✕
            </button>
        </div>
    </div>
</template>