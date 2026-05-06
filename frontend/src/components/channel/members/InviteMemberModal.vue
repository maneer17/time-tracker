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
    <div class="fixed inset-0 z-50 flex items-center justify-center p-6 bg-[#2D3436]/40 backdrop-blur-md">
        <div class="bg-white w-full max-w-md rounded-[3rem] shadow-[0_30px_70px_rgba(0,0,0,0.15)] p-10 border border-white">

            <div class="flex justify-between items-center mb-8">
                <h2 class="text-2xl font-black text-[#4A4A4A] tracking-tight">
                    {{ t('invite_modal.title') }}
                </h2>
                <button 
                    @click="emit('close')" 
                    class="h-10 w-10 flex items-center justify-center rounded-full bg-[#F9F7F2] text-[#A0A0A0] hover:text-[#E07A5F] hover:bg-[#FFF2F0] transition-all text-2xl leading-none">
                    &times;
                </button>
            </div>

            <form @submit.prevent="submit" class="relative">
                <div class="relative">
                    <label class="block mb-3 text-xs font-black text-[#A0A0A0] uppercase tracking-[0.2em] px-2">
                        {{ t('invite_modal.search_label') || 'Find Member' }}
                    </label>
                    <input
                        type="text"
                        v-model="search"
                        :placeholder="t('invite_modal.search_placeholder')"
                        class="w-full px-6 py-4 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-lg font-medium transition-all focus:outline-none focus:border-[#E07A5F] focus:bg-white focus:shadow-inner" />

                    <Transition name="fade">
                        <div v-if="users && users.length > 0"
                            class="absolute z-10 w-full mt-3 bg-white border-2 border-[#F9F7F2] rounded-[2rem] shadow-2xl max-h-56 overflow-hidden">
                            <div class="overflow-y-auto max-h-56 p-2">
                                <button
                                    v-for="user in users"
                                    :key="user.id"
                                    type="button"
                                    @click="selectUser(user)"
                                    class="w-full text-left px-5 py-4 hover:bg-[#FFF2F0] rounded-[1.2rem] transition group flex items-center gap-4">
                                    <div class="h-10 w-10 rounded-xl bg-[#F2E9E4] flex items-center justify-center text-[#D4A373] font-black">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div>
                                        <span class="font-black block text-[#4A4A4A] group-hover:text-[#E07A5F]">{{ user.name }}</span>
                                        <span class="text-xs font-bold text-[#A0A0A0] uppercase tracking-tighter">{{ user.email }}</span>
                                    </div>
                                </button>
                            </div>
                        </div>
                    </Transition>
                </div>

                <p v-if="error?.identifier" class="mt-3 px-4 text-sm text-[#AF4E4E] font-bold">
                    {{ error.identifier[0] }}
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mt-10">
                    <button
                        type="button"
                        @click="emit('close')"
                        class="flex-1 px-6 py-4 text-[#A0A0A0] font-black text-sm hover:text-[#4A4A4A] transition-colors order-2 sm:order-1">
                        {{ t('invite_modal.cancel') }}
                    </button>
                    <button
                        :disabled="loading"
                        class="flex-[2] px-6 py-4 bg-[#E07A5F] hover:bg-[#D6684D] disabled:opacity-50 text-white font-black rounded-[1.5rem] transition-all shadow-xl shadow-[#E07A5F]/20 active:scale-95 order-1 sm:order-2">
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