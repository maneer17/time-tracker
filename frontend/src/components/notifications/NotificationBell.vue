<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useNotificationStore } from '@/stores/notificationStore'
import NotificationDropdown from './NotificationDropdown.vue'

const notificationStore = useNotificationStore()

const isOpen = ref(false)
const bellRef = ref(null)

const unreadCount = computed(() => notificationStore.unreadCount?.count ?? 0)

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
}

// close when clicking outside
const handleClickOutside = (e) => {
    if (bellRef.value && !bellRef.value.contains(e.target)) {
        isOpen.value = false
    }
}
onMounted(() => {
    document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside)
})
</script>

<template>
    <div ref="bellRef" class="relative">

        <!-- Bell Button -->
        <button
            @click.stop="toggleDropdown"
            class="relative p-2.5 rounded-2xl transition-all duration-200 hover:bg-[#F0F4F8] active:scale-95"
            :class="{ 'bg-[#F0F4F8]': isOpen }"
        >
            <!-- Bell Icon -->
            <svg
                class="w-5 h-5 transition-colors"
                :class="unreadCount > 0 ? 'text-[#5A7D5A]' : 'text-[#8E9AAF]'"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                />
            </svg>

            <!-- Unread Badge -->
            <transition name="badge">
                <span
                    v-if="unreadCount > 0"
                    class="absolute -top-1 -right-1 min-w-[18px] h-[18px] flex items-center justify-center bg-[#AF4E4E] text-white text-[10px] font-bold rounded-full px-1 shadow-sm"
                >
                    {{ unreadCount > 99 ? '99+' : unreadCount }}
                </span>
            </transition>
        </button>

        <!-- Dropdown -->
        <transition name="dropdown">
            <div
                v-if="isOpen"
                class="absolute right-0 top-full mt-3 z-50"
                @click.stop
            >
                <NotificationDropdown />
            </div>
        </transition>

    </div>
</template>

<style scoped>
.badge-enter-active, .badge-leave-active {
    transition: all 0.2s ease;
}
.badge-enter-from, .badge-leave-to {
    opacity: 0;
    transform: scale(0.5);
}

.dropdown-enter-active, .dropdown-leave-active {
    transition: all 0.2s ease;
}
.dropdown-enter-from, .dropdown-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>