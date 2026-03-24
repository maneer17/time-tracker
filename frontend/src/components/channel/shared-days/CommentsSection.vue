<script setup>
import { ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import CommentItem from './CommentItem.vue'
import AddComment from './AddComment.vue'
import { useCommentStore } from '@/stores/commentStore'
import { onUnmounted } from 'vue'

const { t } = useI18n()
const sharedDay = inject('sharedDay')
const commentStore = useCommentStore()
const showComments = ref(false)

const toggleComments = () => {
    showComments.value = !showComments.value
    if (showComments.value && commentStore.comments.length === 0) {
        commentStore.fetch(sharedDay.value.channel_id, sharedDay.value.id)
    }
}

onUnmounted(() => {
    commentStore.reset()
})
</script>

<template>
    <div class="mt-8">

        <button
            @click="toggleComments"
            class="flex items-center gap-2 px-4 py-2 text-[11px] font-black uppercase tracking-[0.2em] text-[#5A7D5A] hover:bg-[#F1F5F1] rounded-xl transition-all active:scale-95">
            <span class="text-lg">{{ showComments ? '💬' : '🗨️' }}</span>
            {{ showComments ? t('comments.hide') : t('comments.show') }}
        </button>

        <div v-if="showComments" class="mt-6 space-y-6 animate-in fade-in slide-in-from-top-4 duration-300">

            <div class="bg-[#FDFCFB] p-2 rounded-[2rem] border border-[#F2E9E4]">
                <AddComment />
            </div>

            <div v-if="commentStore.error"
                class="p-4 bg-[#FFF2F0] text-[#E07A5F] rounded-2xl border border-[#FFE5E0] text-[13px] font-bold">
                ⚠️ {{ commentStore.error }}
            </div>

            <div v-else-if="commentStore.comments.length > 0" class="space-y-4">
                <CommentItem
                    v-for="comment in commentStore.comments"
                    :key="comment.id"
                    :comment="comment" />

                <button
                    v-if="commentStore.hasMore"
                    @click="commentStore.loadMore(sharedDay.channel_id, sharedDay.id)"
                    :disabled="commentStore.loading"
                    class="w-full py-4 text-[11px] font-black uppercase tracking-[0.2em] text-[#A0A0A0] hover:text-[#5A7D5A] disabled:opacity-50 transition-colors bg-[#F9F7F2] rounded-2xl">
                    {{ commentStore.loading ? '...' : t('comments.load_more') }}
                </button>
            </div>

            <div v-else-if="!commentStore.loading"
                class="text-center py-12 bg-[#FDFCFB] rounded-[2.5rem] border-2 border-dashed border-[#F2E9E4]">
                <div class="text-3xl mb-3 opacity-50">🍃</div>
                <p class="text-[13px] font-bold text-[#A0A0A0] italic">
                    {{ t('comments.empty') }}
                </p>
            </div>

            <div v-if="commentStore.loading && commentStore.comments.length === 0"
                class="space-y-4">
                <div v-for="i in 3" :key="i" class="h-20 bg-[#F9F7F2] rounded-[2rem] animate-pulse"></div>
            </div>

        </div>
    </div>
</template>