<script setup>
import { ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import { useLocaleFormat } from '@/composables/useLocaleFormat'
import useApi from '@/composables/useApi'
import commentService from '@/services/commentService'
import { useCommentStore } from '@/stores/commentStore'
const { t } = useI18n()
const { formatDateTime } = useLocaleFormat()
const props = defineProps(['comment'])

const sharedDay = inject('sharedDay')
const editing = ref(false)
const confirmDelete = ref(false)
const editBody = ref(props.comment.body)
const commentStore = useCommentStore();
const { data: updatedComment, loading: updateLoading, error: updateError, request: updateRequest } = useApi(
    () => commentService.updateComment(
        sharedDay.value.id,
        props.comment.id,
        { body: editBody.value }
    )
    
)

const { loading: deleteLoading, error: deleteError, request: deleteRequest } = useApi(
    () => commentService.deleteComment(
        sharedDay.value.id,
        props.comment.id
    )
)

const update = async () => {
    await updateRequest()
    if (!updateError.value) {
        editing.value = false
        commentStore.update(updatedComment)
    }
}

const remove = async () => {
    await deleteRequest()
    if (!deleteError.value){
        commentStore.remove(props.comment.id)
    } 
}

const cancelEdit = () => {
    editBody.value = props.comment.body
    editing.value = false
}
</script>

<template>
    <div class="group p-5 bg-white border border-[#FAF9F7] rounded-2xl hover:border-[#083A4F]/20 transition-all">

        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-[#EEF3F3] flex items-center justify-center text-[#083A4F] font-semibold text-xs">
                    {{ comment.author.name.charAt(0) }}
                </div>
                <div class="flex flex-col">
                    <span class="font-semibold text-[14px] text-[#083A4F] leading-none">{{ comment.author.name }}</span>
                    <span class="text-xs text-[#5F7480] font-medium mt-1 opacity-80">{{ formatDateTime(comment.created_at) }}</span>
                </div>
            </div>

            <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                    v-if="comment.can_edit && !editing"
                    @click="editing = true"
                    class="p-2 text-[#5F7480] hover:text-[#083A4F] hover:bg-[#EEF3F3] rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828A2 2 0 0110 16H8v-2a2 2 0 01.586-1.414z"/>
                    </svg>
                </button>

                <template v-if="comment.can_delete">
                    <template v-if="!confirmDelete">
                        <button
                            @click="confirmDelete = true"
                            class="p-2 text-[#5F7480] hover:text-[#991B1B] hover:bg-[#FEE2E2] rounded-xl transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </template>
                    <template v-else>
                        <div class="flex items-center gap-1 bg-[#FEE2E2] p-1 rounded-lg border border-[#FECACA]">
                            <button
                                @click="remove"
                                :disabled="deleteLoading"
                                class="px-3 py-1 text-xs font-semibold bg-[#991B1B] text-white rounded-md disabled:opacity-50 transition-all">
                                {{ deleteLoading ? '...' : $t('single_entry.yes') }}
                            </button>
                            <button
                                @click="confirmDelete = false"
                                :disabled="deleteLoading"
                                class="px-3 py-1 text-xs font-semibold text-[#5F7480] hover:text-[#083A4F] transition-all">
                                {{ $t('single_entry.no') }}
                            </button>
                        </div>
                    </template>
                </template>
            </div>
        </div>

        <div v-if="!editing" class="px-1">
            <p class="text-[14px] text-[#083A4F] font-medium leading-[1.6]">{{ comment.body }}</p>
            <p v-if="deleteError" class="mt-2 text-xs font-bold text-[#991B1B]">{{ deleteError }}</p>
        </div>

        <div v-else class="mt-3">
            <textarea
                v-model="editBody"
                rows="3"
                class="w-full px-4 py-3 text-[14px] font-medium text-[#083A4F] bg-[#FAF9F7] border border-[#F3EEE4] rounded-2xl focus:outline-none focus:border-[#083A4F] resize-none transition-all">
            </textarea>
            <p v-if="updateError" class="mt-1 text-xs font-bold text-[#991B1B]">{{ updateError }}</p>
            <div class="flex items-center gap-2 mt-3">
                <button
                    @click="update"
                    :disabled="updateLoading || !editBody.trim()"
                    class="px-5 py-2 text-xs font-semibold bg-[#083A4F] hover:bg-[#407E8C] text-white rounded-xl disabled:opacity-50 transition-all">
                    {{ updateLoading ? '...' : t('comments.save') }}
                </button>
                <button
                    @click="cancelEdit"
                    :disabled="updateLoading"
                    class="px-5 py-2 text-xs font-semibold text-[#5F7480] hover:text-[#083A4F] transition-all">
                    {{ t('comments.cancel') }}
                </button>
            </div>
        </div>

    </div>
</template>