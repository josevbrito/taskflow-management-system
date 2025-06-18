<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100 p-6">
    <div class="text-center bg-white p-10 rounded-lg shadow-xl">
      <h1 class="text-6xl font-extrabold text-indigo-600 mb-4">404</h1>
      <h2 class="text-3xl font-semibold text-gray-800 mb-4">Página Não Encontrada</h2>
      <p class="text-gray-600 text-lg mb-6">Ops! Parece que você tentou acessar uma página que não existe.</p>
      <router-link to="/" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        Voltar para o Dashboard
      </router-link>
    </div>
  </div>
</template>

<script setup>
</script>

