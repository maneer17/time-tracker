<script setup>
import { useI18n } from 'vue-i18n'
import invitationService from '@/services/invitationService'
import useApi from '@/composables/useApi'

const { t } = useI18n()

const props = defineProps({
    invitation: { type: Object, required: true },
    mode: { type: String, required: true }
})

const emit = defineEmits(['cancelled', 'accepted', 'declined'])

const { error: cancelError, loading: cancelLoading, request: cancelRequest } = useApi(
    () => invitationService.cancelInvitation(props.invitation.channel_id, props.invitation.id)
)
const { error: acceptError, loading: acceptLoading, request: acceptRequest } = useApi(
    () => invitationService.acceptInvitation(props.invitation.channel_id, props.invitation.id)
)
const { error: declineError, loading: declineLoading, request: declineRequest } = useApi(
    () => invitationService.declineInvitation(props.invitation.channel_id, props.invitation.id)
)

const cancel = async () => {
    await cancelRequest()
    if (!cancelError.value) emit('cancelled', props.invitation.id)
}
const accept = async () => {
    await acceptRequest()
    if (!acceptError.value) emit('accepted', props.invitation.id)
}
const decline = async () => {
    await declineRequest()
    if (!declineError.value) emit('declined', props.invitation.id)
}

const displayUser = props.mode === 'received'
    ? props.invitation.invited_by
    : props.invitation.invited_user
</script>

<template>
    <div class="flex items-center justify-between p-6 bg-white border border-[#FAF9F7] border-dashed rounded-3xl transition-all hover:bg-[#FAF9F7]">

        <div class="flex items-center gap-4">
            <div class="h-10 w-10 rounded-xl bg-[#F3EEE4] flex items-center justify-center text-[#A58D66] font-semibold text-sm">
                {{ displayUser.name.charAt(0) }}
            </div>

            <div class="flex flex-col gap-0.5">
                <span class="font-semibold text-[#083A4F] tracking-tight leading-none">{{ displayUser.name }}</span>
                <span class="text-xs font-semibold text-[#5F7480] mt-1">{{ displayUser.email }}</span>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-1 h-1 rounded-full bg-[#083A4F]"></span>
                    <span class="text-xs font-bold text-[#083A4F]">{{ invitation.channel.name }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button
                v-if="mode === 'sent'"
                @click="cancel"
                :disabled="cancelLoading"
                class="px-4 py-2 text-xs font-semibold text-[#991B1B] hover:bg-[#FEE2E2] rounded-full disabled:opacity-50 transition-all border border-transparent hover:border-[#FECACA]">
                {{ cancelLoading ? '...' : t('invitation_card.cancel') }}
            </button>

            <template v-else-if="mode === 'received'">
                <button
                    @click="accept"
                    :disabled="acceptLoading"
                    class="px-5 py-2 text-xs font-semibold text-white bg-[#083A4F] hover:bg-[#407E8C] rounded-full transition-all active:scale-95 disabled:opacity-50">
                    {{ acceptLoading ? '...' : t('invitation_card.accept') }}
                </button>
                <button
                    @click="decline"
                    :disabled="declineLoading"
                    class="px-4 py-2 text-xs font-semibold text-[#991B1B] hover:bg-[#FEE2E2] rounded-full disabled:opacity-50 transition-all border border-transparent hover:border-[#FECACA]">
                    {{ declineLoading ? '...' : t('invitation_card.decline') }}
                </button>
            </template>
        </div>

    </div>
</template>