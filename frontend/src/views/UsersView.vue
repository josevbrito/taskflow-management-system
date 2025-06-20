<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gerenciamento de Utilizadores</h1>

    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
      <div class="relative w-full sm:w-1/2">
        <input
          type="text"
          v-model="searchQuery"
          @keyup.enter="fetchUsers"
          placeholder="Pesquisar utilizadores..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
      </div>
      <button v-if="authStore.currentUser && authStore.currentUser.role === 'admin'" @click="openUserForm(null, 'create')" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200 flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Novo Utilizador
      </button>
    </div>

    <div v-if="paginatedUsers.data && paginatedUsers.data.length > 0" class="overflow-x-auto bg-white rounded-lg shadow-md">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="user in paginatedUsers.data" :key="user.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="flex items-center">
                <img class="h-8 w-8 rounded-full mr-2 object-cover" :src="user.avatar || 'https://placehold.co/32x32/cccccc/000000?text=AU'" alt="Avatar">
                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
              </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm text-gray-900">{{ user.email }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getUserRoleBadgeClass(user.role)">
                {{ user.role }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="user.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                {{ user.is_active ? 'Ativo' : 'Inativo' }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end space-x-2">
                <button @click="openUserForm(user, 'view')" class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="Visualizar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button
                  v-if="authStore.currentUser && authStore.currentUser.role === 'admin'"
                  @click="openUserForm(user, 'edit')"
                  class="text-indigo-600 hover:text-indigo-800 transition-colors duration-200"
                  title="Editar"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                <button
                  v-if="authStore.currentUser && authStore.currentUser.role === 'admin'"
                  @click="confirmDeleteUser(user.id)"
                  class="text-red-600 hover:text-red-800 transition-colors duration-200"
                  title="Excluir"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="text-center py-8 text-gray-500">
      Nenhum utilizador encontrado.
    </div>

    <div v-if="paginatedUsers.last_page > 1" class="flex justify-between items-center mt-6">
      <div class="text-sm text-gray-700">
        Mostrando {{ paginatedUsers.from }} a {{ paginatedUsers.to }} de {{ paginatedUsers.total }} resultados
      </div>
      <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <button
          @click="changePage(paginatedUsers.current_page - 1)"
          :disabled="!paginatedUsers.prev_page_url"
          class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        >
          <span class="sr-only">Anterior</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button
          v-for="page in paginatedUsers.last_page"
          :key="page"
          @click="changePage(page)"
          :class="['relative inline-flex items-center px-4 py-2 border text-sm font-medium', page === paginatedUsers.current_page ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50']"
        >
          {{ page }}
        </button>
        <button
          @click="changePage(paginatedUsers.current_page + 1)"
          :disabled="!paginatedUsers.next_page_url"
          class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        >
          <span class="sr-only">Próximo</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </nav>
    </div>
    <div v-if="formErrorMessage" class="text-red-500 text-sm mt-4 text-center">{{ formErrorMessage }}</div>

    <UserModal
      v-if="showUserFormModal"
      :user="currentUser"
      :mode="modalMode"
      :show-modal="showUserFormModal"
      :form-error="formErrorMessage"
      @close="showUserFormModal = false"
      @save-user="saveUser"
    />

    <div v-if="showDeleteConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-sm">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Confirmar Exclusão</h2>
        <p class="text-gray-700 mb-6">Tem certeza de que deseja excluir este utilizador?</p>
        <div class="flex justify-end space-x-4">
          <button @click="showDeleteConfirmModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md">Cancelar</button>
          <button @click="deleteUser" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md">Excluir</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { api } from '../stores/auth';
import UserModal from '../components/UserModal.vue'; // Importa o novo componente UserModal

const authStore = useAuthStore();

const paginatedUsers = ref({
  data: [],
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0,
});
const searchQuery = ref('');

const showUserFormModal = ref(false);
const editingUser = ref(null);
const modalMode = ref('create');

const currentUser = ref({
  name: '',
  email: '',
  role: 'user',
  password: '',
  password_confirmation: '',
  is_active: true,
});
const formErrorMessage = ref('');

const showDeleteConfirmModal = ref(false);
const userIdToDelete = ref(null);

onMounted(() => {
  fetchUsers();
});

const fetchUsers = async (page = 1) => {
  try {
    const response = await api.get('/users', {
      params: {
        page: page,
        search: searchQuery.value,
      },
    });
    paginatedUsers.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar utilizadores:', error.response?.data || error.message);
    if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizado. Faça login novamente.';
      authStore.logout();
    } else if (error.response?.status === 403) {
      formErrorMessage.value = 'Você não tem permissão para visualizar utilizadores.';
    } else {
      formErrorMessage.value = 'Erro ao buscar utilizadores. Tente novamente.';
    }
  }
};

const changePage = (page) => {
  if (page >= 1 && page <= paginatedUsers.value.last_page) {
    fetchUsers(page);
  }
};

const openUserForm = (user, mode = 'edit') => {
  console.log("openUserForm: user", user);
  console.log("openUserForm: mode", mode);
  formErrorMessage.value = '';
  modalMode.value = mode;

  if (user) {
    editingUser.value = user;
    currentUser.value = { ...user, password: '', password_confirmation: '' };
  } else {
    editingUser.value = null;
    currentUser.value = {
      name: '',
      email: '',
      role: 'user',
      password: '',
      password_confirmation: '',
      is_active: true,
    };
  }
  showUserFormModal.value = true;
};

const saveUser = async (userData) => { // Recebe userData do UserModal
  console.log("saveUser: editingUser.value", editingUser.value);
  console.log("saveUser: userData", userData);
  formErrorMessage.value = '';
  try {
    if (editingUser.value) { // Se editingUser.value tem um valor, é uma edição
      await api.put(`/users/${editingUser.value.id}`, userData);
    } else {
      await api.post('/users', userData);
    }
    showUserFormModal.value = false;
    fetchUsers(paginatedUsers.value.current_page);
  } catch (error) {
    console.error('Erro ao salvar utilizador:', error.response?.data || error.message);
    if (error.response && error.response.data && error.response.data.errors) {
      formErrorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
    } else if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizado. Faça login novamente.';
      authStore.logout();
    } else if (error.response?.status === 403) {
      formErrorMessage.value = 'Você não tem permissão para salvar este utilizador.';
    } else {
      formErrorMessage.value = 'Erro ao salvar utilizador. Tente novamente.';
    }
  }
};

const confirmDeleteUser = (id) => {
  userIdToDelete.value = id;
  showDeleteConfirmModal.value = true;
};

const deleteUser = async () => {
  try {
    await api.delete(`/users/${userIdToDelete.value}`);
    showDeleteConfirmModal.value = false;
    fetchUsers(paginatedUsers.value.current_page);
  } catch (error) {
    console.error('Erro ao excluir utilizador:', error.response?.data || error.message);
    if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizado. Faça login novamente.';
      authStore.logout();
    } else if (error.response?.status === 403) {
      formErrorMessage.value = 'Você não tem permissão para excluir este utilizador.';
    } else {
      formErrorMessage.value = 'Erro ao excluir utilizador. Tente novamente.';
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