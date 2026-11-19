<script setup>
import { useI18n } from 'vue-i18n'
import useApi from '@/composables/useApi'
import { useToast } from 'vue-toastification'
import settingsService from '@/services/settingsService'

const { t } = useI18n()
const toast = useToast()

const { data: emailTypes, loading: fetching, error: fetchError } = useApi(
    () => settingsService.getEmailPreferences(), true
)

const { data: res, loading: saving, error, request } = useApi(() =>
    settingsService.updateEmailSettings({
        preferences: emailTypes.value.map(type => ({
            notification_type: type.notification_type,
            mail: Boolean(type.mail)
        }))
    })
)

const submit = async () => {
    if (!emailTypes.value) return
    await request()
    if (!error.value) toast.success(res.value?.message)
}
import PageShell from '@/components/PageShell.vue'
</script>

<template>
    <PageShell :title="t('nav.settings')" narrow>
        <section class="bg-white border border-[#D9D4CE] rounded-3xl p-5 sm:p-6">

            <div class="mb-5">
                <h2 class="font-semibold">{{ t('settings.emailNotifications.title') }}</h2>
                <p class="text-[#5F7480] text-sm mt-1">{{ t('settings.emailNotifications.description') }}</p>
            </div>

            <!-- Fetch loading -->
            <div v-if="fetching" class="space-y-3">
                <div v-for="i in 4" :key="i" class="h-12 bg-[#EFECE8] rounded-2xl animate-pulse" />
            </div>

            <!-- Fetch error -->
            <div v-else-if="fetchError"
                 class="p-4 bg-[#FEE2E2] text-[#991B1B] text-sm font-medium rounded-2xl border border-[#FECACA]">
                {{ t('settings.emailNotifications.fetchError') }}
            </div>

            <form v-else @submit.prevent="submit" class="space-y-1">
                <div
                    v-for="type in emailTypes"
                    :key="type.id"
                    class="flex items-center justify-between gap-4 px-4 py-3 rounded-2xl hover:bg-[#FAF9F7] transition-colors">

                    <span class="text-sm font-medium capitalize">
                        {{ $te(`settings.emailNotifications.types.${type.notification_type}`) ? $t(`settings.emailNotifications.types.${type.notification_type}`) : type.notification_type.replace(/_/g, ' ') }}
                    </span>

                    <button
                        type="button"
                        dir="ltr"
                        @click="type.mail = !type.mail"
                        :class="Boolean(type.mail) ? 'bg-[#083A4F]' : 'bg-[#D9D4CE]'"
                        class="relative inline-flex h-6 w-11 shrink-0 items-center rounded-full transition-colors duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-[#083A4F]/30">
                        <span
                            :class="Boolean(type.mail) ? 'translate-x-6' : 'translate-x-1'"
                            class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-300" />
                    </button>
                </div>

                <div class="pt-4">
                    <button
                        type="submit"
                        :disabled="saving"
                        class="px-6 py-2.5 bg-[#083A4F] text-white rounded-full font-medium text-sm transition-colors hover:bg-[#407E8C] disabled:opacity-50">
                        {{ saving ? t('settings.emailNotifications.saving') : t('settings.emailNotifications.saveButton') }}
                    </button>
                </div>
            </form>
        </section>
    </PageShell>
</template>
