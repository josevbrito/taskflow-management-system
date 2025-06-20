<template>
  <header class="bg-gray-800 text-white p-4 shadow-lg flex items-center justify-between z-10 relative">
    <div class="flex items-center">
      <h1 class="text-3xl font-bold text-indigo-400 mr-8">TaskFlow</h1>
      <nav>
        <ul class="flex space-x-6">
          <li>
            <router-link to="/" class="flex items-center text-gray-300 hover:text-white transition-colors duration-200">
              <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
              <span class="font-medium hidden sm:inline">Dashboard</span>
            </router-link>
          </li>
          <li>
            <router-link to="/projects" class="flex items-center text-gray-300 hover:text-white transition-colors duration-200">
              <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m7 0V5a2 2 0 012-2h2a2 2 0 012 2v6m-6 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v6"></path></svg>
              <span class="font-medium hidden sm:inline">Projetos</span>
            </router-link>
          </li>
          <li>
            <router-link to="/tasks" class="flex items-center text-gray-300 hover:text-white transition-colors duration-200">
              <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
              <span class="font-medium hidden sm:inline">Tarefas</span>
            </router-link>
          </li>
          <li>
            <router-link to="/profile" class="flex items-center text-gray-300 hover:text-white transition-colors duration-200">
              <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              <span class="font-medium hidden sm:inline">Perfil</span>
            </router-link>
          </li>
          <li v-if="authStore.currentUser && authStore.currentUser.role === 'admin'">
            <router-link to="/admin-panel" class="flex items-center text-gray-300 hover:text-white transition-colors duration-200">
              <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
              <span class="font-medium hidden sm:inline">Painel Admin</span>
            </router-link>
          </li>
        </ul>
      </nav>
    </div>

    <!-- User Name and Logout -->
    <div class="flex items-center space-x-4">
      <span class="text-gray-300 hidden md:inline">Olá, {{ currentUser?.name || 'Utilizador' }}!</span>
      <img class="h-10 w-10 rounded-full object-cover border-2 border-indigo-400" :src="currentUser?.avatar || 'https://placehold.co/40x40/cccccc/000000?text=AU'" alt="Avatar do Utilizador" />
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
  font-weight: 700;
}
</style>
