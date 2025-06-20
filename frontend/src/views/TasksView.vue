<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gerenciamento de Tarefas</h1>

    <!-- Área de Pesquisa, Filtros e Nova Tarefa -->
    <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0 sm:space-x-4">
      <div class="relative w-full sm:w-1/2">
        <input
          type="text"
          v-model="searchQuery"
          @keyup.enter="fetchTasks"
          placeholder="Pesquisar tarefas..."
          class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
        />
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
          <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        </div>
      </div>

      <!-- Filtro de Status -->
      <select v-model="statusFilter" @change="fetchTasks" class="w-full sm:w-auto border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2 px-3 text-gray-700">
        <option value="">Todos os Status</option>
        <option value="pending">Pendente</option>
        <option value="in_progress">Em Progresso</option>
        <option value="completed">Concluída</option>
        <option value="cancelled">Cancelada</option>
      </select>

      <!-- Filtro de Prioridade -->
      <select v-model="priorityFilter" @change="fetchTasks" class="w-full sm:w-auto border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2 px-3 text-gray-700">
        <option value="">Todas as Prioridades</option>
        <option value="low">Baixa</option>
        <option value="medium">Média</option>
        <option value="high">Alta</option>
      </select>

      <button @click="openTaskForm(null, 'create')" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200 flex items-center justify-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nova Tarefa
      </button>
    </div>

    <!-- Tabela de Tarefas -->
    <div v-if="paginatedTasks.data && paginatedTasks.data.length > 0" class="overflow-x-auto bg-white rounded-lg shadow-md">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridade</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Projeto</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Atribuído a</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Criado Por</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Vencimento</th>
            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="task in paginatedTasks.data" :key="task.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ task.title }}</div>
              <div class="text-sm text-gray-500 truncate w-48">{{ task.description }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusBadgeClass(task.status)">
                {{ task.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getPriorityBadgeClass(task.priority)">
                {{ task.priority }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
              <div class="text-sm text-gray-900">{{ task.project?.name || 'N/A' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
              <div class="text-sm text-gray-900">{{ task.assigned_user?.name || 'N/A' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">
              <div class="text-sm text-gray-900">{{ task.created_by_user?.name || 'N/A' }}</div> <!-- Usar created_by_user -->
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ task.due_date ? new Date(task.due_date).toLocaleDateString() : 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end space-x-2">
                <!-- Botão Visualizar -->
                <button @click="openTaskForm(task, 'view')" class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="Visualizar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button @click="openTaskForm(task, 'edit')" class="text-indigo-600 hover:text-indigo-800 transition-colors duration-200" title="Editar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                <button @click="confirmDeleteTask(task.id)" class="text-red-600 hover:text-red-800 transition-colors duration-200" title="Excluir">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-else class="text-center py-8 text-gray-500">
      Nenhuma tarefa encontrada.
    </div>

    <!-- Paginação -->
    <div v-if="paginatedTasks.last_page > 1" class="flex justify-between items-center mt-6">
      <div class="text-sm text-gray-700">
        Mostrando {{ paginatedTasks.from }} a {{ paginatedTasks.to }} de {{ paginatedTasks.total }} resultados
      </div>
      <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <button
          @click="changePage(paginatedTasks.current_page - 1)"
          :disabled="!paginatedTasks.prev_page_url"
          class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        >
          <span class="sr-only">Anterior</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button
          v-for="page in paginatedTasks.last_page"
          :key="page"
          @click="changePage(page)"
          :class="['relative inline-flex items-center px-4 py-2 border text-sm font-medium', page === paginatedTasks.current_page ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50']"
        >
          {{ page }}
        </button>
        <button
          @click="changePage(paginatedTasks.current_page + 1)"
          :disabled="!paginatedTasks.next_page_url"
          class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        >
          <span class="sr-only">Próximo</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </nav>
    </div>
    <div v-if="formErrorMessage" class="text-red-500 text-sm mt-4 text-center">{{ formErrorMessage }}</div>

    <!-- Modal para Formulário/Visualização de Tarefas -->
    <div v-if="showTaskFormModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-lg transform transition-all duration-300 scale-100 opacity-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">
          {{ modalMode === 'create' ? 'Criar Nova Tarefa' : (modalMode === 'edit' ? 'Editar Tarefa' : 'Visualizar Tarefa') }}
        </h2>
        <form @submit.prevent="saveTask">
          <div class="mb-4">
            <label for="task-title" class="block text-gray-700 text-sm font-bold mb-2">Título:</label>
            <input type="text" id="task-title" v-model="currentTask.title" :readonly="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="mb-4">
            <label for="task-description" class="block text-gray-700 text-sm font-bold mb-2">Descrição:</label>
            <textarea id="task-description" v-model="currentTask.description" :readonly="modalMode === 'view'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
          </div>
          <div class="mb-4">
            <label for="task-project" class="block text-gray-700 text-sm font-bold mb-2">Projeto:</label>
            <select id="task-project" v-model="currentTask.project_id" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="">Selecione um projeto</option>
              <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="task-assigned-to" class="block text-gray-700 text-sm font-bold mb-2">Atribuir a:</label>
            <select id="task-assigned-to" v-model="currentTask.assigned_to" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="">Selecione um usuário</option>
              <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="task-status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
            <select id="task-status" v-model="currentTask.status" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Progresso</option>
              <option value="completed">Concluída</option>
              <option value="cancelled">Cancelada</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="task-priority" class="block text-gray-700 text-sm font-bold mb-2">Prioridade:</label>
            <select id="task-priority" v-model="currentTask.priority" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="low">Baixa</option>
              <option value="medium">Média</option>
              <option value="high">Alta</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="task-due-date" class="block text-gray-700 text-sm font-bold mb-2">Data de Vencimento:</label>
            <input type="date" id="task-due-date" v-model="currentTask.due_date" :readonly="modalMode === 'view'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div v-if="formErrorMessage" class="text-red-500 text-sm mb-4">{{ formErrorMessage }}</div>
          <div class="flex justify-end space-x-4">
            <button type="button" @click="showTaskFormModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200">
              {{ modalMode === 'view' ? 'Fechar' : 'Cancelar' }}
            </button>
            <button v-if="modalMode !== 'view'" type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">Salvar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-sm">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Confirmar Exclusão</h2>
        <p class="text-gray-700 mb-6">Tem certeza de que deseja excluir esta tarefa?</p>
        <div class="flex justify-end space-x-4">
          <button @click="showDeleteConfirmModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md">Cancelar</button>
          <button @click="deleteTask" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md">Excluir</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import { api } from '../stores/auth';

const authStore = useAuthStore();

const paginatedTasks = ref({
  data: [],
  current_page: 1,
  last_page: 1,
  from: 0,
  to: 0,
  total: 0,
});
const searchQuery = ref('');
const statusFilter = ref('');
const priorityFilter = ref('');


const projects = ref([]);
const users = ref([]);
const showTaskFormModal = ref(false);
const editingTask = ref(null);
const modalMode = ref('create');
const currentTask = ref({
  title: '',
  description: '',
  project_id: '',
  assigned_to: '',
  status: 'pending',
  priority: 'medium',
  due_date: '',
});
const formErrorMessage = ref('');

const showDeleteConfirmModal = ref(false);
const taskIdToDelete = ref(null);

onMounted(() => {
  fetchTasks();
  fetchProjects();
  fetchUsers();
});

const fetchTasks = async (page = 1) => {
  try {
    const response = await api.get('/tasks', {
      params: {
        page: page,
        search: searchQuery.value,
        status: statusFilter.value,
        priority: priorityFilter.value,
      },
    });
    paginatedTasks.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar tarefas:', error.response?.data || error.message);
    if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizado. Faça login novamente.';
      authStore.logout();
    } else {
      formErrorMessage.value = 'Erro ao buscar tarefas. Tente novamente.';
    }
  }
};

const fetchProjects = async () => {
  try {
    // Buscar todos os projetos, não paginados, para o dropdown
    const response = await api.get('/projects', { params: { all: true } });
    projects.value = response.data.data ? response.data.data : response.data;
  } catch (error) {
    console.error('Erro ao buscar projetos para tarefas:', error.response?.data || error.message);
  }
};

const fetchUsers = async () => {
  try {
    // Buscar todos os usuários, não paginados, para o dropdown
    const response = await api.get('/users', { params: { all: true } });
    users.value = response.data.data ? response.data.data : response.data;
  } catch (error) {
    console.error('Erro ao buscar usuários para tarefas:', error.response?.data || error.message);
  }
};

const changePage = (page) => {
  if (page >= 1 && page <= paginatedTasks.value.last_page) {
    fetchTasks(page);
  }
};

const openTaskForm = (task, mode = 'edit') => {
  formErrorMessage.value = '';
  modalMode.value = mode;

  if (task) {
    editingTask.value = task;
    currentTask.value = { ...task, due_date: task.due_date ? new Date(task.due_date).toISOString().split('T')[0] : '' };
  } else {
    editingTask.value = null;
    currentTask.value = {
      title: '',
      description: '',
      project_id: projects.value.length > 0 ? projects.value[0].id : '',
      assigned_to: authStore.currentUser?.id || '',
      status: 'pending',
      priority: 'medium',
      due_date: '',
    };
  }
  showTaskFormModal.value = true;
};

const saveTask = async () => {
  formErrorMessage.value = '';
  try {
    if (editingTask.value) {
      await api.put(`/tasks/${editingTask.value.id}`, currentTask.value);
    } else {
      await api.post('/tasks', currentTask.value);
    }
    showTaskFormModal.value = false;
    fetchTasks(paginatedTasks.value.current_page);
  } catch (error) {
    console.error('Erro ao salvar tarefa:', error.response?.data || error.message);
    if (error.response && error.response.data && error.response.data.errors) {
      formErrorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
    } else if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizado. Faça login novamente.';
      authStore.logout();
    } else {
      formErrorMessage.value = 'Erro ao salvar tarefa. Tente novamente.';
    }
  }
};

const confirmDeleteTask = (id) => {
  taskIdToDelete.value = id;
  showDeleteConfirmModal.value = true;
};

const deleteTask = async () => {
  try {
    await api.delete(`/tasks/${taskIdToDelete.value}`);
    showDeleteConfirmModal.value = false;
    fetchTasks(paginatedTasks.value.current_page);
  } catch (error) {
    console.error('Erro ao excluir tarefa:', error.response?.data || error.message);
    if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizada. Faça login novamente.';
      authStore.logout();
    } else {
      formErrorMessage.value = 'Erro ao excluir tarefa. Tente novamente.';
    }
  }
};

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'pending': return 'bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'in_progress': return 'bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'completed': return 'bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'cancelled': return 'bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs font-semibold';
    default: return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
  }
};

const getPriorityBadgeClass = (priority) => {
  switch (priority) {
    case 'low': return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'medium': return 'bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'high': return 'bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs font-semibold';
    default: return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
  }
};
</script>
