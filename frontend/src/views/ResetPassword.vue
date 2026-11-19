<script setup>
import { ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import useApi from '@/composables/useApi'
import PasswordService from '@/services/PasswordService'
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

const route = useRoute()
const router = useRouter()

const email = route.query.email
const token = route.query.token

const password = ref('')
const password_confirmation = ref('')
const submitted = ref(false)

const { data: message, error, request } = useApi(
    () =>
        PasswordService.resetPassword({
            email,
            token,
            password: password.value,
            password_confirmation: password_confirmation.value,
        }),
    false
)

const handleSubmit = async () => {
    await request()

    if (!error.value) {
        submitted.value = true
        setTimeout(() => router.push('/login'), 2000)
    }
}
</script>

<template>
    <div class="min-h-screen flex items-center justify-center paper-bg px-4">
        <div class="w-full max-w-md p-8">

            <Transition name="fade" mode="out-in">

                <div v-if="submitted" class="text-center space-y-4 py-4">
                    <div class="w-14 h-14 bg-[#C0D5D6] rounded-full flex items-center justify-center mx-auto">
                        <svg class="w-7 h-7 text-[#083A4F]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>

                    <h2 class="text-2xl font-semibold tracking-tight text-[#083A4F]">
                        {{ t('passwords.resetPassword.passwordReset') }}
                    </h2>

                    <p class="text-sm text-[#3E5660]">
                        {{ message.message }}
                    </p>

                    <p class="text-xs text-[#5F7480]">
                        {{ t('passwords.resetPassword.redirecting') }}
                    </p>
                </div>

                <form v-else @submit.prevent="handleSubmit" class="space-y-6">
                    <div class="text-center">
                        <h2 class="text-2xl font-semibold tracking-tight text-[#083A4F]">
                            {{ t('passwords.resetPassword.setNewPassword') }}
                        </h2>

                        <p class="text-sm text-[#3E5660] mt-2">
                            {{ t('passwords.resetPassword.enterNewPassword') }}
                        </p>
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1.5">
                            {{ t('passwords.resetPassword.newPassword') }}
                        </label>

                        <input
                            v-model="password"
                            type="password"
                            :placeholder="t('passwords.resetPassword.newPasswordPlaceholder')"
                            class="w-full px-4 py-3 text-sm border border-[#C0D5D6] rounded-xl focus:ring-2 focus:ring-[#083A4F]/15 focus:border-[#083A4F] outline-none transition-colors"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-medium mb-1.5">
                            {{ t('passwords.resetPassword.confirmPassword') }}
                        </label>

                        <input
                            v-model="password_confirmation"
                            type="password"
                            :placeholder="t('passwords.resetPassword.confirmPasswordPlaceholder')"
                            class="w-full px-4 py-3 text-sm border border-[#C0D5D6] rounded-xl focus:ring-2 focus:ring-[#083A4F]/15 focus:border-[#083A4F] outline-none transition-colors"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="!password || !password_confirmation"
                        class="w-full bg-[#083A4F] hover:bg-[#407E8C] disabled:opacity-60 disabled:cursor-not-allowed text-white text-sm font-medium py-3 px-4 rounded-full transition-colors"
                    >
                        {{ t('passwords.resetPassword.resetPasswordButton') }}
                    </button>

                    <div
                        v-if="error"
                        class="text-sm text-[#991B1B] bg-[#FEE2E2] p-4 rounded-xl border border-[#FECACA] flex items-center gap-2"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 9v2m0 4h.01M12 3a9 9 0 100 18A9 9 0 0012 3z"
                            />
                        </svg>
                        {{ error }}
                    </div>
                </form>

            </Transition>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}

.fade-enter-from {
    opacity: 0;
    transform: translateY(8px);
}

.fade-leave-to {
    opacity: 0;
    transform: translateY(-8px);
}
</style>

