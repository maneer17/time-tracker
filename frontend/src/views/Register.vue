<script setup>
import {ref} from 'vue';
import { useAuthStore } from '@/stores/auth';
import router from '@/router'

const email = ref('');
const name = ref('')
const password = ref('')
const password_confirmation = ref('')
const errors = ref(null)
const authStore = useAuthStore();

const handleSubmit = async ()=>{
    errors.value = null
    const result = await authStore.register({
    name: name.value,   
    email: email.value,
    password: password.value,
    password_confirmation: password_confirmation.value
  })

    if(!result.success && result.errors){
        errors.value = result.errors
    }
}
</script>

<template>
  <div class="page">
    <form @submit.prevent="handleSubmit">
      <h2>{{ $t("register.create_account") }}</h2>
      
      <div class="field">
        <label>{{ $t("register.name") }}</label>
        <input type="text" v-model="name" required>
        <span v-if="errors?.errors?.name" class="error">
          {{ errors.errors.name[0] }}
        </span>
      </div>

      <div class="field">
        <label>{{ $t("register.email") }}</label>
        <input type="email" v-model="email" required>
        <span v-if="errors?.errors?.email" class="error">
          {{ errors.errors.email[0] }}
        </span>
      </div>

      <div class="field">
        <label>{{ $t("register.password") }}</label>
        <input type="password" v-model="password" required>
        <span v-if="errors?.errors?.password" class="error">
          {{ errors.errors.password[0] }}
        </span>
      </div>

      <div class="field">
        <label>{{ $t("register.confirm_password") }}</label>
        <input type="password" v-model="password_confirmation" required>
      </div>

      <button type="submit" class="btn">{{ $t("register.sign_up") }}</button>
    </form>
  </div>
    <p class="login">
        {{ $t("register.you_already_have_account") }}
        <router-link :to="{name: 'Login'}">{{ $t("register.login") }}</router-link>
    </p>
</template>

<style scoped>
.page {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #f5f5f5;
  padding: 2rem;
}

form {
  max-width: 420px;
  width: 100%;
  background: white;
  padding: 2.5rem;
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

h2 {
  margin: 0 0 2rem 0;
  color: #333;
  font-size: 1.8rem;
  font-weight: 600;
  text-align: center;
}

.field {
  margin-bottom: 1.5rem;
}

label {
  display: block;
  margin-bottom: 0.5rem;
  color: #888;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 1px;
  font-weight: 600;
}

input {
  display: block;
  width: 100%;
  padding: 0.75rem 0.5rem;
  border: none;
  border-bottom: 2px solid #e0e0e0;
  color: #555;
  font-size: 1rem;
  transition: border-color 0.3s;
  background: transparent;
}

input:focus {
  outline: none;
  border-bottom-color: #007bff;
}

.error {
  display: block;
  margin-top: 0.5rem;
  color: #dc3545;
  font-size: 0.85rem;
}

.btn {
  width: 100%;
  padding: 0.9rem;
  margin-top: 1rem;
  background: #007bff;
  color: white;
  border: none;
  border-radius: 6px;
  font-size: 1rem;
  font-weight: 500;
  cursor: pointer;
  transition: background 0.2s;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.btn:hover {
  background: #0056b3;
}

.btn:active {
  transform: translateY(1px);
}
.login {
  margin-top: 1.5rem;
  text-align: center;
  color: #666;
  font-size: 0.9rem;
}

.login a {
  color: #007bff;
  text-decoration: none;
  font-weight: 500;
}

.login a:hover {
  text-decoration: underline;
}
</style>