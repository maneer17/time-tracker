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
</script>

<template>
    <div class="p-6 max-w-md">

        <div class="mb-6">
            <h2 class="text-lg font-bold text-[#2D3436]">
                {{ t('settings.emailNotifications.title') }}
            </h2>
            <p class="text-[#A0A0A0] text-sm mt-1">
                {{ t('settings.emailNotifications.description') }}
            </p>
        </div>

        <!-- Fetch loading -->
        <div v-if="fetching" class="space-y-3">
            <div v-for="i in 4" :key="i"
                class="h-14 bg-[#F9F7F2] rounded-2xl animate-pulse" />
        </div>

        <!-- Fetch error -->
        <div v-else-if="fetchError"
            class="flex items-center gap-3 p-4 bg-[#FFF2F2] text-[#AF4E4E] text-sm font-medium rounded-2xl border border-[#FFDADA]">
            <span>⚠</span> {{ t('settings.emailNotifications.fetchError') }}
        </div>

        <form v-else @submit.prevent="submit" class="space-y-2">

            <div
                v-for="type in emailTypes"
                :key="type.id"
                class="flex items-center justify-between px-5 py-4 rounded-2xl hover:bg-[#F9F7F2] transition-colors">

                <span class="text-sm font-medium text-[#2D3436] capitalize">
                    {{ type.notification_type.replace(/_/g, ' ') }}
                </span>

                <button
                    type="button"
                    dir="ltr"
                    @click="type.mail = !type.mail"
                    :class="Boolean(type.mail) ? 'bg-[#5A7D5A]' : 'bg-gray-200'"
                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-300 focus:outline-none"
                >
                    <span
                        :class="Boolean(type.mail) ? 'translate-x-6' : 'translate-x-1'"
                        class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-300"
                    />
                </button>
            </div>

            <div class="pt-3">
                <button
                    type="submit"
                    :disabled="saving"
                    class="px-6 py-2.5 bg-[#5A7D5A] text-white rounded-xl font-semibold text-sm shadow-lg shadow-[#5A7D5A]/20 transition-all hover:bg-[#4a6b4a] hover:-translate-y-0.5 active:scale-95 disabled:opacity-50">
                    {{ saving ? t('settings.emailNotifications.saving') : t('settings.emailNotifications.saveButton') }}
                </button>
            </div>

        </form>
    </div>
</template>