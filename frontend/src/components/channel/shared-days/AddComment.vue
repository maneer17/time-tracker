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
        sharedDay.value.channel_id,
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
    <form @submit.prevent="submit" class="flex flex-col gap-4 p-4">
        <textarea
            v-model="formData.body"
            :placeholder="t('comments.placeholder')"
            rows="3"
            class="w-full px-5 py-4 text-[14px] font-medium text-[#4A4A4A] bg-white border-2 border-[#F9F7F2] rounded-[1.5rem] placeholder-[#A0A0A0] focus:outline-none focus:border-[#5A7D5A] focus:bg-white resize-none transition-all duration-300 shadow-sm" />

        <p v-if="error?.body" class="px-2 text-[11px] font-bold text-[#E07A5F] italic tracking-wide">
            {{ error.body[0] }}
        </p>

        <button
            type="submit"
            :disabled="loading || !formData.body.trim()"
            class="self-end px-8 py-2.5 bg-[#5A7D5A] hover:bg-[#4A6D4A] text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-xl disabled:opacity-40 disabled:grayscale transition-all shadow-lg shadow-[#5A7D5A]/10 active:scale-95">
            {{ loading ? t('comments.submitting') : t('comments.submit') }}
        </button>
    </form>
</template>