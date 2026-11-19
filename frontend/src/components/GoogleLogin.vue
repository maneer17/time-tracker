<script setup>
import { googleTokenLogin } from 'vue3-google-login'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// So we can authnticate with google using 3 methods and each one returns a specific thing
// with specific options according to google developer docs  : 
// 1 googleLogin -> returns JWT which we can decode on the backend 
// and get user data (then we won't use socialite but a rather manual acess to google) like this to verify the JWT: Http::get('https://oauth2.googleapis
// 2 googleTokenLogin -> returns access token 
// [we can get use of socialite here because it accepts a token and can return a user from it without an extra route (redirect)]
// 3 googleAuthCodeLogin()  this one for advanced cases where we want to be able to access the user drive for example 
// there are options like auto login, propmt ... that google allows for us in case we used the JWT method (first one)
// https://devbaji.github.io/vue3-google-login/guide/custom-button.html#googleauthcodelogin-function
// https://developers.google.com/identity/oauth2/web/guides/use-token-model
const handleGoogleLogin = async () => {
    const response = await googleTokenLogin()
    await authStore.googleLogin(response.access_token)
}
</script>

<template>
  <div>

    <div class="relative flex items-center justify-center mb-4">
      <div class="absolute inset-0 flex items-center">
        <div class="w-full border-t border-[#D9D4CE]"></div>
      </div>
      <span class="relative bg-[#F9EBD8] px-3 text-xs text-[#3E5660]">
        {{ $t('login.or_continue_with') }}
      </span>
    </div>

    <button
      @click="handleGoogleLogin"
      type="button"
      class="w-full flex items-center justify-center gap-3 py-3 px-5 bg-white border border-[#C0D5D6] rounded-full text-sm font-medium text-[#083A4F] transition-colors hover:bg-[#FAF9F7]"
    >
      <!-- Google SVG icon -->
      <svg width="18" height="18" viewBox="0 0 48 48">
        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
        <path fill="none" d="M0 0h48v48H0z"/>
      </svg>
      {{ $t('login.google_sign_in') }}
    </button>

  </div>
</template>