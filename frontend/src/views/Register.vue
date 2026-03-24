<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
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
  <div class="min-h-screen flex bg-[#FDFCFB]">
    
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-[#D4A373]">
      <div class="absolute inset-0 bg-black/5 z-10"></div>
      <img 
        src="https://images.unsplash.com/photo-1490623970972-ae8bb3da443e?auto=format&fit=crop&q=80&w=2070" 
        alt="Fresh start aesthetic" 
        class="absolute inset-0 w-full h-full object-cover opacity-90"
      />
      <div class="relative z-20 m-auto text-center p-12">
          <h1 class="text-white text-4xl font-black tracking-tighter mb-4 italic">Start something beautiful.</h1>
          <p class="text-white/90 font-medium text-lg">Join a community focused on clarity and growth.</p>
      </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12">
      <div class="max-w-[440px] w-full">
        
        <header class="mb-8">
          <h2 class="text-3xl font-black text-[#4A4A4A] tracking-tight">
            {{ $t("register.create_account") }}
          </h2>
          <p class="text-[15px] text-[#8E9AAF] font-medium mt-2 italic">
            Begin your journey with us today.
          </p>
        </header>

        <form @submit.prevent="handleSubmit" class="space-y-5">
          
          <div class="space-y-1.5">
            <label class="block px-1 text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
              {{ $t("register.name") }}
            </label>
            <input 
              type="text" 
              v-model="registerForm.name" 
              required
              class="block w-full py-3 px-5 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[14px] font-bold text-[#4A4A4A] transition-all focus:outline-none focus:border-[#5A7D5A] focus:bg-white focus:shadow-xl focus:shadow-[#5A7D5A]/5"
              placeholder="Your Name"
            >
            <span v-if="errors?.errors?.name" class="block px-2 text-[#E07A5F] text-[11px] font-bold italic">
              {{ errors.errors.name[0] }}
            </span>
          </div>

          <div class="space-y-1.5">
            <label class="block px-1 text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
              {{ $t('register.email') }}
            </label>
            <input 
              type="email" 
              v-model="registerForm.email" 
              required
              class="block w-full py-3 px-5 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[14px] font-bold text-[#4A4A4A] transition-all focus:outline-none focus:border-[#5A7D5A] focus:bg-white focus:shadow-xl focus:shadow-[#5A7D5A]/5"
              placeholder="email@example.com"
            >
            <span v-if="errors?.errors?.email" class="block px-2 text-[#E07A5F] text-[11px] font-bold italic">
              {{ errors.errors.email[0] }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="space-y-1.5">
              <label class="block px-1 text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
                {{ $t('register.password') }}
              </label>
              <input 
                type="password" 
                v-model="registerForm.password" 
                required
                class="block w-full py-3 px-5 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[14px] font-bold text-[#4A4A4A] transition-all focus:outline-none focus:border-[#5A7D5A] focus:bg-white focus:shadow-xl focus:shadow-[#5A7D5A]/5"
                placeholder="••••••••"
              >
            </div>

            <div class="space-y-1.5">
              <label class="block px-1 text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
                {{ $t("register.confirm_password") }}
              </label>
              <input 
                type="password" 
                v-model="registerForm.password_confirmation" 
                required
                class="block w-full py-3 px-5 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[14px] font-bold text-[#4A4A4A] transition-all focus:outline-none focus:border-[#5A7D5A] focus:bg-white focus:shadow-xl focus:shadow-[#5A7D5A]/5"
                placeholder="••••••••"
              >
            </div>
          </div>
          <span v-if="errors?.errors?.password" class="block px-2 text-[#E07A5F] text-[11px] font-bold italic">
            {{ errors.errors.password[0] }}
          </span>

          <div class="pt-4">
            <button 
              type="submit"
              class="w-full py-4 bg-[#5A7D5A] hover:bg-[#4A6D4A] text-white rounded-[1.5rem] text-[12px] font-black uppercase tracking-[0.2em] shadow-xl shadow-[#5A7D5A]/20 transition-all active:scale-[0.98]"
            >
              {{ $t("register.sign_up") }}
            </button>
          </div>

          <p class="mt-6 text-center text-[13px] font-bold text-[#A0A0A0]">
            {{ $t("register.you_already_have_account") }}
            <router-link 
              :to="{name: 'Login'}"
              class="text-[#D4A373] hover:text-[#5A7D5A] transition-colors ml-1 underline decoration-2 underline-offset-4"
            >
              {{ $t("register.login") }}
            </router-link>
          </p>
        </form>
      </div>
    </div>
  </div>
</template>