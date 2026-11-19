<script setup>
import { useI18n } from 'vue-i18n'
import invitationService from '@/services/invitationService'
import { usePagination } from '@/composables/usePagination'
import AppPagination from '@/components/AppPagination.vue'
import InvitationCard from '@/components/invitations/InvitationCard.vue'

const { t } = useI18n()
const { items: invitations, paginatorData, loading, error, goToPage, refresh } = usePagination(
    (page) => invitationService.getMyInvitations({page}),
    { immediate: true }
)

import PageShell from '@/components/PageShell.vue'
</script>

<template>
    <PageShell :title="t('invitations_view.title')" narrow>

        <div v-if="error" class="p-4 bg-[#FEE2E2] text-[#991B1B] rounded-3xl border border-[#FECACA] text-sm font-medium">
            {{ t('invitations_view.error') }}
        </div>

        <div v-else-if="invitations" class="space-y-4">
            <div v-if="invitations.length > 0" class="space-y-3">
                <InvitationCard
                    v-for="invitation in invitations"
                    :key="invitation.id"
                    :invitation="invitation"
                    mode="received"
                    @accepted="refresh"
                    @declined="refresh" />
            </div>

            <div v-else class="py-16 bg-white border border-dashed border-[#D9D4CE] rounded-3xl text-center text-sm text-[#5F7480] px-6">
                {{ t('invitations_view.empty') }}
            </div>

            <AppPagination
                v-if="invitations.length > 0 && paginatorData?.meta"
                :data="paginatorData"
                @page-change="goToPage" />
        </div>

        <div v-else class="text-center py-16 text-[#5F7480]">
            {{ t('invitations_view.loading') }}
        </div>

    </PageShell>
</template>
