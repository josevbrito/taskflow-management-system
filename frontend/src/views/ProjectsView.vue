<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gerenciamento de Projetos</h1>

    <!-- Botão para adicionar novo projeto -->
    <div class="mb-6 flex justify-end">
      <button @click="openProjectForm(null)" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200 flex items-center">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Novo Projeto
      </button>
    </div>

    <!-- Lista de Projetos -->
    <div v-if="projects.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div v-for="project in projects" :key="project.id" class="bg-gray-50 p-6 rounded-lg shadow-md border border-gray-200">
        <div class="flex justify-between items-start mb-3">
          <h2 class="text-xl font-bold text-gray-800">{{ project.name }}</h2>
          <span :class="getStatusBadgeClass(project.status)">
            {{ project.status }}
          </span>
        </div>
        <p class="text-gray-600 text-sm mb-4">{{ project.description || 'Sem descrição.' }}</p>

        <div class="text-gray-700 text-sm mb-2">
          <span class="font-semibold">Criado por:</span> {{ project.user?.name || 'N/A' }}
        </div>
        <div class="text-gray-700 text-sm mb-2">
          <span class="font-semibold">Início:</span> {{ project.start_date ? new Date(project.start_date).toLocaleDateString() : 'N/A' }}
        </div>
        <div class="text-gray-700 text-sm mb-4">
          <span class="font-semibold">Fim Estimado:</span> {{ project.end_date ? new Date(project.end_date).toLocaleDateString() : 'N/A' }}
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="openProjectForm(project)" class="text-indigo-600 hover:text-indigo-800 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
          </button>
          <button @click="confirmDeleteProject(project.id)" class="text-red-600 hover:text-red-800 transition-colors duration-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          </button>
        </div>
      </div>
    </div>
    <div v-else class="text-center py-8 text-gray-500">
      Nenhum projeto encontrado. Crie um novo!
    </div>

    <!-- Modal para Formulário de Projetos -->
    <div v-if="showProjectFormModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-lg transform transition-all duration-300 scale-100 opacity-100">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">{{ editingProject ? 'Editar Projeto' : 'Criar Novo Projeto' }}</h2>
        <form @submit.prevent="saveProject">
          <div class="mb-4">
            <label for="project-name" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
            <input type="text" id="project-name" v-model="currentProject.name" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="mb-4">
            <label for="project-description" class="block text-gray-700 text-sm font-bold mb-2">Descrição:</label>
            <textarea id="project-description" v-model="currentProject.description" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
          </div>
          <div class="mb-4">
            <label for="project-status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
            <select id="project-status" v-model="currentProject.status" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="pending">Pendente</option>
              <option value="in_progress">Em Progresso</option>
              <option value="completed">Concluído</option>
              <option value="cancelled">Cancelado</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="project-priority" class="block text-gray-700 text-sm font-bold mb-2">Prioridade:</label>
            <select id="project-priority" v-model="currentProject.priority" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
              <option value="low">Baixa</option>
              <option value="medium">Média</option>
              <option value="high">Alta</option>
            </select>
          </div>
          <div class="mb-4">
            <label for="project-start-date" class="block text-gray-700 text-sm font-bold mb-2">Data de Início:</label>
            <input type="date" id="project-start-date" v-model="currentProject.start_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="mb-4">
            <label for="project-end-date" class="block text-gray-700 text-sm font-bold mb-2">Data de Fim Estimada:</label>
            <input type="date" id="project-end-date" v-model="currentProject.end_date" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>
          <div class="mb-4">
            <label for="project-budget" class="block text-gray-700 text-sm font-bold mb-2">Orçamento (R$):</label>
            <input type="number" id="project-budget" v-model="currentProject.budget" step="0.01" min="0" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
          </div>

          <div v-if="formErrorMessage" class="text-red-500 text-sm mb-4">{{ formErrorMessage }}</div>
          <div class="flex justify-end space-x-4">
            <button type="button" @click="showProjectFormModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200">Cancelar</button>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">Salvar</button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div v-if="showDeleteConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-sm">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Confirmar Exclusão</h2>
        <p class="text-gray-700 mb-6">Tem certeza de que deseja excluir este projeto? Todas as tarefas associadas também serão excluídas.</p>
        <div class="flex justify-end space-x-4">
          <button @click="showDeleteConfirmModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md">Cancelar</button>
          <button @click="deleteProject" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md">Excluir</button>
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

const projects = ref([]);
const showProjectFormModal = ref(false);
const editingProject = ref(null);
const currentProject = ref({
  name: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  start_date: '',
  end_date: '',
  budget: 0,
});
const formErrorMessage = ref('');

const showDeleteConfirmModal = ref(false);
const projectIdToDelete = ref(null);

onMounted(() => {
  fetchProjects();
});

const fetchProjects = async () => {
  try {
    const response = await api.get('/projects');
    projects.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar projetos:', error.response?.data || error.message);
    if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizado. Faça login novamente.';
      authStore.logout();
    } else {
      formErrorMessage.value = 'Erro ao buscar projetos. Tente novamente.';
    }
  }
};

const openProjectForm = (project) => {
  formErrorMessage.value = '';
  if (project) {
    editingProject.value = project;
    currentProject.value = { ...project,
      start_date: project.start_date ? new Date(project.start_date).toISOString().split('T')[0] : '',
      end_date: project.end_date ? new Date(project.end_date).toISOString().split('T')[0] : '',
    };
  } else {
    editingProject.value = null;
    currentProject.value = {
      name: '',
      description: '',
      status: 'pending',
      priority: 'medium',
      start_date: new Date().toISOString().split('T')[0],
      end_date: '',
      budget: 0,
    };
  }
  showProjectFormModal.value = true;
};

const saveProject = async () => {
  formErrorMessage.value = '';
  try {
    if (editingProject.value) {
      await api.put(`/projects/${editingProject.value.id}`, currentProject.value);
    } else {
      await api.post('/projects', currentProject.value);
    }
    showProjectFormModal.value = false;
    fetchProjects();
  } catch (error) {
    console.error('Erro ao salvar projeto:', error.response?.data || error.message);
    if (error.response && error.response.data && error.response.data.errors) {
      formErrorMessage.value = Object.values(error.response.data.errors).flat().join(' ');
    } else if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizado. Faça login novamente.';
      authStore.logout();
    } else {
      formErrorMessage.value = 'Erro ao salvar projeto. Tente novamente.';
    }
  }
};

const confirmDeleteProject = (id) => {
  projectIdToDelete.value = id;
  showDeleteConfirmModal.value = true;
};

const deleteProject = async () => {
  try {
    await api.delete(`/projects/${projectIdToDelete.value}`);
    showDeleteConfirmModal.value = false;
    fetchProjects();

  } catch (error) {

    console.error('Erro ao excluir projeto:', error.response?.data || error.message);

    if (error.response?.status === 401) {
      formErrorMessage.value = 'Sessão expirada ou não autorizada. Faça login novamente.';
      authStore.logout();
    } else {
      formErrorMessage.value = 'Erro ao excluir projeto. Tente novamente.';
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
</script>
