<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Painel Administrativo</h1>

    <p class="text-gray-700 text-lg mb-4">Bem-vindo ao painel de administração. Aqui você pode gerenciar usuários, configurações e outras opções administrativas.</p>

    <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 rounded-md mb-6" role="alert">
      <p class="font-bold">Funcionalidade de Teste Admin:</p>
      <p>Esta página é acessível apenas para usuários com o role de 'admin'.</p>
      <p>Tentaremos buscar uma lista de usuários restrita a administradores do backend.</p>
    </div>

    <button @click="fetchAdminUsers" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200">
      Buscar Usuários (Admin Only)
    </button>

    <div v-if="adminUsers.length > 0" class="mt-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Usuários (Admin View)</h2>
      <ul class="divide-y divide-gray-200 bg-gray-50 rounded-lg p-4 shadow-inner">
        <li v-for="user in adminUsers" :key="user.id" class="py-3 flex justify-between items-center">
          <div>
            <p class="text-gray-900 font-medium">{{ user.name }} ({{ user.role }})</p>
            <p class="text-gray-600 text-sm">{{ user.email }}</p>
          </div>
          <span :class="getUserRoleBadgeClass(user.role)">{{ user.role }}</span>
        </li>
      </ul>
    </div>
    <p v-if="adminError" class="text-red-600 mt-4">{{ adminError }}</p>
    <p v-if="adminUsers.length === 0 && !adminError && fetchedAdminUsers" class="text-gray-500 mt-4">Nenhum usuário encontrado ou você não tem permissão.</p>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { api } from '../stores/auth';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const adminUsers = ref([]);
const adminError = ref(null);
const fetchedAdminUsers = ref(false);

const fetchAdminUsers = async () => {
  adminError.value = null;
  fetchedAdminUsers.value = true;
  try {
    const response = await api.get('/admin/users');
    adminUsers.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar usuários (Admin):', error.response?.data || error.message);
    if (error.response?.status === 403) {
      adminError.value = 'Acesso Negado. Você não tem permissões de administrador para ver esta lista.';
    } else {
      adminError.value = 'Erro ao carregar usuários. Tente novamente.';
    }
  }
};

const getUserRoleBadgeClass = (role) => {
  switch (role) {
    case 'admin': return 'bg-purple-200 text-purple-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'manager': return 'bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'user': return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
    default: return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
  }
};
</script>

