<script setup>
import { ref, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import { useI18n } from 'vue-i18n'
import { useAuthStore } from '@/stores/auth'
import channelService from '@/services/channelService'
import invitationService from '@/services/invitationService'
import { usePagination } from '@/composables/usePagination'
import useApi from '@/composables/useApi'
import AppPagination from '@/components/AppPagination.vue'

const { t } = useI18n()
const route = useRoute()
const auth = useAuthStore()

// The list is filtered on the server: type=owned for channels you created, type=joined for ones you were invited to.
const filters = ref({ type: 'owned' })

const { items: channels, paginatorData, loading, error, goToPage, refresh } = usePagination(
    (page) => channelService.getChannels({ page, ...filters.value }),
    { immediate: true }
)

watch(filters, () => refresh(), { deep: true })

// The first pending invitation is offered right here, with the same accept/decline calls as the Invitations page.
const { data: invitations, request: loadInvitations } = useApi(() => invitationService.getMyInvitations({}), true)
const invitation = computed(() => (Array.isArray(invitations.value) ? invitations.value[0] : null))

const answer = (action) => useApi(() => invitationService[action](invitation.value.channel_id, invitation.value.id))
const { loading: accepting, request: accept, error: acceptError } = answer('acceptInvitation')
const { loading: declining, request: decline, error: declineError } = answer('declineInvitation')

const respond = async (run, error) => {
    await run()
    if (!error.value) {
        await loadInvitations()
        refresh()
    }
}

// Creating, renaming or deleting a channel happens on other routes, so refresh the list whenever the route changes.
watch(() => route.fullPath, () => refresh())

const total = computed(() => paginatorData.value?.meta?.total ?? channels.value.length)
const isOwner = (channel) => channel.user_id === auth.user?.id
</script>

<template>
    <div class="min-h-screen flex flex-col">

        <header class="bg-white border-b border-[#D9D4CE] px-4 lg:px-8 py-4 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <h1 class="text-2xl lg:text-3xl font-bold tracking-tight">{{ t('channel_list.title') }}</h1>
                <span class="inline-flex px-2.5 py-1 rounded-full bg-[#C0D5D6] text-[#083A4F] text-xs font-semibold tabular-nums">
                    {{ t('channels_page.count_badge', total) }}
                </span>
            </div>

            <router-link
                :to="{ name: 'AddChannel' }"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                {{ t('channel_list.new_channel') }}
            </router-link>
        </header>

        <div class="flex-1 grid grid-cols-1 lg:grid-cols-[300px_1fr]">

            <!-- Channel list -->
            <aside class="bg-white border-b lg:border-b-0 lg:border-e border-[#D9D4CE] p-4 flex flex-col gap-4 min-w-0">

                <section v-if="invitation" class="rounded-2xl bg-[#FEF3C7]/60 border border-[#D97706]/30 p-3.5 flex flex-col gap-2.5">
                    <div class="flex flex-col gap-0.5">
                        <span class="text-xs font-semibold text-[#78350F]">{{ t('channels_page.pending_invitation') }}</span>
                        <p class="text-xs">{{ t('channels_page.invited_you', { name: invitation.invited_by?.name, channel: invitation.channel?.name }) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button type="button" :disabled="accepting || declining" @click="respond(accept, acceptError)"
                                class="px-3.5 py-1.5 rounded-full bg-[#083A4F] text-white text-xs font-medium hover:bg-[#407E8C] transition-colors disabled:opacity-60">
                            {{ t('invitation_card.accept') }}
                        </button>
                        <button type="button" :disabled="accepting || declining" @click="respond(decline, declineError)"
                                class="px-3.5 py-1.5 rounded-full bg-white border border-[#C0D5D6] text-xs font-medium hover:bg-[#EFECE8] transition-colors disabled:opacity-60">
                            {{ t('invitation_card.decline') }}
                        </button>
                    </div>
                </section>

                <!-- Owned / joined switch -->
                <div class="grid grid-cols-2 gap-1 p-1 rounded-full bg-[#E5E1DD]">
                    <button
                        v-for="tab in ['owned', 'joined']"
                        :key="tab"
                        type="button"
                        @click="filters.type = tab"
                        :aria-pressed="filters.type === tab"
                        :class="filters.type === tab ? 'bg-white text-[#083A4F] shadow-sm' : 'text-[#3E5660] hover:text-[#083A4F]'"
                        class="px-3 py-2 rounded-full text-sm font-medium transition-colors">
                        {{ tab === 'owned' ? t('channel_list.my_channels') : t('channel_list.member_of') }}
                    </button>
                </div>

                <div v-if="error" class="p-3 bg-[#FEE2E2] text-[#991B1B] rounded-xl text-sm border border-[#FECACA]">
                    {{ t('channel_list.error') }}: {{ error }}
                </div>

                <div v-else-if="loading && !channels.length" class="flex lg:flex-col gap-2">
                    <div v-for="i in 4" :key="i" class="h-14 w-40 lg:w-auto shrink-0 bg-[#EFECE8] rounded-2xl animate-pulse" />
                </div>

                <div v-else-if="!channels.length" class="p-4 text-center text-sm text-[#5F7480]">
                    <p>{{ filters.type === 'owned' ? t('channel_list.no_owned') : t('channel_list.no_joined') }}</p>
                    <router-link v-if="filters.type === 'owned'" :to="{ name: 'AddChannel' }" class="inline-block mt-2 font-medium text-[#083A4F] underline underline-offset-4">
                        {{ t('channel_list.create_first') }}
                    </router-link>
                </div>

                <template v-else>
                    <!-- A horizontal strip on small screens, a column on large ones -->
                    <div class="flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-1 lg:pb-0">
                        <router-link
                            v-for="channel in channels"
                            :key="channel.id"
                            :to="{ name: 'Channel', params: { id: channel.id } }"
                            class="shrink-0 lg:shrink min-w-[170px] lg:min-w-0 rounded-2xl border border-[#D9D4CE] bg-white p-3.5 flex flex-col gap-0.5 transition-colors hover:bg-[#EFECE8] [&.router-link-active]:bg-[#C0D5D6] [&.router-link-active]:border-[#083A4F]/20">
                            <span class="font-medium truncate">{{ channel.name }}</span>
                            <span class="text-xs text-[#3E5660]">
                                {{ isOwner(channel) ? t('channels_page.role_owner') : t('channels_page.role_member') }}
                                · {{ t('channels_page.members_count', channel.members_count ?? 0) }}
                            </span>
                        </router-link>
                    </div>

                    <AppPagination
                        v-if="paginatorData?.meta"
                        class="hidden lg:flex"
                        :data="paginatorData"
                        @page-change="goToPage" />
                </template>
            </aside>

            <!-- Selected channel -->
            <main class="min-w-0">
                <router-view />
            </main>
        </div>
    </div>
</template>
