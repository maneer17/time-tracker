<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import channelService from '@/services/channelService'

const router = useRouter()
const { t } = useI18n()

const formData = ref({
    name: '',
    description: '',
})

const { loading, error, request } = useApi(() =>
    channelService.createChannel({
        name: formData.value.name,
        description: formData.value.description,
    })
)

const submit = async () => {
    await request()
    if (!error.value) router.push({ name: 'Channels' })
}
</script>

<template>
    <div class="min-h-[80vh] flex items-center justify-center p-6">
        <div class="bg-white rounded-[3.5rem] shadow-[0_30px_60px_rgba(0,0,0,0.04)] p-12 w-full max-w-[550px] border border-white">

            <div class="mb-10 text-center">
                <h2 class="text-3xl font-black text-[#2D3436] tracking-tight">{{ t('create_channel.title') }}</h2>
                <p class="text-[#A0A0A0] text-sm mt-2 font-medium">Set up a new space for your projects</p>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div>
                    <label class="block mb-3 text-xs font-black text-[#A0A0A0] uppercase tracking-[0.2em] px-2">
                        {{ t('create_channel.name') }}
                    </label>
                    <input
                        type="text"
                        v-model="formData.name"
                        required
                        placeholder="e.g. Design Team"
                        class="w-full px-8 py-5 bg-[#F9F7F2] border-2 border-transparent rounded-[2rem] text-lg transition-all focus:outline-none focus:border-[#5A7D5A] focus:bg-white focus:shadow-inner" />
                    <span v-if="error?.name" class="block mt-2 px-4 text-[#AF4E4E] text-xs font-bold">
                        {{ error.name[0] }}
                    </span>
                </div>

                <div>
                    <label class="block mb-3 text-xs font-black text-[#A0A0A0] uppercase tracking-[0.2em] px-2">
                        {{ t('create_channel.description') }}
                    </label>
                    <textarea
                        v-model="formData.description"
                        required
                        placeholder="What's this channel's purpose?"
                        rows="4"
                        class="w-full px-8 py-5 bg-[#F9F7F2] border-2 border-transparent rounded-[2rem] text-lg transition-all focus:outline-none focus:border-[#5A7D5A] focus:bg-white focus:shadow-inner resize-none"></textarea>
                    <span v-if="error?.description" class="block mt-2 px-4 text-[#AF4E4E] text-xs font-bold">
                        {{ error.description[0] }}
                    </span>
                </div>

                <div v-if="error?.message"
                    class="p-5 bg-[#FFF2F2] text-[#AF4E4E] text-sm font-bold rounded-[2rem] border-2 border-[#FFDADA]">
                    {{ error.message }}
                </div>

                <div class="pt-4 flex flex-col gap-4">
                    <button
                        type="submit"
                        :disabled="loading"
                        class="w-full py-5 bg-[#5A7D5A] text-white rounded-[2rem] font-black text-xl shadow-xl shadow-[#5A7D5A]/20 transition-all hover:bg-[#4a6b4a] hover:-translate-y-1 active:scale-95 disabled:opacity-50">
                        {{ loading ? t('create_channel.loading') : t('create_channel.submit') }}
                    </button>
                    
                    <button 
                        type="button"
                        @click="router.back()"
                        class="w-full py-4 text-[#8E9AAF] font-bold text-sm hover:text-[#2D3436] transition-colors">
                        {{ t('update.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>