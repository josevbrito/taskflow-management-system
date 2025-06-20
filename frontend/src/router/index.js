import { createRouter, createWebHistory } from 'vue-router';
import LoginView from '../views/LoginView.vue';
import RegisterView from '../views/RegisterView.vue';
import DashboardView from '../views/DashboardView.vue';
import TasksView from '../views/TasksView.vue';
import ProjectsView from '../views/ProjectsView.vue';
import ProfileView from '../views/ProfileView.vue';
import AdminPanelView from '../views/AdminPanelView.vue';
import NotFoundView from '../views/NotFoundView.vue';
import { useAuthStore } from '../stores/auth';

const routes = [
  {
    path: '/login',
    name: 'login',
    component: LoginView,
    meta: { guest: true }
  },
  {
    path: '/register',
    name: 'register',
    component: RegisterView,
    meta: { guest: true }
  },
  {
    path: '/',
    name: 'dashboard',
    component: DashboardView,
    meta: { requiresAuth: true }
  },
  {
    path: '/tasks',
    name: 'tasks',
    component: TasksView,
    meta: { requiresAuth: true }
  },
  {
    path: '/projects',
    name: 'projects',
    component: ProjectsView,
    meta: { requiresAuth: true }
  },
  {
    path: '/profile',
    name: 'profile',
    component: ProfileView,
    meta: { requiresAuth: true }
  },
  {
    path: '/admin-panel',
    name: 'admin-panel',
    component: AdminPanelView,
    meta: { requiresAuth: true, requiresAdmin: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: NotFoundView
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
});

// Navegação de guarda para autenticação e autorização (roles)
router.beforeEach((to, from, next) => {
  const authStore = useAuthStore();

  const loggedIn = authStore.isLoggedIn;
  const userRole = authStore.currentUser?.role;

  if (to.matched.some(record => record.meta.requiresAuth) && !loggedIn) {
    // Se a rota exige autenticação e o usuário não está logado
    next('/login');
  } else if (to.matched.some(record => record.meta.guest) && loggedIn) {
    // Se a rota é para convidados (login/registro) e o usuário já está logado
    next('/');
  } else if (to.matched.some(record => record.meta.requiresAdmin) && userRole !== 'admin') {
    // Se a rota exige role de admin e o usuário não é admin
    // Redireciona para o dashboard ou uma página de acesso negado
    next('/'); // Ou '/access-denied' se tiver uma página específica
    alert('Acesso negado. Apenas administradores podem acessar esta página.'); // Apenas para feedback rápido
  }
  else {
    next();
  }
});

export default router;
