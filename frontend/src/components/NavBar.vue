<template>
  <nav class="fixed top-0 left-0 right-0 bg-[#333] px-8 py-4 z-[1000] shadow-md">
    <ol class="flex gap-2 list-none m-0 p-0">
      <li>
        <router-link 
          :to="{ name: 'Home' }"
          class="block text-white no-underline px-4 py-2 rounded bg-transparent text-[0.95rem] transition-colors duration-200 hover:bg-[#555] [&.router-link-active]:bg-[#007bff]"
        >{{ $t("nav.home") }}</router-link>
      </li>
      <li>
        <router-link 
          :to="{ name: 'Add' }"
          class="block text-white no-underline px-4 py-2 rounded bg-transparent text-[0.95rem] transition-colors duration-200 hover:bg-[#555] [&.router-link-active]:bg-[#007bff]"
        >{{ $t("nav.add") }}</router-link>
      </li>      
      <li>
        <router-link 
          :to="{ name: 'About' }"
          class="block text-white no-underline px-4 py-2 rounded bg-transparent text-[0.95rem] transition-colors duration-200 hover:bg-[#555] [&.router-link-active]:bg-[#007bff]"
        >{{ $t("nav.about") }}</router-link>
      </li>     
      <li>
        <button 
          @click="handleLogout"
          class="block text-white px-4 py-2 rounded bg-transparent border-none cursor-pointer text-[0.95rem] transition-colors duration-200 hover:bg-[#555]"
        >{{ $t("nav.sign_out") }}</button>
      </li>
    </ol>
  </nav>
</template>

<script setup>
import { useAuthStore } from '@/stores/auth';

const authStore = useAuthStore();

const handleLogout = async () => {
  await authStore.logout();
};
</script>