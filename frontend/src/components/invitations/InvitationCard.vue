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
    <div class="flex items-center justify-between p-6 bg-white border-2 border-[#F9F7F2] border-dashed rounded-[2rem] transition-all hover:bg-[#FDFCFB]">

        <div class="flex items-center gap-4">
            <div class="h-10 w-10 rounded-xl bg-[#F2E9E4] flex items-center justify-center text-[#D4A373] font-black text-sm uppercase">
                {{ displayUser.name.charAt(0) }}
            </div>

            <div class="flex flex-col gap-0.5">
                <span class="font-black text-[#4A4A4A] tracking-tight leading-none">{{ displayUser.name }}</span>
                <span class="text-[10px] font-black text-[#A0A0A0] uppercase tracking-widest mt-1">{{ displayUser.email }}</span>
                <div class="flex items-center gap-1.5 mt-2">
                    <span class="w-1 h-1 rounded-full bg-[#5A7D5A]"></span>
                    <span class="text-[10px] font-bold text-[#5A7D5A] uppercase tracking-tighter">{{ invitation.channel.name }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <button
                v-if="mode === 'sent'"
                @click="cancel"
                :disabled="cancelLoading"
                class="px-4 py-2 text-[10px] font-black text-[#E07A5F] hover:bg-[#FFF2F0] rounded-full uppercase tracking-widest disabled:opacity-50 transition-all border border-transparent hover:border-[#FFE5E0]">
                {{ cancelLoading ? '...' : t('invitation_card.cancel') }}
            </button>

            <template v-else-if="mode === 'received'">
                <button
                    @click="accept"
                    :disabled="acceptLoading"
                    class="px-5 py-2 text-[10px] font-black text-white bg-[#5A7D5A] hover:bg-[#4a6b4a] rounded-full uppercase tracking-widest shadow-lg shadow-[#5A7D5A]/20 transition-all active:scale-95 disabled:opacity-50">
                    {{ acceptLoading ? '...' : t('invitation_card.accept') }}
                </button>
                <button
                    @click="decline"
                    :disabled="declineLoading"
                    class="px-4 py-2 text-[10px] font-black text-[#E07A5F] hover:bg-[#FFF2F0] rounded-full uppercase tracking-widest disabled:opacity-50 transition-all border border-transparent hover:border-[#FFE5E0]">
                    {{ declineLoading ? '...' : t('invitation_card.decline') }}
                </button>
            </template>
        </div>

    </div>
</template>