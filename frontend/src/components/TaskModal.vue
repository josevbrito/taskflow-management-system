<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-2xl transform transition-all duration-300 scale-100 opacity-100">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">
        {{ modalMode === 'create' ? 'Criar Nova Tarefa' : (modalMode === 'edit' ? 'Editar Tarefa' : 'Visualizar Tarefa') }}
      </h2>

      <!-- Navegação dentro do Modal (Detalhes vs Comentários) -->
      <div v-if="modalMode !== 'create'" class="mb-6 border-b border-gray-200">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
          <button
            @click="modalSubTab = 'details'"
            :class="[modalSubTab === 'details' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
          >
            Detalhes
          </button>
          <button
            @click="modalSubTab = 'comments'; emit('fetch-comments', localTask.id)"
            :class="[modalSubTab === 'comments' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300', 'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm']"
          >
            Comentários ({{ taskComments.length || 0 }})
          </button>
        </nav>
      </div>

      <!-- Detalhes da Tarefa -->
      <form @submit.prevent="emit('save-task', localTask)" v-show="modalSubTab === 'details'">
        <div class="mb-4">
          <label for="task-title" class="block text-gray-700 text-sm font-bold mb-2">Título:</label>
          <input type="text" id="task-title" v-model="localTask.title" :readonly="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label for="task-description" class="block text-gray-700 text-sm font-bold mb-2">Descrição:</label>
          <textarea id="task-description" v-model="localTask.description" :readonly="modalMode === 'view'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>
        <div class="mb-4">
          <label for="task-project" class="block text-gray-700 text-sm font-bold mb-2">Projeto:</label>
          <select id="task-project" v-model="localTask.project_id" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">Selecione um projeto</option>
            <option v-for="project in projects" :key="project.id" :value="project.id">{{ project.name }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="task-assigned-to" class="block text-gray-700 text-sm font-bold mb-2">Atribuir a:</label>
          <select id="task-assigned-to" v-model="localTask.assigned_to" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="">Selecione um usuário</option>
            <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="task-status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
          <select id="task-status" v-model="localTask.status" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="Pendente">Pendente</option>
            <option value="Em Progresso">Em Progresso</option>
            <option value="completed">Concluída</option>
            <option value="Cancelado">Cancelada</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="task-priority" class="block text-gray-700 text-sm font-bold mb-2">Prioridade:</label>
          <select id="task-priority" v-model="localTask.priority" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="Baixa">Baixa</option>
            <option value="Média">Média</option>
            <option value="Alta">Alta</option>
          </select>
        </div>
        <div class="mb-4">
          <label for="task-due-date" class="block text-gray-700 text-sm font-bold mb-2">Data de Vencimento:</label>
          <input type="date" id="task-due-date" v-model="localTask.due_date" :readonly="modalMode === 'view'" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div v-if="formError" class="text-red-500 text-sm mb-4">{{ formError }}</div>
      </form>

      <!-- Comentários da Tarefa -->
      <div v-show="modalSubTab === 'comments'">
        <h3 class="text-xl font-bold text-gray-800 mb-4">Comentários</h3>
        <div v-if="taskComments.length > 0" class="space-y-4 max-h-60 overflow-y-auto pr-2">
          <div v-for="comment in taskComments" :key="comment.id" class="bg-gray-100 p-3 rounded-md border border-gray-200">
            <div class="flex justify-between items-center text-sm text-gray-600 mb-1">
              <span class="font-semibold">{{ comment.user?.name || 'Usuário Desconhecido' }}</span>
              <span>{{ new Date(comment.created_at).toLocaleString() }}</span>
            </div>
            <p class="text-gray-800 text-base">{{ comment.content }}</p>
            <div class="flex justify-end mt-2" v-if="authStore.currentUser.id === comment.user_id || authStore.currentUser.role === 'admin' || authStore.currentUser.role === 'manager'">
               <button @click="emit('delete-comment', localTask.id, comment.id)" class="text-red-500 hover:text-red-700 text-sm">Excluir</button>
            </div>
          </div>
        </div>
        <p v-else class="text-gray-600 text-center py-4">Nenhum comentário ainda.</p>

        <!-- Adicionar Comentário -->
        <div class="mt-6 pt-4 border-t border-gray-200">
          <h3 class="text-xl font-bold text-gray-800 mb-3">Adicionar Comentário</h3>
          <textarea
            v-model="newCommentContent"
            placeholder="Escreva seu comentário aqui..."
            class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
            rows="3"
            :readonly="modalMode === 'view'"
          ></textarea>
          <p v-if="commentError" class="text-red-500 text-sm mt-1">{{ commentError }}</p>
          <div class="flex justify-end mt-3">
            <button
              @click="emit('add-comment', localTask.id, newCommentContent); newCommentContent = '';"
              :disabled="modalMode === 'view'"
              class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200"
            >
              Publicar Comentário
            </button>
          </div>
        </div>
      </div>

      <!-- Botões de ação do modal-->
      <div class="flex justify-end space-x-4 mt-6">
          <button type="button" @click="emit('close')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200">
            {{ modalMode === 'view' || modalSubTab === 'comments' ? 'Fechar' : 'Cancelar' }}
          </button>
          <button
            v-if="modalMode !== 'view' && modalSubTab === 'details'"
            type="submit"
            @click="emit('save-task', localTask)"
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
  task: {
    type: Object,
    required: true,
  },
  mode: {
    type: String, // 'create', 'edit', 'view'
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
  commentError: {
    type: String,
    default: '',
  },
  projects: {
    type: Array,
    default: () => [],
  },
  users: {
    type: Array,
    default: () => [],
  },
  taskComments: {
    type: Array,
    default: () => [],
  },
});

const emit = defineEmits([
  'close',
  'save-task',
  'add-comment',
  'delete-comment',
  'fetch-comments',
]);

const authStore = useAuthStore();

const localTask = ref({});
watch(() => props.task, (newVal) => {
  localTask.value = JSON.parse(JSON.stringify(newVal));
}, { immediate: true, deep: true });

const modalMode = computed(() => props.mode);

const modalSubTab = ref('details');
const newCommentContent = ref('');

</script>
