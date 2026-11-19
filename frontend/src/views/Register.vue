<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import GoogleLogin from '@/components/GoogleLogin.vue';
import AuthShell from '@/components/AuthShell.vue';
const registerForm = ref({
  email: '',
  name: '',
  password: '',
  password_confirmation: ''
});
const errors = ref(null)
const authStore = useAuthStore();

const handleSubmit = async () => {
  errors.value = null
  const result = await authStore.register({
    name: registerForm.value.name,
    email: registerForm.value.email,
    password: registerForm.value.password,
    password_confirmation: registerForm.value.password_confirmation
  })

  if (!result.success && result.errors) {
    errors.value = result.errors
  }
}
</script>

<template>
  <AuthShell :title="$t('register.hero_title')" :text="$t('register.hero_text')">
    <header>
      <h2 class="text-2xl font-semibold tracking-tight">{{ $t("register.create_account") }}</h2>
      <p class="text-sm text-[#3E5660] mt-1">{{ $t('register.tagline') }}</p>
    </header>

    <form @submit.prevent="handleSubmit" class="space-y-4">
      <div>
        <label class="block mb-1.5 text-xs font-medium text-[#083A4F]">{{ $t("register.name") }}</label>
        <input type="text" v-model="registerForm.name" required class="block w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#3E5660]/60 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" :placeholder="$t('register.name_placeholder')">
        <span v-if="errors?.errors?.name" class="block mt-1 text-xs text-[#991B1B]">{{ errors.errors.name[0] }}</span>
      </div>

      <div>
        <label class="block mb-1.5 text-xs font-medium text-[#083A4F]">{{ $t('register.email') }}</label>
        <input type="email" v-model="registerForm.email" required class="block w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#3E5660]/60 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" placeholder="email@example.com">
        <span v-if="errors?.errors?.email" class="block mt-1 text-xs text-[#991B1B]">{{ errors.errors.email[0] }}</span>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="block mb-1.5 text-xs font-medium text-[#083A4F]">{{ $t('register.password') }}</label>
          <input type="password" v-model="registerForm.password" required class="block w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#3E5660]/60 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" placeholder="••••••••">
        </div>
        <div>
          <label class="block mb-1.5 text-xs font-medium text-[#083A4F]">{{ $t("register.confirm_password") }}</label>
          <input type="password" v-model="registerForm.password_confirmation" required class="block w-full rounded-xl border border-[#C0D5D6] bg-white px-4 py-3 text-sm text-[#083A4F] placeholder:text-[#3E5660]/60 focus:outline-none focus:border-[#083A4F] focus:ring-2 focus:ring-[#083A4F]/15" placeholder="••••••••">
        </div>
      </div>
      <span v-if="errors?.errors?.password" class="block mt-1 text-xs text-[#991B1B]">{{ errors.errors.password[0] }}</span>

      <button type="submit" class="w-full py-3 rounded-full bg-[#083A4F] text-white text-sm font-medium hover:bg-[#407E8C] transition-colors active:scale-[0.99]">{{ $t("register.sign_up") }}</button>
    </form>

    <GoogleLogin />
  </AuthShell>
</template>
