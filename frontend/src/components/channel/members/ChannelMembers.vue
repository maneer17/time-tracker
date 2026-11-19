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
    <div class="space-y-4">

        <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="font-semibold">{{ t('channel_members.community') }}</h2>
                <p class="text-sm text-[#5F7480] mt-0.5">{{ t('channel_members.manage_members') }}</p>
            </div>

            <button
                v-if="isOwner"
                @click="showInviteModal = true"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#083A4F] hover:bg-[#407E8C] text-white text-sm font-medium rounded-full transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                {{ t('channel_members.invite_member') }}
            </button>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4 items-start">

            <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold">{{ t('channel_members.active_members') }}</h3>
                    <span class="px-2.5 py-0.5 bg-[#C0D5D6] text-[#083A4F] text-xs font-semibold rounded-full tabular-nums">
                        {{ channel.members?.length || 0 }}
                    </span>
                </div>

                <div class="divide-y divide-[#D9D4CE]">
                    <div
                        v-for="member in channel.members"
                        :key="member.id"
                        class="flex items-center gap-3 py-3">
                        <div class="h-10 w-10 rounded-full bg-[#C0D5D6] flex items-center justify-center text-[#083A4F] font-semibold shrink-0">
                            {{ member.user.name.charAt(0) }}
                        </div>
                        <div class="min-w-0">
                            <p class="font-medium truncate">{{ member.user.name }}</p>
                            <p class="text-xs text-[#5F7480] truncate">{{ member.user.email }}</p>
                        </div>
                    </div>
                </div>
            </section>

            <section v-if="isOwner" class="bg-white border border-[#D9D4CE] rounded-3xl p-5 space-y-3">
                <div class="flex items-center gap-2">
                    <h3 class="font-semibold">{{ t('channel_members.pending_invitations') }}</h3>
                    <span v-if="invitations?.length"
                        class="px-2.5 py-0.5 bg-[#FEE2E2] text-[#991B1B] text-xs font-semibold rounded-full tabular-nums">
                        {{ invitations.length }}
                    </span>
                </div>

                <PendingInvitationsList
                    :invitations="invitations"
                    @cancelled="refreshInvitations" />
            </section>

        </div>

        <InviteMemberModal
            v-if="showInviteModal && isOwner"
            @close="showInviteModal = false"
            @invited="refreshInvitations" />
    </div>
</template>
