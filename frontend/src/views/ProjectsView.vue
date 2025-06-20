<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Gerenciamento de Projetos</h1>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
      <div class="flex-grow flex items-center gap-2">
        <div class="relative w-full">
          <input
            type="text"
            v-model="searchQuery"
            @keyup.enter="fetchProjects"
            placeholder="Pesquisar projetos..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
          </div>
        </div>
        <button
          @click="fetchProjects"
          class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200 flex-shrink-0"
          title="Aplicar Pesquisa"
        >
          Pesquisar
        </button>
      </div>

      <div class="flex flex-col sm:flex-row gap-4 flex-shrink-0">
        <select v-model="statusFilter" @change="fetchProjects" class="w-full sm:w-auto border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2 px-3 text-gray-700">
          <option value="">Todos os Status</option>
          <option value="Pendente">Pendente</option>
          <option value="Em Progresso">Em Progresso</option>
          <option value="completed">Concluído</option>
          <option value="Cancelado">Cancelado</option>
        </select>

        <select v-model="priorityFilter" @change="fetchProjects" class="w-full sm:w-auto border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-2 px-3 text-gray-700">
          <option value="">Todas as Prioridades</option>
          <option value="Baixa">Baixa</option>
          <option value="Média">Média</option>
          <option value="Alta">Alta</option>
        </select>
      </div>

      <button @click="openProjectForm(null, 'create')" class="w-full md:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200 flex items-center justify-center flex-shrink-0">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
        Novo Projeto
      </button>
    </div>

    <div v-if="paginatedProjects.data && paginatedProjects.data.length > 0" class="overflow-x-auto bg-white rounded-lg shadow-md">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridade</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Criado Por</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Início</th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Fim Estimado</th>
            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Ações</span></th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="project in paginatedProjects.data" :key="project.id">
            <td class="px-6 py-4 whitespace-nowrap">
              <div class="text-sm font-medium text-gray-900">{{ project.name }}</div>
              <div class="text-sm text-gray-500 truncate w-48">{{ project.description }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getStatusBadgeClass(project.status)">
                {{ project.status }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span :class="getPriorityBadgeClass(project.priority)">
                {{ project.priority }}
              </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
              <div class="text-sm text-gray-900">{{ project.user?.name || 'N/A' }}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ project.start_date ? new Date(project.start_date).toLocaleDateString() : 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden md:table-cell">{{ project.end_date ? new Date(project.end_date).toLocaleDateString() : 'N/A' }}</td>
            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
              <div class="flex justify-end space-x-2">
                <button @click="openProjectForm(project, 'view')" class="text-blue-600 hover:text-blue-800 transition-colors duration-200" title="Visualizar">
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
                <button
                  v-if="authStore.currentUser && (authStore.currentUser.role === 'admin' || authStore.currentUser.role === 'manager')"
                  @click="openProjectForm(project, 'edit')"
                  class="text-indigo-600 hover:text-indigo-800 transition-colors duration-200"
                  title="Editar"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                </button>
                <button
                  v-if="authStore.currentUser && (authStore.currentUser.role === 'admin' || authStore.currentUser.role === 'manager')"
                  @click="confirmDeleteProject(project.id)"
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
      Nenhum projeto encontrado.
    </div>

    <div v-if="paginatedProjects.last_page > 1" class="flex justify-between items-center mt-6">
      <div class="text-sm text-gray-700">
        Mostrando {{ paginatedProjects.from }} a {{ paginatedProjects.to }} de {{ paginatedProjects.total }} resultados
      </div>
      <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
        <button
          @click="changePage(paginatedProjects.current_page - 1)"
          :disabled="!paginatedProjects.prev_page_url"
          class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        >
          <span class="sr-only">Anterior</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
        </button>
        <button
          v-for="page in paginatedProjects.last_page"
          :key="page"
          @click="changePage(page)"
          :class="['relative inline-flex items-center px-4 py-2 border text-sm font-medium', page === paginatedProjects.current_page ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600' : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50']"
        >
          {{ page }}
        </button>
        <button
          @click="changePage(paginatedProjects.current_page + 1)"
          :disabled="!paginatedProjects.next_page_url"
          class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
        >
          <span class="sr-only">Próximo</span>
          <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </button>
      </nav>
    </div>
    <div v-if="formErrorMessage" class="text-red-500 text-sm mt-4 text-center">{{ formErrorMessage }}</div>

    <ProjectModal
      v-if="showProjectFormModal"
      :project="currentProject"
      :mode="modalMode"
      :form-error="formErrorMessage"
      :member-error="memberError"
      :all-users="allUsers"
      :project-members="projectMembers"
      @close="showProjectFormModal = false"
      @save-project="saveProject"
      @add-member="addProjectMember"
      @remove-member="removeProjectMember"
      @fetch-members="fetchProjectMembers"
    />

    <div v-if="showDeleteConfirmModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-sm">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Confirmar Exclusão</h2>
        <p class="text-gray-700 mb-6">Tem certeza de que deseja excluir este projeto? Todas as tarefas associadas também serão excluídas.</p>
        <div class="flex justify-end space-x-4">
          <button type="button" @click="showDeleteConfirmModal = false" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200">Cancelar</button>
          <button type="button" @click="deleteProject" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">Excluir</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { api } from '../stores/auth';
import ProjectModal from '../components/ProjectModal.vue';

const authStore = useAuthStore();

const paginatedProjects = ref({
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

const showProjectFormModal = ref(false);
const editingProject = ref(null);
const modalMode = ref('create');
const currentProject = ref({
  id: null,
  name: '',
  description: '',
  status: 'Pendente',
  priority: 'Média',
  start_date: '',
  end_date: '',
  budget: 0,
  members: [],
});
const formErrorMessage = ref('');
const memberError = ref('');

const showDeleteConfirmModal = ref(false);
const projectIdToDelete = ref(null);

const allUsers = ref([]);
const projectMembers = ref([]);

onMounted(() => {
  fetchProjects();
  fetchAllUsers();
});

const fetchProjects = async (page = 1) => {
  try {
    const response = await api.get('/projects', {
      params: {
        page: page,
        search: searchQuery.value,
        status: statusFilter.value,
        priority: priorityFilter.value,
      },
    });
    paginatedProjects.value = response.data;
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

const fetchAllUsers = async () => {
  try {
    const response = await api.get('/users', { params: { all: true } });
    allUsers.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar todos os usuários:', error.response?.data || error.message);
  }
};

const fetchProjectMembers = async (projectId) => {
  memberError.value = '';
  try {
    const response = await api.get(`/projects/${projectId}/members`);
    projectMembers.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar membros do projeto:', error.response?.data || error.message);
    memberError.value = 'Erro ao carregar membros do projeto. ' + (error.response?.data?.message || '');
  }
};

const addProjectMember = async (projectId, userId, role) => {
  memberError.value = '';
  try {
    const response = await api.post(`/projects/${projectId}/members`, {
      user_id: userId,
      role: role,
    });
    projectMembers.value.push(response.data);
  } catch (error) {
    console.error('Erro ao adicionar membro:', error.response?.data || error.message);
    memberError.value = 'Erro ao adicionar membro. ' + (error.response?.data?.message || Object.values(error.response?.data?.errors || {}).flat().join(' ') || '');
  }
};

const removeProjectMember = async (projectId, userId) => {
  memberError.value = '';
  if (!confirm('Tem certeza que deseja remover este membro?')) {
    return;
  }
  try {
    await api.delete(`/projects/${projectId}/members/${userId}`);
    projectMembers.value = projectMembers.value.filter(m => m.user_id !== userId);
  } catch (error) {
    console.error('Erro ao remover membro:', error.response?.data || error.message);
    memberError.value = 'Erro ao remover membro. ' + (error.response?.data?.message || '');
  }
};

const changePage = (page) => {
  if (page >= 1 && page <= paginatedProjects.value.last_page) {
    fetchProjects(page);
  }
};

const openProjectForm = (project, mode = 'edit') => {
  formErrorMessage.value = '';
  modalMode.value = mode;

  if (project) {
    editingProject.value = project;
    currentProject.value = JSON.parse(JSON.stringify(project));
    currentProject.value.start_date = project.start_date ? new Date(project.start_date).toISOString().split('T')[0] : '';
    currentProject.value.end_date = project.end_date ? new Date(project.end_date).toISOString().split('T')[0] : '';
  } else {
    editingProject.value = null;
    currentProject.value = {
      name: '',
      description: '',
      status: 'Pendente',
      priority: 'Média',
      start_date: new Date().toISOString().split('T')[0],
      end_date: '',
      budget: 0,
      members: [],
    };
  }
  showProjectFormModal.value = true;
};

const saveProject = async (projectData) => {
  formErrorMessage.value = '';
  try {
    if (editingProject.value) {
      await api.put(`/projects/${projectData.id}`, projectData);
    } else {
      await api.post('/projects', projectData);
    }
    showProjectFormModal.value = false;
    fetchProjects(paginatedProjects.value.current_page);
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
    fetchProjects(paginatedProjects.value.current_page);
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
    case 'Pendente': return 'bg-yellow-200 text-yellow-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'Em Progresso': return 'bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'Concluído': return 'bg-green-200 text-green-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'Cancelado': return 'bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs font-semibold';
    default: return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
  }
};

const getPriorityBadgeClass = (priority) => {
  switch (priority) {
    case 'Baixa': return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'Média': return 'bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'Alta': return 'bg-red-200 text-red-800 px-2 py-1 rounded-full text-xs font-semibold';
    default: return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
  }
};
</script>