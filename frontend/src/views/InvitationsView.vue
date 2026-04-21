<script setup>
import { useI18n } from 'vue-i18n'
import invitationService from '@/services/invitationService'
import useApi from '@/composables/useApi'
import InvitationCard from '@/components/invitations/InvitationCard.vue'

const { t } = useI18n()
const { data: invitations, error, request: refresh } = useApi(
    () => invitationService.getMyInvitations(), true
)
</script>

<template>
    <div class="max-w-2xl mx-auto px-6 py-8">

        <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ t('invitations_view.title') }}</h1>

        <div v-if="error" class="p-4 bg-red-50 text-red-600 rounded-lg border border-red-200">
            {{ t('invitations_view.error') }}
        </div>

        <div v-else-if="invitations">
            <div v-if="invitations.length > 0" class="space-y-3">
                <InvitationCard
                    v-for="invitation in invitations"
                    :key="invitation.id"
                    :invitation="invitation"
                    mode="received"
                    @accepted="refresh"
                    @declined="refresh" />
            </div>
            <div v-else class="text-center py-16 text-gray-400">
                {{ t('invitations_view.empty') }}
            </div>
        </div>

        <div v-else class="text-center py-16 text-gray-400">
            {{ t('invitations_view.loading') }}
        </div>

    </div>
</template>