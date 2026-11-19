<script setup>
import { ref, inject, onMounted, onUnmounted } from 'vue'
import { useI18n } from 'vue-i18n'
import CommentItem from './CommentItem.vue'
import AddComment from './AddComment.vue'
import { useCommentStore } from '@/stores/commentStore'

const { t } = useI18n()
const sharedDay = inject('sharedDay')
const commentStore = useCommentStore()
const showComments = ref(true)

const toggleComments = () => {
    showComments.value = !showComments.value
    if (showComments.value && commentStore.comments.length === 0) {
        commentStore.fetch(sharedDay.value.id)
    }
}

// The discussion is open straight away, so load it as soon as the shared day is shown
onMounted(() => commentStore.fetch(sharedDay.value.id))
onUnmounted(() => commentStore.reset())
</script>

<template>
    <div>

        <button
            @click="toggleComments"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#083A4F] bg-[#EFECE8] hover:bg-[#E5E1DD] rounded-full transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            {{ showComments ? t('comments.hide') : t('comments.show') }}
        </button>

        <div v-if="showComments" class="mt-5 space-y-4">

            <div class="bg-[#FAF9F7] rounded-2xl border border-[#D9D4CE]">
                <AddComment />
            </div>

            <div v-if="commentStore.error"
                class="p-4 bg-[#FEE2E2] text-[#991B1B] rounded-2xl border border-[#FECACA] text-sm font-medium">
                {{ commentStore.error }}
            </div>

            <div v-else-if="commentStore.comments.length > 0" class="space-y-3">
                <CommentItem
                    v-for="comment in commentStore.comments"
                    :key="comment.id"
                    :comment="comment" />

                <button
                    v-if="commentStore.hasMore"
                    @click="commentStore.loadMore(sharedDay.value.id)"
                    :disabled="commentStore.loading"
                    class="w-full py-2.5 text-sm font-medium text-[#3E5660] hover:text-[#083A4F] disabled:opacity-50 transition-colors bg-[#EFECE8] rounded-full">
                    {{ commentStore.loading ? '...' : t('comments.load_more') }}
                </button>
            </div>

            <div v-else-if="!commentStore.loading"
                class="text-center py-10 bg-[#FAF9F7] rounded-2xl border border-dashed border-[#D9D4CE]">
                <p class="text-sm text-[#5F7480]">{{ t('comments.empty') }}</p>
            </div>

            <div v-if="commentStore.loading && commentStore.comments.length === 0" class="space-y-3">
                <div v-for="i in 3" :key="i" class="h-16 bg-[#EFECE8] rounded-2xl animate-pulse"></div>
            </div>

        </div>
    </div>
</template>
