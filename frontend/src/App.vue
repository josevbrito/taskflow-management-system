<template>
  <div id="app" class="flex min-h-screen bg-gray-100 font-sans antialiased">
    <!-- Se não for uma página de autenticação ou erro, exibe o layout com sidebar e header -->
    <template v-if="!isAuthOrErrorPage">
      <!-- Sidebar -->
      <aside class="w-64 bg-gray-800 text-white flex flex-col p-4 shadow-lg rounded-r-lg">
        <div class="mb-8 text-center">
          <h1 class="text-3xl font-bold text-indigo-400">TaskFlow</h1>
          <p class="text-sm text-gray-400 mt-1">Gerenciamento de Tarefas</p>
        </div>
        <nav class="flex-grow">
          <ul>
            <li class="mb-2">
              <router-link to="/" class="flex items-center p-3 rounded-md hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m0 0l-7 7m7-7v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                <span class="font-medium">Dashboard</span>
              </router-link>
            </li>
            <li class="mb-2">
              <router-link to="/projects" class="flex items-center p-3 rounded-md hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m7 0V5a2 2 0 012-2h2a2 2 0 012 2v6m-6 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v6"></path></svg>
                <span class="font-medium">Projetos</span>
              </router-link>
            </li>
            <li class="mb-2">
              <router-link to="/tasks" class="flex items-center p-3 rounded-md hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <span class="font-medium">Tarefas</span>
              </router-link>
            </li>
            <li class="mb-2">
              <router-link to="/profile" class="flex items-center p-3 rounded-md hover:bg-gray-700 transition-colors duration-200">
                <svg class="w-5 h-5 mr-3 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <span class="font-medium">Perfil</span>
              </router-link>
            </li>
          </ul>
        </nav>
        <div class="mt-auto">
          <button @click="logout" class="w-full flex items-center justify-center p-3 rounded-md bg-red-600 hover:bg-red-700 transition-colors duration-200 text-white font-semibold">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H5a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            Sair
          </button>
        </div>
      </aside>

      <!-- Main Content Area -->
      <main class="flex-grow p-6">
        <header class="bg-white shadow rounded-lg p-4 mb-6 flex justify-between items-center">
          <h2 class="text-2xl font-semibold text-gray-800">
            {{ currentRouteTitle }}
          </h2>
          <div class="flex items-center">
            <span class="text-gray-700 mr-2">Bem-vindo, {{ authStore.currentUser?.name || 'Usuário' }}!</span>
            <img class="h-10 w-10 rounded-full object-cover" :src="authStore.currentUser?.avatar || 'https://placehold.co/40x40/cccccc/000000?text=AU'" alt="Avatar do Usuário" />
          </div>
        </header>
        <router-view />
      </main>
    </template>

    <template v-else>
      <router-view />
    </template>
  </div>
</template>

<script setup>
import { computed, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import { useAuthStore } from './stores/auth';

const route = useRoute();
const authStore = useAuthStore();

// Verifica se a rota atual é uma página de autenticação (login, registro) ou de erro (404)
const isAuthOrErrorPage = computed(() => {
  return route.name === 'login' || route.name === 'register' || route.name === 'NotFound';
});

// Computa o título da rota atual para exibir no cabeçalho
const currentRouteTitle = computed(() => {
  switch (route.name) {
    case 'dashboard':
      return 'Dashboard';
    case 'projects':
      return 'Gerenciamento de Projetos';
    case 'tasks':
      return 'Gerenciamento de Tarefas';
    case 'profile':
      return 'Meu Perfil';
    default:
      return 'TaskFlow';
  }
});

onMounted(() => {
  authStore.initializeAuth();
});

const logout = () => {
  authStore.logout();
};
</script>

<style>
html, body {
  margin: 0;
  padding: 0;
  height: 100%;
}
#app {
  height: 100vh;
}
</style>
