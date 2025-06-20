<template>
  <div id="app" class="flex flex-col min-h-screen bg-gray-100 font-sans antialiased">
    <!-- Visível apenas em páginas autenticadas e não-erro -->
    <template v-if="!isAuthOrErrorPage">
      <AppHeader />
      <!-- Main Content Area -->
      <main class="flex-grow p-6">
        <router-view />
      </main>
      <AppFooter />
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
import AppHeader from './components/AppHeader.vue';
import AppFooter from './components/AppFooter.vue';

const route = useRoute();
const authStore = useAuthStore();

// Verifica se a rota atual é uma página de autenticação (login, registro) ou de erro (404)
const isAuthOrErrorPage = computed(() => {
  return route.name === 'login' || route.name === 'register' || route.name === 'NotFound';
});

onMounted(() => {
  authStore.initializeAuth();
});
</script>

<style>
html, body {
  margin: 0;
  padding: 0;
  height: 100%;
}
#app {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}
</style>
