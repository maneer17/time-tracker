<script setup>
import {ref} from 'vue';
import { useAuthStore } from '@/stores/auth';
const loginForm = ref({
  email: '',
  password: ''
})

const errors = ref(null)
const authStore = useAuthStore();

const handleSubmit = async ()=>{
    errors.value = null
    const result = await authStore.login({
    email: loginForm.value.email,
    password: loginForm.value.password
  })

    if(!result.success && result.errors){
        errors.value = result.errors
    }
}
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-[#f5f5f5] p-8">
    <form 
      @submit.prevent="handleSubmit"
      class="max-w-[420px] w-full bg-white p-10 rounded-xl shadow-md"
    >
      <h2 class="mt-0 mb-8 text-[#333] text-[1.8rem] font-semibold text-center">
        {{ $t("login.welcome_back") }}
      </h2>

      <div v-if="errors?.message" class="p-3 mb-6 bg-[#ffeeee] text-[#cc3333] rounded-md border border-[#ffcccc] text-[0.9rem]">
        {{ errors.message }}
      </div>

      <div class="mb-6">
        <label class="block mb-2 text-[#888] text-[0.75rem] uppercase tracking-widest font-semibold">
          {{ $t('login.email') }}
        </label>
        <input 
          type="email" 
          v-model="loginForm.email" 
          required
          class="block w-full py-3 px-2 border-0 border-b-2 border-[#e0e0e0] text-[#555] text-base transition-colors duration-300 bg-transparent focus:outline-none focus:border-b-[#007bff]"
        >
        <span v-if="errors?.errors?.email" class="block mt-2 text-[#dc3545] text-[0.85rem]">
          {{ errors.errors.email[0] }}
        </span>
      </div>

      <div class="mb-6">
        <label class="block mb-2 text-[#888] text-[0.75rem] uppercase tracking-widest font-semibold">
          {{ $t('login.password') }}
        </label>
        <input 
          type="password" 
          v-model="loginForm.password" 
          required
          class="block w-full py-3 px-2 border-0 border-b-2 border-[#e0e0e0] text-[#555] text-base transition-colors duration-300 bg-transparent focus:outline-none focus:border-b-[#007bff]"
        >
      </div>

      <button 
        type="submit"
        class="w-full py-3.5 mt-4 bg-[#007bff] text-white border-none rounded-md text-base font-medium cursor-pointer transition-colors duration-200 uppercase tracking-wide hover:bg-[#0056b3] active:translate-y-px"
      >{{ $t('login.login') }}</button>

      <p class="mt-6 text-center text-[#666] text-[0.9rem]">
        {{ $t('login.dont_have_account') }}
        <router-link 
          :to="{name: 'Register'}"
          class="text-[#007bff] no-underline font-medium hover:underline"
        >{{$t('login.sign_up_here')}}</router-link>
      </p>
    </form>
  </div>
</template>