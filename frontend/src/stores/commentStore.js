import { defineStore } from 'pinia'
import commentService from '@/services/commentService'

export const useCommentStore = defineStore('commentStore', {
    state: () => ({
        comments: [],
        meta: { current_page: 1, last_page: 1 },
        loading: false,
        error: null,
    }),

    getters: {
        hasMore: (state) => state.meta.current_page < state.meta.last_page
    },

    actions: {
        async fetch(sharedDayId, page = 1) {
            this.loading = true
            this.error = null
            try {
                const response = await commentService.getComments(sharedDayId, { page })
                this.comments = page === 1
                    ? response.data.data
                    : [...this.comments, ...response.data.data]
                this.meta = response.data.meta
            } catch (err) {
                this.error = err.response?.data?.errors ?? err.message
            } finally {
                this.loading = false
            }
        },

        loadMore(sharedDayId) {
            if (!this.hasMore || this.loading) return
            this.fetch(sharedDayId, this.meta.current_page + 1)
        },

        add(comment)    { this.comments.unshift(comment) },
        remove(id)      { this.comments = this.comments.filter(c => c.id !== id) },
        update(updated) {
            const i = this.comments.findIndex(c => c.id === updated.id)
            if (i !== -1) this.comments[i] = updated
        },
        reset()         { this.$reset() }
    }
})