<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-2xl transform transition-all duration-300 scale-100 opacity-100">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">
        {{ mode === 'create' ? 'Criar Novo Projeto' : (mode === 'edit' ? 'Editar Projeto' : 'Visualizar Projeto') }}
      </h2>

      <!-- Navegação dentro do Modal (Formulário vs Membros) -->
      <div v-if="modalMode !== 'create'" class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
          <button
            @click="modalSubTab = 'details'"
            :class="[modalSubTab === 'details' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
          >
            Detalhes
          </button>
          <!-- BOTÃO DA ABA MEMBROS: Visível apenas se o usuário for admin ou manager -->
          <button
            v-if="authStore.currentUser && (authStore.currentUser.role === 'admin' || authStore.currentUser.role === 'manager')"
            @click="modalSubTab = 'members'; emit('fetch-members', localProject.id);"
            :class="[modalSubTab === 'members' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
          >
            Membros ({{ localProject.members ? localProject.members.length : 0 }})
          </button>
        </nav>
      </div>

      <!-- Conteúdo do Modal: Detalhes do Projeto -->
      <div v-show="modalSubTab === 'details'">
        <div class="mb-4">
          <label for="project-name" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
          <input type="text" id="project-name" v-model="localProject.name" :readonly="mode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label for="project-description" class="block text-gray-700 text-sm font-bold mb-2">Descrição:</label>
          <textarea id="project-description" v-model="localProject.description" :readonly="mode === 'view'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>
        <div class="mb-4">
          <label for="project-status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
          <select id="project-status" v-model="localProject.status" :disabled="mode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="pending">Pendente</option>
            <option value="in_progress">Em Progresso</option>
            <option value="completed">Concluído</option>
            <option value="cancelled">Cancelado</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="project-priority" class="block text-gray-700 text-sm font-bold mb-2">Prioridade:</label>
          <select id="project-priority" v-model="localProject.priority" :disabled="mode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="low">Baixa</option>
            <option value="medium">Média</option>
            <option value="high">Alta</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="project-start-date" class="block text-gray-700 text-sm font-bold mb-2">Data de Início:</label>
          <input type="date" id="project-start-date" v-model="localProject.start_date" :readonly="mode === 'view'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label for="project-end-date" class="block text-gray-700 text-sm font-bold mb-2">Data de Fim Estimada:</label>
          <input type="date" id="project-end-date" v-model="localProject.end_date" :readonly="mode === 'view'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label for="project-budget" class="block text-gray-700 text-sm font-bold mb-2">Orçamento (R$):</label>
          <input type="number" id="project-budget" v-model="localProject.budget" step="0.01" min="0" :readonly="mode === 'view'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div v-if="formError" class="text-red-500 text-sm mb-4">{{ formError }}</div>
      </div>


      <!-- Conteúdo do Modal: Membros do Projeto -->
      <div v-show="modalSubTab === 'members'">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Membros Atuais</h3>
        <ul v-if="projectMembers.length > 0" class="divide-y divide-gray-200 border border-gray-200 rounded-md">
          <li v-for="member in projectMembers" :key="member.id" class="flex justify-between items-center p-3">
            <div>
              <p class="text-gray-900 font-medium">{{ member.user.name }}</p>
              <p class="text-gray-600 text-sm">{{ member.user.email }} ({{ member.role }})</p>
            </div>
            <!-- Botão Remover Membro - Visível se for edição, ou admin/manager, e não for o próprio usuário -->
            <button
              @click="emit('remove-member', localProject.id, member.user.id)"
              v-if="authStore.currentUser.id !== member.user.id && (mode === 'edit' || authStore.currentUser.role === 'admin' || authStore.currentUser.role === 'manager')"
              class="text-red-600 hover:text-red-800 transition-colors duration-200"
              title="Remover Membro"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            </button>
          </li>
        </ul>
        <p v-else class="text-gray-600 text-center py-4">Nenhum membro neste projeto, exceto o criador.</p>

        <!-- Adicionar Novo Membro - Visível se for edição, ou admin/manager -->
        <h3 class="text-xl font-bold text-gray-800 mt-6 mb-4" v-if="mode === 'edit' || authStore.currentUser.role === 'admin' || authStore.currentUser.role === 'manager'">Adicionar Novo Membro</h3>
        <div class="flex flex-wrap gap-2 items-end" v-if="mode === 'edit' || authStore.currentUser.role === 'admin' || authStore.currentUser.role === 'manager'">
          <select v-model="selectedUserToAdd" class="w-full sm:flex-grow border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">Selecione um usuário</option>
            <option v-for="userOption in availableUsersToAdd" :key="userOption.id" :value="userOption.id">{{ userOption.name }} ({{ userOption.email }})</option>
          </select>
          <select v-model="selectedRoleToAdd" class="w-full sm:w-32 border border-gray-300 rounded-md shadow-sm py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="member">Membro</option>
            <option value="viewer">Visualizador</option>
            <option value="contributor">Colaborador</option>
          </select>
          <button @click="emit('add-member', localProject.id, selectedUserToAdd, selectedRoleToAdd); selectedUserToAdd = ''; selectedRoleToAdd.value = 'member';" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">Adicionar</button>
        </div>
        <p v-if="memberError" class="text-red-500 text-sm mt-2">{{ memberError }}</p>
      </div>

      <!-- Botões de ação do modal -->
      <div class="flex justify-end space-x-4 mt-6">
          <!-- Botão Fechar/Cancelar-->
          <button type="button" @click="emit('close')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200">
            {{ modalMode === 'view' || modalSubTab === 'members' ? 'Fechar' : 'Cancelar' }}
          </button>
          <!-- Botão Salvar -->
          <button
            v-if="modalMode !== 'view' && modalSubTab === 'details'"
            type="submit"
            @click="emit('save-project', localProject)"
            form="modal-form-details"
            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200"
          >
            Salvar
          </button>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
  project: {
    type: Object,
    required: true,
  },
  mode: {
    type: String,
    required: true,
  },
  showModal: {
    type: Boolean,
    required: true,
  },
  formError: {
    type: String,
    default: '',
  },
  memberError: {
    type: String,
    default: '',
  },
  allUsers: {
    type: Array,
    default: () => [],
  },
  projectMembers: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits([
  'close',
  'save-project',
  'add-member',
  'remove-member',
  'fetch-members',
]);

const authStore = useAuthStore();

const localProject = ref({});
watch(() => props.project, (newVal) => {
  localProject.value = JSON.parse(JSON.stringify(newVal));
}, { immediate: true, deep: true });

const modalMode = computed(() => props.mode);

const modalSubTab = ref('details');
const selectedUserToAdd = ref('');
const selectedRoleToAdd = ref('member');

const availableUsersToAdd = computed(() => {
  if (!props.allUsers.length || !props.projectMembers.length) {
    return props.allUsers;
  }
  const memberIds = new Set(props.projectMembers.map(m => m.user_id));
  return props.allUsers.filter(user => !memberIds.has(user.id));
});

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
