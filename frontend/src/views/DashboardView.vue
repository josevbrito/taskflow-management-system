<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Visão Geral do Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
      <!-- Card: Total de Projetos -->
      <div class="bg-blue-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-blue-800">Projetos Totais</h3>
          <p class="text-3xl font-extrabold text-blue-900">{{ stats.total_projects }}</p>
        </div>
        <svg class="h-12 w-12 text-blue-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m7 0V5a2 2 0 012-2h2a2 2 0 012 2v6m-6 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v6"></path></svg>
      </div>

      <!-- Card: Tarefas Pendentes -->
      <div class="bg-yellow-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-yellow-800">Tarefas Pendentes</h3>
          <p class="text-3xl font-extrabold text-yellow-900">{{ stats.pending_tasks }}</p>
        </div>
        <svg class="h-12 w-12 text-yellow-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>

      <!-- Card: Tarefas Concluídas -->
      <div class="bg-green-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-green-800">Tarefas Concluídas</h3>
          <p class="text-3xl font-extrabold text-green-900">{{ stats.completed_tasks }}</p>
        </div>
        <svg class="h-12 w-12 text-green-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>
    </div>

    <!-- Seção de Atividades Recentes -->
    <div class="bg-white p-6 rounded-lg shadow-xl">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Atividades Recentes</h2>
      <div v-if="stats.recent_activities && stats.recent_activities.length > 0">
        <ul class="divide-y divide-gray-200">
          <li v-for="activity in stats.recent_activities" :key="activity._id" class="py-4">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              <div>
                <p class="text-gray-700 text-base font-medium">{{ formatActivity(activity) }}</p>
                <p class="text-gray-500 text-sm mt-1">{{ formatDate(activity.created_at) }}</p>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <div v-else class="text-gray-500 text-center py-8">
        Nenhuma atividade recente encontrada.
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

const stats = ref({
  total_projects: 0,
  completed_projects: 0,
  total_tasks: 0,
  completed_tasks: 0,
  pending_tasks: 0,
  recent_activities: [],
});

onMounted(() => {
  fetchDashboardStats();
});

const fetchDashboardStats = async () => {
  try {
    const response = await axios.get(`${API_URL}/dashboard/stats`, {
      headers: {
        Authorization: `Bearer ${authStore.authToken}`,
      },
    });
    stats.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar estatísticas do dashboard:', error.response?.data || error.message);
  }
};

const formatActivity = (activity) => {
  let message = `Usuário ${activity.user_id} `;
  switch (activity.action) {
    case 'project_created':
      message += `criou o projeto "${activity.details.name}".`;
      break;
    case 'project_updated':
      message += `atualizou o projeto "${activity.details.name}".`;
      break;
    case 'project_deleted':
      message += `deletou o projeto "${activity.details.name}".`;
      break;
    case 'task_created':
      message += `criou a tarefa "${activity.details.title}" no projeto "${activity.details.project_name}".`;
      break;
    case 'task_updated':
      message += `atualizou a tarefa "${activity.details.title}" no projeto "${activity.details.project_name}".`;
      break;
    case 'task_deleted':
      message += `deletou a tarefa "${activity.details.title}" no projeto "${activity.details.project_name}".`;
      break;
    case 'user_login':
        message += `fez login com sucesso.`;
        break;
    case 'user_logout':
        message += `fez logout.`;
        break;
    default:
      message += `realizou uma ação de ${activity.action} em ${activity.entity_type} (ID: ${activity.entity_id}).`;
      break;
  }
  return message;
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('pt-BR', options);
};
</script>

