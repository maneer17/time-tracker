import { defineStore } from 'pinia'
import commentService from '@/services/commentService'

export const useCommentStore = defineStore('commentStore', {
    state: () => ({
        comments: [],
        currentPage: 1,
        lastPage: 1,
        loading: false,
        error: null,
    }),

    getters: {
        hasMore() {
            return this.currentPage < this.lastPage
        }
    },

    actions: {
        async fetch(channelId, sharedDayId) {
            this.loading = true
            this.error = null
            try {
                const response = await commentService.getComments(channelId, sharedDayId, 1)
                this.comments = response.data.data
                this.lastPage = response.data.meta.last_page
                this.currentPage = 1
            } catch (err) {
                this.error = err.response?.data?.errors ?? err.message
            } finally {
                this.loading = false
            }
        },

        async loadMore(channelId, sharedDayId) {
            this.currentPage++
            this.loading = true
            this.error = null
            try {
                const response = await commentService.getComments(channelId, sharedDayId, this.currentPage)
                this.comments = [...this.comments, ...response.data.data]
                this.lastPage = response.data.meta.last_page
            } catch (err) {
                this.currentPage--  // revert page increment if failed
                this.error = err.response?.data?.errors ?? err.message
            } finally {
                this.loading = false
            }
        },

        add(comment) {
            this.comments.unshift(comment)
        },

        remove(id) {
            this.comments = this.comments.filter(c => c.id !== id)
        },

        update(updated) {
            const index = this.comments.findIndex(c => c.id === updated.id)
            if (index !== -1) this.comments[index] = updated
        },

        reset() {
            this.comments = []
            this.currentPage = 1
            this.lastPage = 1
            this.loading = false
            this.error = null
        }
    }
})