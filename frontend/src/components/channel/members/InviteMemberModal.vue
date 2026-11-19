<script setup>
import { ref, watch, inject } from 'vue'
import { useI18n } from 'vue-i18n'
import invitationService from '@/services/invitationService'
import useApi from '@/composables/useApi'

const { t } = useI18n()
const channel = inject('channel')
const emit = defineEmits(['close', 'invited'])

const search = ref('')
const identifier = ref('')

const { data: users, request: usersRequest } = useApi(
    () => invitationService.getUsers({ search: search.value })
)

watch(search, (val) => {
    if (val.trim()) usersRequest()
    else users.value = null
})

const selectUser = (user) => {
    search.value = user.name
    identifier.value = user.name
    users.value = null
}

const { loading, error, request } = useApi(
    () => invitationService.createInvitation({ identifier: identifier.value }, channel.value.id)
)

const submit = async () => {
    await request()
    if (!error.value) {
        emit('close')
        emit('invited')
    }
}
</script>

<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-[#083A4F]/40 backdrop-blur-md">
        <div class="bg-white w-full max-w-md rounded-3xl p-6 border border-white">

            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-semibold text-[#083A4F] tracking-tight">
                    {{ t('invite_modal.title') }}
                </h2>
                <button 
                    @click="emit('close')" 
                    class="h-10 w-10 flex items-center justify-center rounded-full bg-[#FAF9F7] text-[#5F7480] hover:text-[#991B1B] hover:bg-[#FEE2E2] transition-all text-2xl leading-none">
                    &times;
                </button>
            </div>

            <form @submit.prevent="submit" class="relative">
                <div class="relative">
                    <label class="block mb-3 text-xs font-semibold text-[#5F7480] px-2">
                        {{ t('invite_modal.search_label') }}
                    </label>
                    <input
                        type="text"
                        v-model="search"
                        :placeholder="t('invite_modal.search_placeholder')"
                        class="w-full px-6 py-4 bg-[#FAF9F7] border border-transparent rounded-xl text-lg font-medium transition-all focus:outline-none focus:border-[#991B1B] focus:bg-white focus:shadow-inner" />

                    <Transition name="fade">
                        <div v-if="users && users.length > 0"
                            class="absolute z-10 w-full mt-3 bg-white border border-[#FAF9F7] rounded-3xl max-h-56 overflow-hidden">
                            <div class="overflow-y-auto max-h-56 p-2">
                                <button
                                    v-for="user in users"
                                    :key="user.id"
                                    type="button"
                                    @click="selectUser(user)"
                                    class="w-full text-start px-5 py-3 hover:bg-[#FEE2E2] rounded-xl transition group flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl bg-[#F3EEE4] flex items-center justify-center text-[#A58D66] font-semibold">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold block text-[#083A4F] group-hover:text-[#407E8C]">{{ user.name }}</span>
                                        <span class="text-xs font-bold text-[#5F7480]">{{ user.email }}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>

                <p v-if="error?.identifier" class="mt-3 px-4 text-sm text-[#991B1B] font-bold">
                    {{ error.identifier[0] }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mt-10">
                    <button
                        type="button"
                        @click="emit('close')"
                        class="flex-1 px-6 py-3 text-[#5F7480] font-semibold text-sm hover:text-[#083A4F] transition-colors order-2 sm:order-1">
                        {{ t('invite_modal.cancel') }}
                    </button>
                    <button
                        :disabled="loading"
                        class="flex-[2] px-6 py-3 bg-[#083A4F] hover:bg-[#407E8C] disabled:opacity-50 text-white font-semibold rounded-full transition-all active:scale-95 order-1 sm:order-2">
                        {{ loading ? t('invite_modal.sending') : t('invite_modal.send') }}
                    </button>
                </div>
            </form>

        </div>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s, transform 0.2s; }
.fade-enter-from, .fade-leave-to { opacity: 0; transform: translateY(-10px); }
</style>