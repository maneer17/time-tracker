<script setup>
import { ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import commentService from '@/services/commentService'
import { useCommentStore } from '@/stores/commentStore'

const { t } = useI18n()
const sharedDay = inject('sharedDay')
const commentStore = useCommentStore()

const formData = ref({ body: '' })

const { data, error, loading, request } = useApi(
    () => commentService.createComment(
        sharedDay.value.id,
        formData.value
    )
)

const submit = async () => {
    await request()
    if (!error.value) {
        commentStore.add(data.value)
        formData.value.body = ''
    }
}
</script>
<template>
    <form @submit.prevent="submit" class="flex flex-col gap-3 p-3">
        <textarea
            v-model="formData.body"
            :placeholder="t('comments.placeholder')"
            rows="3"
            class="w-full px-4 py-3 text-sm text-[#083A4F] bg-white border border-[#C0D5D6] rounded-xl placeholder-[#5F7480]/70 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15 resize-none transition-colors" />

        <p v-if="error?.body" class="px-1 text-xs text-[#991B1B]">
            {{ error.body[0] }}
        </p>

        <button
            type="submit"
            :disabled="loading || !formData.body.trim()"
            class="self-end px-5 py-2 bg-[#083A4F] hover:bg-[#407E8C] text-white text-sm font-medium rounded-full disabled:opacity-40 transition-colors">
            {{ loading ? t('comments.submitting') : t('comments.submit') }}
        </button>
    </form>
</template>
