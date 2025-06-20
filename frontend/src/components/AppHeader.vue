<template>
  <header class="bg-gray-900 text-white p-4 shadow-lg flex items-center justify-between z-10 relative">
    <div class="flex items-center">
      <router-link to="/" class="text-3xl font-extrabold text-indigo-400 mr-8 hover:text-indigo-200 transition-colors duration-300 tracking-wide cursor-pointer">
        TaskFlow
      </router-link>
      <nav class="hidden md:block"> <ul class="flex space-x-6">
          <li>
            <router-link to="/" class="flex items-center text-gray-300 px-3 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-all duration-200 text-base font-medium">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
              <span>Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/projects" class="flex items-center text-gray-300 px-3 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-all duration-200 text-base font-medium">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m7 0V5a2 2 0 012-2h2a2 2 0 012 2v6m-6 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v6"></path></svg>
              <span>Projetos</span>
            </router-link>
          </li>
          <li>
            <router-link to="/tasks" class="flex items-center text-gray-300 px-3 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-all duration-200 text-base font-medium">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
              <span>Tarefas</span>
            </router-link>
          </li>
          <li v-if="authStore.currentUser && (authStore.currentUser.role === 'admin' || authStore.currentUser.role === 'manager')">
            <router-link to="/users" class="flex items-center text-gray-300 px-3 py-2 rounded-md hover:bg-gray-700 hover:text-white transition-all duration-200 text-base font-medium">
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A4 4 0 018 16h8a4 4 0 012.879 1.804M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
              <span>Usuários</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>


    <div class="flex items-center space-x-4">
      <router-link to="/profile" class="flex items-center space-x-2 cursor-pointer group p-1 -m-1 rounded-full hover:bg-gray-700 transition-colors duration-200">
        <span class="text-gray-300 hidden md:inline group-hover:text-white transition-colors duration-200 text-base">Olá, {{ currentUser?.name || 'Usuário' }}!</span>
        
        <img v-if="currentUser?.avatar" class="h-10 w-10 rounded-full object-cover border-2 border-indigo-400 group-hover:border-white transition-colors duration-200" :src="currentUser.avatar" alt="Avatar do Usuário" />
        <div v-else class="h-10 w-10 rounded-full bg-gray-700 text-white flex items-center justify-center border-2 border-indigo-400 group-hover:border-white transition-colors duration-200">
          <svg class="h-6 w-6 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
        </div>
      </router-link>
      <button @click="handleLogout" class="flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-3 rounded-md transition-colors duration-200 text-sm">
        <svg class="w-4 h-4 mr-1 hidden sm:inline" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H5a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        Sair
      </button>
    </div>
  </header>
</template>

<script setup>
import { computed } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();

const currentUser = computed(() => authStore.currentUser);

const handleLogout = () => {
  authStore.logout();
};
</script>

<style scoped>
.router-link-active {
  color: white;
  font-weight: 600;
  background-color: theme('colors.gray.700');
}
.router-link-active svg {
  color: theme('colors.indigo.200');
}
</style>
