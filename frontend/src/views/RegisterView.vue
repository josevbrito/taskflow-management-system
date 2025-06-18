<template>
  <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-purple-600 to-indigo-500 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-xl shadow-2xl p-8 space-y-8 transform transition-transform duration-300 hover:scale-105">
      <div>
        <h2 class="mt-6 text-center text-4xl font-extrabold tracking-tight text-gray-900">
          Crie sua conta TaskFlow
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Comece a organizar suas tarefas hoje!
        </p>
      </div>
      <form class="mt-8 space-y-6" @submit.prevent="handleRegister">
        <div class="rounded-md shadow-sm -space-y-px">
          <div>
            <label for="name" class="sr-only">Nome Completo</label>
            <input
              id="name"
              name="name"
              type="text"
              autocomplete="name"
              required
              v-model="name"
              class="relative block w-full rounded-t-md border-0 py-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
              placeholder="Nome Completo"
            />
          </div>
          <div>
            <label for="email-address" class="sr-only">Endereço de Email</label>
            <input
              id="email-address"
              name="email"
              type="email"
              autocomplete="email"
              required
              v-model="email"
              class="relative block w-full border-0 py-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
              placeholder="Seu Email"
            />
          </div>
          <div>
            <label for="password" class="sr-only">Senha</label>
            <input
              id="password"
              name="password"
              type="password"
              autocomplete="new-password"
              required
              v-model="password"
              class="relative block w-full border-0 py-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
              placeholder="Sua Senha"
            />
          </div>
          <div>
            <label for="password-confirm" class="sr-only">Confirmar Senha</label>
            <input
              id="password-confirm"
              name="password_confirmation"
              type="password"
              autocomplete="new-password"
              required
              v-model="passwordConfirmation"
              class="relative block w-full rounded-b-md border-0 py-2.5 text-gray-900 ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:z-10 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-base sm:leading-6"
              placeholder="Confirme a Senha"
            />
          </div>
        </div>

        <div>
          <button
            type="submit"
            :disabled="isLoading"
            class="group relative flex w-full justify-center rounded-md bg-indigo-600 px-4 py-3 text-lg font-semibold text-white hover:bg-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 transition-colors duration-200"
          >
            <span v-if="isLoading" class="flex items-center">
              <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              Registrando...
            </span>
            <span v-else>Registrar</span>
          </button>
        </div>
        <p v-if="errorMessage" class="text-red-600 text-sm text-center font-medium">{{ errorMessage }}</p>
        <p v-if="successMessage" class="text-green-600 text-sm text-center font-medium">{{ successMessage }}</p>
      </form>
      <div class="text-center">
        <p class="text-sm text-gray-600">
          Já tem uma conta?
          <router-link to="/login" class="font-medium text-indigo-600 hover:text-indigo-500">
            Faça login aqui
          </router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const name = ref('');
const email = ref('');
const password = ref('');
const passwordConfirmation = ref('');
const errorMessage = ref('');
const successMessage = ref('');
const isLoading = ref(false);

const router = useRouter();
const API_URL = import.meta.env.VITE_APP_API_URL || 'http://localhost:8000/api';

const handleRegister = async () => {
  errorMessage.value = '';
  successMessage.value = '';
  isLoading.value = true;
  try {
    const response = await axios.post(`${API_URL}/register`, {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
    });

    successMessage.value = response.data.message || 'Registro realizado com sucesso! Redirecionando para o login...';
    
    setTimeout(() => {
      router.push('/login');
    }, 1000); // Redireciona após 1 segundos
  } catch (error) {
    if (error.response && error.response.data && error.response.data.message) {
      errorMessage.value = error.response.data.message;
    } else if (error.response && error.response.data && error.response.data.errors) {

      errorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
    } else {
      errorMessage.value = 'Ocorreu um erro inesperado ao registrar. Por favor, tente novamente.';
    }
    console.error('Erro de registro:', error);
  } finally {
    isLoading.value = false;
  }
};
</script>

