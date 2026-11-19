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
import PageShell from '@/components/PageShell.vue'
</script>

<template>
    <PageShell :title="t('create_channel.title')" narrow>
        <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 sm:p-6">
            <p class="text-sm text-[#5F7480] mb-5">{{ t('create_channel.subtitle') }}</p>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block mb-1.5 text-xs font-medium">{{ t('create_channel.name') }}</label>
                    <input type="text" v-model="formData.name" required :placeholder="t('create_channel.name_placeholder')" class="w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#5F7480]/70 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" />
                    <span v-if="error?.name" class="block mt-1 text-xs text-[#991B1B]">{{ error.name[0] }}</span>
                </div>

                <div>
                    <label class="block mb-1.5 text-xs font-medium">{{ t('create_channel.description') }}</label>
                    <textarea v-model="formData.description" required :placeholder="t('create_channel.description_placeholder')" rows="4" class="w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#5F7480]/70 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15 resize-none"></textarea>
                    <span v-if="error?.description" class="block mt-1 text-xs text-[#991B1B]">{{ error.description[0] }}</span>
                </div>

                <div v-if="error?.message" class="p-3 bg-[#FEE2E2] text-[#991B1B] text-sm font-medium rounded-xl border border-[#FECACA]">
                    {{ error.message }}
                </div>

                <div class="pt-2 flex flex-col gap-2">
                    <button type="submit" :disabled="loading" class="w-full py-3 rounded-full bg-[#083A4F] text-white text-sm font-medium transition-colors hover:bg-[#407E8C] disabled:opacity-50">
                        {{ loading ? t('create_channel.loading') : t('create_channel.submit') }}
                    </button>
                    <button type="button" @click="router.back()" class="w-full py-2.5 rounded-full text-sm font-medium text-[#3E5660] hover:bg-[#EFECE8] transition-colors">{{ t('update.cancel') }}</button>
                </div>
            </form>
        </section>
    </PageShell>
</template>
