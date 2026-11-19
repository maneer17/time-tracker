<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import GoogleLogin from '@/components/GoogleLogin.vue';
import AuthShell from '@/components/AuthShell.vue';
const loginForm = ref({
  email: '',
  password: ''
})

const errors = ref(null)
const authStore = useAuthStore();

const handleSubmit = async () => {
  errors.value = null
  const result = await authStore.login({
    email: loginForm.value.email,
    password: loginForm.value.password
  })

  if (!result.success && result.errors) {
    errors.value = result.errors
  }
}
</script>

<template>
  <AuthShell :title="$t('login.hero_title')" :text="$t('login.hero_text')">
    <header>
      <h2 class="text-2xl font-semibold tracking-tight">{{ $t("login.welcome_back") }}</h2>
      <p class="text-sm text-[#3E5660] mt-1">{{ $t('login.tagline') }}</p>
    </header>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div v-if="errors?.message" class="p-3 bg-[#FEE2E2] text-[#7F1D1D] rounded-xl text-sm font-medium">
        {{ errors.message }}
      </div>

      <div>
        <label class="block mb-1.5 text-xs font-medium text-[#083A4F]">{{ $t('login.email') }}</label>
        <input type="email" v-model="loginForm.email" required class="block w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#3E5660]/60 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" placeholder="name@example.com">
        <span v-if="errors?.errors?.email" class="block mt-1 text-xs text-[#991B1B]">{{ errors.errors.email[0] }}</span>
      </div>

      <div>
        <div class="flex items-center justify-between gap-2 mb-1.5">
          <label class="text-xs font-medium text-[#083A4F]">{{ $t('login.password') }}</label>
          <router-link :to="{name: 'ForgotPassword'}" class="text-xs font-medium text-[#407E8C] hover:underline">
            {{ $t('login.forgot_password') }}
          </router-link>
        </div>
        <input type="password" v-model="loginForm.password" required class="block w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#3E5660]/60 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" placeholder="••••••••">
      </div>

      <button type="submit" class="w-full py-3 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors active:scale-[0.99]">{{ $t('login.login') }}</button>
    </form>

    <GoogleLogin />
  </AuthShell>
</template>
