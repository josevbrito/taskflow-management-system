import { defineStore } from 'pinia';
import axios from 'axios';
import router from '../router';

const API_URL = import.meta.env.VITE_APP_API_URL || 'http://localhost:8000/api';

const api = axios.create({
  baseURL: API_URL,
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json',
  },
});

export const useAuthStore = defineStore('auth', {
  state: () => {
    let user = null;
    const storedUser = localStorage.getItem('user');
    if (storedUser) {
      try {
        user = JSON.parse(storedUser);
      } catch (e) {
        console.error('Erro ao fazer parse do usuário no localStorage:', e);
        localStorage.removeItem('user');
      }
    }

    return {
      user: user,
      token: localStorage.getItem('access_token') || null,
      isAuthenticated: !!localStorage.getItem('access_token'),
      needs2Fa: false,
    };
  },
  getters: {
    isLoggedIn: (state) => state.isAuthenticated,
    authToken: (state) => state.token,
    currentUser: (state) => state.user,
    requires2Fa: (state) => state.needs2Fa,
  },
  actions: {
    async login(email, password, oneTimePassword = null) {
      try {
        const payload = { email, password };
        if (oneTimePassword) {
          payload.one_time_password = oneTimePassword;
        }

        const response = await api.post('/login', payload);

        if (response.data.needs_2fa) {
          this.needs2Fa = true;
          this.user = response.data.user;
          return { success: true, needs2Fa: true };
        } else {
          this.token = response.data.access_token;
          this.user = response.data.user;
          this.isAuthenticated = true;
          this.needs2Fa = false;

          localStorage.setItem('access_token', this.token);
          localStorage.setItem('user', JSON.stringify(this.user));

          api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
          console.log('AuthStore: Token set after login:', this.token);
          console.log('AuthStore: api.defaults.headers.common.Authorization after login:', api.defaults.headers.common['Authorization']);

          router.push('/');
          return { success: true, needs2Fa: false };
        }
      } catch (error) {
        console.error('Erro de login:', error.response?.data || error.message);
        throw error;
      }
    },

    async verify2Fa(email, oneTimePassword) {
      try {
        const response = await api.post('/2fa/verify', { email, one_time_password: oneTimePassword });

        this.token = response.data.access_token;
        this.user = response.data.user;
        this.isAuthenticated = true;
        this.needs2Fa = false;

        localStorage.setItem('access_token', this.token);
        localStorage.setItem('user', JSON.stringify(this.user));

        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        console.log('AuthStore: Token set after 2FA verification:', this.token);
        console.log('AuthStore: api.defaults.headers.common.Authorization after 2FA:', api.defaults.headers.common['Authorization']);

        router.push('/');
        return { success: true };
      } catch (error) {
        console.error('Erro de verificação 2FA:', error.response?.data || error.message);
        throw error;
      }
    },

    async logout() {
      try {
        if (this.token) {
           await api.post('/logout');
        }
      } catch (error) {
        console.error('Erro ao fazer logout no backend:', error.response?.data || error.message);
      } finally {
        this.token = null;
        this.user = null;
        this.isAuthenticated = false;
        this.needs2Fa = false;

        localStorage.removeItem('access_token');
        localStorage.removeItem('user');

        delete api.defaults.headers.common['Authorization'];
        console.log('AuthStore: Token removed after logout.');

        router.push('/login');
      }
    },

    initializeAuth() {
      if (this.token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${this.token}`;
        console.log('AuthStore: Token initialized from localStorage:', this.token);
        console.log('AuthStore: api.defaults.headers.common.Authorization after init:', api.defaults.headers.common['Authorization']);
      } else {
        console.log('AuthStore: No token found in localStorage during initialization.');
      }
    },
  },
});

export { api };
