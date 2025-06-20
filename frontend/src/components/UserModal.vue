<template>
  <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-lg transform transition-all duration-300 scale-100 opacity-100">
      <h2 class="text-2xl font-bold text-gray-800 mb-6">
        {{ modalMode === 'create' ? 'Criar Novo Usuário' : (modalMode === 'edit' ? 'Editar Usuário' : 'Visualizar Usuário') }}
      </h2>
      <form @submit.prevent="emit('save-user', localUser)">
        <div class="mb-4">
          <label for="user-name" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
          <input type="text" id="user-name" v-model="localUser.name" :readonly="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label for="user-email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
          <input type="email" id="user-email" v-model="localUser.email" :readonly="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4">
          <label for="user-role" class="block text-gray-700 text-sm font-bold mb-2">Role:</label>
          <select id="user-role" v-model="localUser.role" :disabled="modalMode === 'view'" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
            <option value="user">Usuário</option>
            <option value="manager">Gestor</option>
            <option value="admin">Administrador</option>
          </select>
        </div>
        <div class="mb-4" v-if="modalMode !== 'view'">
          <label for="user-password" class="block text-gray-700 text-sm font-bold mb-2">Senha (deixe em branco para não alterar):</label>
          <input type="password" id="user-password" v-model="localUser.password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4" v-if="modalMode !== 'view'">
          <label for="user-password-confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Senha:</label>
          <input type="password" id="user-password-confirmation" v-model="localUser.password_confirmation" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
        </div>
        <div class="mb-4" v-if="modalMode !== 'create'">
          <label for="user-is-active" class="block text-gray-700 text-sm font-bold mb-2">Status Ativo:</label>
          <input type="checkbox" id="user-is-active" v-model="localUser.is_active" :disabled="modalMode === 'view'" class="form-checkbox h-5 w-5 text-indigo-600">
          <span class="ml-2 text-gray-700">{{ localUser.is_active ? 'Ativo' : 'Inativo' }}</span>
        </div>

        <div v-if="formError" class="text-red-500 text-sm mb-4">{{ formError }}</div>
        <div class="flex justify-end space-x-4">
          <button type="button" @click="emit('close')" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200">
            {{ modalMode === 'view' ? 'Fechar' : 'Cancelar' }}
          </button>
          <button v-if="modalMode !== 'view'" type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">Salvar</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const props = defineProps({
  user: {
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
});

const emit = defineEmits(['close', 'save-user']);

// Copia local do objeto user para edição no modal
const localUser = ref({});
watch(() => props.user, (newVal) => {
  localUser.value = JSON.parse(JSON.stringify(newVal));
}, { immediate: true, deep: true });

const modalMode = computed(() => props.mode); // Modo do modal

// Métodos de badge
const getUserRoleBadgeClass = (role) => {
  switch (role) {
    case 'admin': return 'bg-purple-200 text-purple-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'manager': return 'bg-blue-200 text-blue-800 px-2 py-1 rounded-full text-xs font-semibold';
    case 'user': return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
    default: return 'bg-gray-200 text-gray-800 px-2 py-1 rounded-full text-xs font-semibold';
  }
};
</script>
