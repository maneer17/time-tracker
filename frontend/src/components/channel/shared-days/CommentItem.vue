<script setup>
import { ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import commentService from '@/services/commentService'
import { useCommentStore } from '@/stores/commentStore'
const { t } = useI18n()
const props = defineProps(['comment'])

const sharedDay = inject('sharedDay')
const editing = ref(false)
const confirmDelete = ref(false)
const editBody = ref(props.comment.body)
const commentStore = useCommentStore();
const { data: updatedComment, loading: updateLoading, error: updateError, request: updateRequest } = useApi(
    () => commentService.updateComment(
        sharedDay.value.channel_id,
        sharedDay.value.id,
        props.comment.id,
        { body: editBody.value }
    )
    
)

const { loading: deleteLoading, error: deleteError, request: deleteRequest } = useApi(
    () => commentService.deleteComment(
        sharedDay.value.channel_id,
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
    <div class="group p-5 bg-white border-2 border-[#F9F7F2] rounded-[1.8rem] hover:border-[#5A7D5A]/20 transition-all">

        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-xl bg-[#F1F5F1] flex items-center justify-center text-[#5A7D5A] font-black text-xs uppercase tracking-wider">
                    {{ comment.author.name.charAt(0) }}
                </div>
                <div class="flex flex-col">
                    <span class="font-black text-[14px] text-[#4A4A4A] leading-none">{{ comment.author.name }}</span>
                    <span class="text-[11px] text-[#A0A0A0] font-medium mt-1 italic opacity-80">{{ comment.created_at }}</span>
                </div>
            </div>

            <div class="flex items-center gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                <button
                    v-if="comment.can_edit && !editing"
                    @click="editing = true"
                    class="p-2 text-[#A0A0A0] hover:text-[#5A7D5A] hover:bg-[#F1F5F1] rounded-xl transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828A2 2 0 0110 16H8v-2a2 2 0 01.586-1.414z"/>
                    </svg>
                </button>

                <template v-if="comment.can_delete">
                    <template v-if="!confirmDelete">
                        <button
                            @click="confirmDelete = true"
                            class="p-2 text-[#A0A0A0] hover:text-[#E07A5F] hover:bg-[#FFF2F0] rounded-xl transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                        </button>
                    </template>
                    <template v-else>
                        <div class="flex items-center gap-1 bg-[#FFF2F0] p-1 rounded-lg border border-[#FFE5E0]">
                            <button
                                @click="remove"
                                :disabled="deleteLoading"
                                class="px-3 py-1 text-[10px] font-black uppercase tracking-widest bg-[#E07A5F] text-white rounded-md disabled:opacity-50 transition-all">
                                {{ deleteLoading ? '...' : $t('single_entry.yes') }}
                            </button>
                            <button
                                @click="confirmDelete = false"
                                :disabled="deleteLoading"
                                class="px-3 py-1 text-[10px] font-black uppercase tracking-widest text-[#A0A0A0] hover:text-[#4A4A4A] transition-all">
                                {{ $t('single_entry.no') }}
                            </button>
                        </div>
                    </template>
                </template>
            </div>
        </div>

        <div v-if="!editing" class="px-1">
            <p class="text-[14px] text-[#4A4A4A] font-medium leading-[1.6]">{{ comment.body }}</p>
            <p v-if="deleteError" class="mt-2 text-[11px] font-bold text-[#E07A5F] italic">{{ deleteError }}</p>
        </div>

        <div v-else class="mt-3">
            <textarea
                v-model="editBody"
                rows="3"
                class="w-full px-4 py-3 text-[14px] font-medium text-[#4A4A4A] bg-[#FDFCFB] border-2 border-[#F2E9E4] rounded-2xl focus:outline-none focus:border-[#5A7D5A] resize-none transition-all">
            </textarea>
            <p v-if="updateError" class="mt-1 text-[11px] font-bold text-[#E07A5F] italic">{{ updateError }}</p>
            <div class="flex items-center gap-2 mt-3">
                <button
                    @click="update"
                    :disabled="updateLoading || !editBody.trim()"
                    class="px-5 py-2 text-[11px] font-black uppercase tracking-widest bg-[#5A7D5A] hover:bg-[#4A6D4A] text-white rounded-xl disabled:opacity-50 transition-all">
                    {{ updateLoading ? '...' : t('comments.save') }}
                </button>
                <button
                    @click="cancelEdit"
                    :disabled="updateLoading"
                    class="px-5 py-2 text-[11px] font-black uppercase tracking-widest text-[#A0A0A0] hover:text-[#4A4A4A] transition-all">
                    {{ t('comments.cancel') }}
                </button>
            </div>
        </div>

    </div>
</template>