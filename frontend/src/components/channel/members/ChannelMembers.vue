<script setup>
import { onMounted, ref, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import InviteMemberModal from './InviteMemberModal.vue'
import PendingInvitationsList from './PendingInvitationsList.vue'
import invitationService from '@/services/invitationService'
import useApi from '@/composables/useApi'

const { t } = useI18n()
const isOwner = inject('isOwner')
const channel = inject('channel')
const showInviteModal = ref(false)

const { data: invitations, request: refreshInvitations } = useApi(
    () => invitationService.getChanelInvitations(channel.value.id)
)

onMounted(() => {
    if (isOwner.value) refreshInvitations()
})
</script>

<template>
    <div class="max-w-6xl mx-auto p-2">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-12 gap-6">
            <div>
                <h2 class="text-3xl font-black text-[#4A4A4A] tracking-tight">
                    {{ t('channel_members.community') }}
                </h2>
                <p class="text-[#8E9AAF] font-medium mt-1 italic">{{ t('channel_members.manage_members') }}</p>
            </div>
            
            <button
                v-if="isOwner"
                @click="showInviteModal = true"
                class="px-8 py-4 bg-[#E07A5F] hover:bg-[#D6684D] text-white text-sm font-black rounded-[1.5rem] transition-all shadow-xl shadow-[#E07A5F]/20 active:scale-95 flex items-center gap-2">
                <span class="text-xl leading-none">+</span>
                {{ t('channel_members.invite_member') }}
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            <section class="space-y-6">
                <div class="flex items-center gap-3 mb-2 px-2">
                    <h3 class="text-xs font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
                        {{ t('channel_members.active_members') }}
                    </h3>
                    <span class="px-3 py-1 bg-[#F9F7F2] text-[#D4A373] text-xs font-black rounded-full border border-[#F2E9E4]">
                        {{ channel.members?.length || 0 }}
                    </span>
                </div>

                <div class="grid gap-4">
                    <div
                        v-for="member in channel.members"
                        :key="member.id"
                        class="flex items-center p-5 bg-white border-2 border-[#F9F7F2] rounded-[2rem] hover:border-[#D4A373]/30 hover:shadow-lg hover:shadow-[#D4A373]/5 transition-all group">
                        
                        <div class="h-12 w-12 rounded-[1.2rem] bg-[#F2E9E4] flex items-center justify-center text-[#D4A373] font-black text-lg mr-5 border border-[#F2E9E4] transition-transform group-hover:rotate-6">
                            {{ member.user.name.charAt(0) }}
                        </div>
                        
                        <div>
                            <h3 class="font-black text-[#4A4A4A] leading-none group-hover:text-[#D4A373] transition-colors">
                                {{ member.user.name }}
                            </h3>
                            <p class="text-xs text-[#A0A0A0] font-bold mt-2 uppercase tracking-tighter">{{ member.user.email }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section v-if="isOwner" class="space-y-6">
                <div class="flex items-center gap-3 mb-2 px-2">
                    <h3 class="text-xs font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
                        {{ t('channel_members.pending_invitations') }}
                    </h3>
                    <span v-if="invitations?.length"
                        class="px-3 py-1 bg-[#FFF2F0] text-[#E07A5F] text-xs font-black rounded-full border border-[#FFE5E0]">
                        {{ invitations.length }}
                    </span>
                </div>
                
                <div class="bg-[#FDFCFB] border-2 border-[#F9F7F2] rounded-[2.5rem] p-4">
                    <PendingInvitationsList
                        :invitations="invitations"
                        @cancelled="refreshInvitations" />
                </div>
            </section>

        </div>

        <InviteMemberModal
            v-if="showInviteModal && isOwner"
            @close="showInviteModal = false"
            @invited="refreshInvitations" />
    </div>
</template>