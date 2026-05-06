<script setup>
import { ref } from 'vue';
import { useAuthStore } from '@/stores/auth';
import GoogleLogin from '@/components/GoogleLogin.vue';
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
  <div class="min-h-screen flex bg-[#FDFCFB]">
    
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-[#5A7D5A]">
      <div class="absolute inset-0 bg-black/10 z-10"></div>
      <img 
        src="https://images.unsplash.com/photo-1494438639946-1ebd1d20bf85?auto=format&fit=crop&q=80&w=2067" 
        alt="Cozy workspace" 
        class="absolute inset-0 w-full h-full object-cover opacity-80"
      />
      <div class="relative z-20 m-auto text-center p-12">
          <h1 class="text-white text-4xl font-black tracking-tighter mb-4 italic">Organize with intent.</h1>
          <p class="text-white/80 font-medium text-lg">Your shared journey continues here.</p>
      </div>
    </div>

    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12">
      <div class="max-w-[400px] w-full">
        
        <header class="mb-10">
          <h2 class="text-3xl font-black text-[#4A4A4A] tracking-tight">
            {{ $t("login.welcome_back") }}
          </h2>
          <p class="text-[15px] text-[#8E9AAF] font-medium mt-2 italic">
            Ready to log your progress?
          </p>
        </header>

        <form @submit.prevent="handleSubmit" class="space-y-6">
          
          <div v-if="errors?.message" 
               class="p-4 bg-[#FFF2F0] text-[#E07A5F] rounded-2xl border border-[#FFE5E0] text-[13px] font-bold">
            ⚠️ {{ errors.message }}
          </div>

          <div class="space-y-2">
            <label class="block px-1 text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
              {{ $t('login.email') }}
            </label>
            <input 
              type="email" 
              v-model="loginForm.email" 
              required
              class="block w-full py-3.5 px-5 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[14px] font-bold text-[#4A4A4A] transition-all focus:outline-none focus:border-[#5A7D5A] focus:bg-white focus:shadow-xl focus:shadow-[#5A7D5A]/5"
              placeholder="name@example.com"
            >
            <span v-if="errors?.errors?.email" class="block px-2 mt-1 text-[#E07A5F] text-[11px] font-bold italic">
              {{ errors.errors.email[0] }}
            </span>
          </div>

          <div class="space-y-2">
            <label class="block px-1 text-[11px] font-black text-[#A0A0A0] uppercase tracking-[0.2em]">
              {{ $t('login.password') }}
            </label>
            <input 
              type="password" 
              v-model="loginForm.password" 
              required
              class="block w-full py-3.5 px-5 bg-[#F9F7F2] border-2 border-transparent rounded-[1.5rem] text-[14px] font-bold text-[#4A4A4A] transition-all focus:outline-none focus:border-[#5A7D5A] focus:bg-white focus:shadow-xl focus:shadow-[#5A7D5A]/5"
              placeholder="••••••••"
            >
          </div>

          <div class="pt-4">
            <button 
              type="submit"
              class="w-full py-4 bg-[#5A7D5A] hover:bg-[#4A6D4A] text-white rounded-[1.5rem] text-[12px] font-black uppercase tracking-[0.2em] shadow-xl shadow-[#5A7D5A]/20 transition-all active:scale-[0.98]"
            >
              {{ $t('login.login') }}
            </button>
          </div>

          <p class="mt-8 text-center text-[13px] font-bold text-[#A0A0A0]">
            {{ $t('login.dont_have_account') }}
            <router-link 
              :to="{name: 'Register'}"
              class="text-[#D4A373] hover:text-[#5A7D5A] transition-colors ml-1 underline decoration-2 underline-offset-4"
            >
              {{$t('login.sign_up_here')}}
            </router-link>
          </p>
        </form>
        <GoogleLogin />
      </div>
    </div>
  </div>
</template>