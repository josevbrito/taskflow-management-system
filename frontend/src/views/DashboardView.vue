<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Visão Geral do Dashboard</h1>

    <!-- Secção de Estatísticas Gerais -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 mb-8">
      <!-- Card: Total de Projetos -->
      <div class="bg-blue-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-blue-800">Projetos Totais</h3>
          <p class="text-3xl font-extrabold text-blue-900">{{ dashboardData.general_stats.total_projects }}</p>
        </div>
        <svg class="h-12 w-12 text-blue-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m7 0V5a2 2 0 012-2h2a2 2 0 012 2v6m-6 0V5a2 2 0 00-2-2H9a2 2 0 00-2 2v6"></path></svg>
      </div>

      <!-- Card: Projetos Concluídos -->
      <div class="bg-green-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-green-800">Projetos Concluídos</h3>
          <p class="text-3xl font-extrabold text-green-900">{{ dashboardData.general_stats.completed_projects }}</p>
        </div>
        <svg class="h-12 w-12 text-green-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>

      <!-- Card: Tarefas Totais -->
      <div class="bg-purple-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-purple-800">Tarefas Totais</h3>
          <p class="text-3xl font-extrabold text-purple-900">{{ dashboardData.general_stats.total_tasks }}</p>
        </div>
        <svg class="h-12 w-12 text-purple-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
      </div>

      <!-- Card: Tarefas Atrasadas -->
      <div class="bg-red-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-red-800">Tarefas Atrasadas</h3>
          <p class="text-3xl font-extrabold text-red-900">{{ dashboardData.general_stats.overdue_tasks }}</p>
        </div>
        <svg class="h-12 w-12 text-red-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>

      <!-- Card: Tarefas Pendentes -->
      <div class="bg-yellow-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-yellow-800">Tarefas Pendentes</h3>
          <p class="text-3xl font-extrabold text-yellow-900">{{ dashboardData.general_stats.pending_tasks }}</p>
        </div>
        <svg class="h-12 w-12 text-yellow-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>

      <!-- Card: Tarefas Em Progresso -->
      <div class="bg-blue-200 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-blue-900">Tarefas Em Progresso</h3>
          <p class="text-3xl font-extrabold text-blue-900">{{ dashboardData.general_stats.in_progress_tasks }}</p>
        </div>
        <svg class="h-12 w-12 text-blue-600 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
      </div>

      <!-- Card: Tarefas Concluídas -->
      <div class="bg-emerald-100 p-6 rounded-lg shadow-md flex items-center justify-between">
        <div>
          <h3 class="text-xl font-semibold text-emerald-800">Tarefas Concluídas</h3>
          <p class="text-3xl font-extrabold text-emerald-900">{{ dashboardData.general_stats.completed_tasks }}</p>
        </div>
        <svg class="h-12 w-12 text-emerald-500 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      </div>

      <!-- Card: Percentagem de Conclusão de Tarefas (Ajustado para caber na grade) -->
      <div class="bg-indigo-100 p-6 rounded-lg shadow-md flex items-center justify-between col-span-1">
        <div>
          <h3 class="text-xl font-semibold text-indigo-800">Conclusão Geral (%)</h3>
          <p class="text-3xl font-extrabold text-indigo-900">{{ dashboardData.general_stats.progress_percentage_tasks }}%</p>
        </div>
        <div class="w-24 h-24 relative">
          <svg class="w-full h-full" viewBox="0 0 100 100">
            <circle class="text-gray-300 stroke-current" stroke-width="10" cx="50" cy="50" r="40" fill="transparent"></circle>
            <circle class="text-indigo-500 progress-ring__circle stroke-current"
                    stroke-width="10" cx="50" cy="50" r="40" fill="transparent"
                    :stroke-dasharray="2 * Math.PI * 40"
                    :stroke-dashoffset="2 * Math.PI * 40 - (2 * Math.PI * 40 * dashboardData.general_stats.progress_percentage_tasks / 100)"
                    transform="rotate(-90 50 50)"></circle>
          </svg>
          <span class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 font-bold text-lg text-indigo-800">{{ dashboardData.general_stats.progress_percentage_tasks }}%</span>
        </div>
      </div>
    </div>

    <!-- Secção de Gráficos e Ranking -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
      <!-- Gráfico: Distribuição de Projetos por Status -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Projetos por Status</h2>
        <div v-if="projectStatusChartData.labels.length" class="h-64">
          <Pie :data="projectStatusChartData" :options="chartOptions" />
        </div>
        <p v-else class="text-gray-500 text-center py-8">Dados de projetos por status não disponíveis.</p>
      </div>

      <!-- Gráfico: Distribuição de Tarefas por Status -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Tarefas por Status</h2>
        <div v-if="taskStatusChartData.labels.length" class="h-64">
          <Doughnut :data="taskStatusChartData" :options="chartOptions" />
        </div>
        <p v-else class="text-gray-500 text-center py-8">Dados de tarefas por status não disponíveis.</p>
      </div>

      <!-- Gráfico: Distribuição de Tarefas por Prioridade -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Tarefas por Prioridade</h2>
        <div v-if="taskPriorityChartData.labels.length" class="h-64">
          <Bar :data="taskPriorityChartData" :options="barChartOptions" />
        </div>
        <p v-else class="text-gray-500 text-center py-8">Dados de tarefas por prioridade não disponíveis.</p>
      </div>

      <!-- Ranking de Usuários por Tarefas Atribuídas -->
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Ranking de Usuários (Tarefas)</h2>
        <div v-if="dashboardData.user_task_ranking && dashboardData.user_task_ranking.length">
          <ul class="divide-y divide-gray-200">
            <li v-for="(user, index) in dashboardData.user_task_ranking" :key="user.id" class="py-3 flex justify-between items-center">
              <div class="flex items-center">
                <span class="text-lg font-bold text-gray-700 mr-3">#{{ index + 1 }}</span>
                <p class="text-gray-900 font-medium">{{ user.name }}</p>
              </div>
              <span class="text-indigo-600 font-semibold">{{ user.tasks_count }} tarefas</span>
            </li>
          </ul>
        </div>
        <p v-else class="text-gray-500 text-center py-8">Ranking de usuários não disponível ou sem tarefas.</p>
      </div>
    </div>

    <!-- Secção de Atividades Recentes (Tarefas Ativas) -->
    <div class="bg-white p-6 rounded-lg shadow-xl">
      <h2 class="text-2xl font-bold text-gray-800 mb-4">Atividades Recentes (Tarefas Ativas)</h2>
      <div v-if="dashboardData.recent_tasks && dashboardData.recent_tasks.length > 0">
        <ul class="divide-y divide-gray-200">
          <li v-for="task in dashboardData.recent_tasks" :key="task.id" class="py-4">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
              <div>
                <p class="text-gray-700 text-base font-medium">{{ task.title }} ({{ task.status }})</p>
                <p class="text-gray-500 text-sm mt-1">Projeto: {{ task.project?.name || 'N/A' }} | Atribuída a: {{ task.assigned_user?.name || 'N/A' }} | Última Atualização: {{ formatDate(task.updated_at) }}</p>
              </div>
            </div>
          </li>
        </ul>
      </div>
      <div v-else class="text-gray-500 text-center py-8">
        Nenhuma tarefa ativa recente encontrada.
      </div>
    </div>

    <p v-if="errorMessage" class="text-red-500 text-sm text-center">{{ errorMessage }}</p>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../stores/auth';
import { api } from '../stores/auth';
import { Pie, Doughnut, Bar } from 'vue-chartjs';
import { Chart as ChartJS, Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, BarElement } from 'chart.js';

ChartJS.register(Title, Tooltip, Legend, ArcElement, CategoryScale, LinearScale, BarElement);

const authStore = useAuthStore();
const dashboardData = ref({
  general_stats: {
    total_projects: 0,
    completed_projects: 0,
    total_tasks: 0,
    completed_tasks: 0,
    pending_tasks: 0,
    in_progress_tasks: 0,
    overdue_tasks: 0,
    progress_percentage_tasks: 0,
  },
  project_status_breakdown: [],
  task_status_breakdown: [],
  task_priority_breakdown: [],
  user_task_ranking: [],
  recent_tasks: [],
});
const errorMessage = ref('');

onMounted(() => {
  fetchDashboardStats();
});

const fetchDashboardStats = async () => {
  try {
    const response = await api.get('/dashboard/stats');
    dashboardData.value = response.data;
  } catch (error) {
    console.error('Erro ao buscar estatísticas do dashboard:', error.response?.data || error.message);
    if (error.response?.status === 401) {
      errorMessage.value = 'Sessão expirada ou não autorizado. Faça login novamente.';
      authStore.logout();
    } else {
      errorMessage.value = 'Erro ao buscar estatísticas do dashboard. Tente novamente.';
    }
  }
};

// Opções globais para gráficos de pizza/doughnut
const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      position: 'right',
    },
    tooltip: {
      callbacks: {
        label: function(context) {
          let label = context.label || '';
          if (label) {
            label += ': ';
          }
          if (context.parsed !== null) {
            label += context.parsed;
          }
          return label;
        }
      }
    }
  }
};

// Opções para gráficos de barra
const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true,
      ticks: {
        precision: 0
      }
    }
  }
};


// Computed properties para formatar dados para os gráficos
const projectStatusChartData = computed(() => {
  const labels = dashboardData.value.project_status_breakdown.map(item => item.status);
  const data = dashboardData.value.project_status_breakdown.map(item => item.count);
  const backgroundColors = data.map((_, index) => {
    const status = labels[index];
    switch(status) {
      case 'pendente': return '#F59E0B';
      case 'em_progresso': return '#3B82F6';
      case 'concluido': return '#10B981';
      case 'cancelado': return '#EF4444';
      default: return '#6B7280';
    }
  });

  return {
    labels: labels,
    datasets: [{
      backgroundColor: backgroundColors,
      data: data
    }]
  };
});

const taskStatusChartData = computed(() => {
  const labels = dashboardData.value.task_status_breakdown.map(item => item.status);
  const data = dashboardData.value.task_status_breakdown.map(item => item.count);
  const backgroundColors = data.map((_, index) => {
    const status = labels[index];
    switch(status) {
      case 'pendente': return '#FCD34D';
      case 'em_progresso': return '#60A5FA';
      case 'concluido': return '#34D399';
      case 'cancelado': return '#F87171';
      default: return '#9CA3AF';
    }
  });

  return {
    labels: labels,
    datasets: [{
      backgroundColor: backgroundColors,
      data: data
    }]
  };
});

const taskPriorityChartData = computed(() => {
  const labels = dashboardData.value.task_priority_breakdown.map(item => item.priority);
  const data = dashboardData.value.task_priority_breakdown.map(item => item.count);
  const backgroundColors = data.map((_, index) => {
    const priority = labels[index];
    switch(priority) {
      case 'baixa': return '#9CA3AF';
      case 'media': return '#60A5FA';
      case 'alta': return '#F87171';
      default: return '#6B7280';
    }
  });

  return {
    labels: labels,
    datasets: [{
      label: 'Número de Tarefas',
      backgroundColor: backgroundColors,
      data: data
    }]
  };
});

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('pt-BR', options);
};
</script>
