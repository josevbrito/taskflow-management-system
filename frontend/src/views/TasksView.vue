<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gerenciamento de Tarefas</h1>

    <!-- Botão para adicionar nova tarefa -->
    <div class="mb-6 flex justify-end">
      <button @click="openTaskForm(null)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Nova Tarefa
      </button>
    </div>

    <!-- Lista de Tarefas -->
    <div v-if="tasks.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="task in tasks" :key="task.id" class="bg-gray-50 p-6 rounded-lg shadow-md border border-gray-200">
        <div class="flex justify-between items-start mb-3">
          <h2 class="text-xl font-bold text-gray-800">{{ task.title }}</h2>
          <span :class="getStatusBadgeClass(task.status)">
            {{ task.status }}
          </span>
        </div>
        <p class="text-gray-600 text-sm mb-4">{{ task.description || 'Sem descrição.' }}</p>

        <div class="text-gray-700 text-sm mb-2">
          <span class="font-semibold">Projeto:</span> {{ task.project?.name || 'N/A' }}
        </div>
        <div class="text-gray-700 text-sm mb-2">
          <span class="font-semibold">Atribuído a:</span> {{ task.assigned_user?.name || 'N/A' }}
        </div>
        <div class="text-gray-700 text-sm mb-2">
          <span class="font-semibold">Prioridade:</span> {{ task.priority }}
        </div>
        <div class="text-gray-700 text-sm mb-4">
          <span class="font-semibold">Vencimento:</span> {{ task.due_date ? new Date(task.due_date).toLocaleDateString() : 'N/A' }}
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="openTaskForm(task)" class="text-indigo-600 hover:text-indigo-800 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          </button>
          <button @click="confirmDeleteTask(task.id)" class="text-red-600 hover:text-red-800 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          </button>
        </div>
      </div>
    </div>
    <div v-else class="text-center py-8 text-gray-500">
      Nenhuma tarefa encontrada. Crie uma nova!
    </div>

    <!-- Modal para Formulário de Tarefas -->
    <div v-if="showTaskFormModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-lg transform transition-all duration-300 scale-100 opacity-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ editingTask ? 'Editar Tarefa' : 'Criar Nova Tarefa' }}</h2>
        <form @submit.prevent="saveTask">
          <div class="mb-4">
            <label for="task-title" class="block text-gray-700 text-sm font-bold mb-2">Título:</label>
            <input type="text" id="task-title" v-model="currentTask.title" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="mb-4">
            <label for="task-description" class="block text-gray-700 text-sm font-bold mb-2">Descrição:</label>
            <textarea id="task-description" v-model="currentTask.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
          </div>
          <div class="mb-4">
            <label for="task-project" class="block text-gray-700 text-sm font-bold mb-2">Projeto:</label>
            <select id="task-project" v-model="currentTask.project_id" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="">Selecione um projeto</option>
              <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="task-assigned-to" class="block text-gray-700 text-sm font-bold mb-2">Atribuir a:</label>
            <select id="task-assigned-to" v-model="currentTask.assigned_to" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="">Selecione um usuário</option>
              <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="task-status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
            <select id="task-status" v-model="currentTask.status" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Progresso</option>
              <option value="completed">Concluída</option>
              <option value="cancelled">Cancelada</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="task-priority" class="block text-gray-700 text-sm font-bold mb-2">Prioridade:</label>
            <select id="task-priority" v-model="currentTask.priority" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="low">Baixa</option>
              <option value="medium">Média</option>
              <option value="high">Alta</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="task-due-date" class="block text-gray-700 text-sm font-bold mb-2">Data de Vencimento:</label>
            <input type="date" id="task-due-date" v-model="currentTask.due_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div v-if="formErrorMessage" class="text-red-500 text-sm mb-4">{{ formErrorMessage }}</div>
          <div class="flex justify-end space-x-4">
            <button type="button" @click="showTaskFormModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200">Cancelar</button>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">Salvar</button>
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
import axios from 'axios';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();
const API_URL = import.meta.env.VITE_APP_API_URL || 'http://localhost:8000/api';

const tasks = ref([]);
const projects = ref([]);
const users = ref([]);
const showTaskFormModal = ref(false);
const editingTask = ref(null);
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

const fetchTasks = async () => {
  try {
    const response = await axios.get(`${API_URL}/tasks`, {
      headers: {
        Authorization: `Bearer ${authStore.authToken}`,
      },
    });
    tasks.value = response.data.map(task => ({
      ...task,
      project: projects.value.find(p => p.id === task.project_id),
      assigned_user: users.value.find(u => u.id === task.assigned_to)
    }));
  } catch (error) {
    console.error('Erro ao buscar tarefas:', error.response?.data || error.message);
  }
};

const fetchProjects = async () => {
  try {
    const response = await axios.get(`${API_URL}/projects`, {
      headers: {
        Authorization: `Bearer ${authStore.authToken}`,
      },
    });
    projects.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar projetos:', error.response?.data || error.message);
  }
};

const fetchUsers = async () => {
  try {
    const response = await axios.get(`${API_URL}/users`, {
      headers: {
        Authorization: `Bearer ${authStore.authToken}`,
      },
    });
    users.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar usuários:', error.response?.data || error.message);
  }
};

const openTaskForm = (task) => {
  formErrorMessage.value = '';
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
      await axios.put(`${API_URL}/tasks/${editingTask.value.id}`, currentTask.value, {
        headers: {
          Authorization: `Bearer ${authStore.authToken}`,
        },
      });
    } else {
      await axios.post(`${API_URL}/tasks`, currentTask.value, {
        headers: {
          Authorization: `Bearer ${authStore.authToken}`,
        },
      });
    }
    showTaskFormModal.value = false;
    fetchTasks();
  } catch (error) {
    console.error('Erro ao salvar tarefa:', error.response?.data || error.message);
    if (error.response && error.response.data && error.response.data.errors) {
      formErrorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
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
    await axios.delete(`${API_URL}/tasks/${taskIdToDelete.value}`, {
      headers: {
        Authorization: `Bearer ${authStore.authToken}`,
      },
    });
    showDeleteConfirmModal.value = false;
    fetchTasks();
  } catch (error) {
    console.error('Erro ao excluir tarefa:', error.response?.data || error.message);
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
</script>

