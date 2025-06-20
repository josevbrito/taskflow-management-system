<template>
  <div class="p-6 bg-white rounded-lg shadow-xl">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Meu Perfil</h1>

    <div v-if="user" class="grid grid-cols-1 md:grid-cols-2 gap-8">
      <div class="bg-gray-50 p-8 rounded-lg shadow-md border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Informações Pessoais</h2>

        <div v-if="!isEditing">
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-1">Nome:</label>
            <p class="text-gray-900 text-lg">{{ user.name }}</p>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-1">Email:</label>
            <p class="text-gray-900 text-lg">{{ user.email }}</p>
          </div>
          <div class="mb-4">
            <label class="block text-gray-700 text-sm font-semibold mb-1">Função:</label>
            <p class="text-gray-900 text-lg capitalize">{{ user.role }}</p>
          </div>
          <div class="flex justify-end mt-6 pt-4 border-t border-gray-200">
            <button @click="startEditing" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200">
              Editar Perfil
            </button>
          </div>
        </div>

        <div v-else>
          <form @submit.prevent="saveProfile">
            <div class="mb-4">
              <label for="edit-name" class="block text-gray-700 text-sm font-bold mb-2">Nome:</label>
              <input type="text" id="edit-name" v-model="editableUser.name" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
              <label for="edit-email" class="block text-gray-700 text-sm font-bold mb-2">Email:</label>
              <input type="email" id="edit-email" v-model="editableUser.email" required class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
              <label for="edit-password" class="block text-gray-700 text-sm font-bold mb-2">Nova Senha (deixe em branco para não alterar):</label>
              <input type="password" id="edit-password" v-model="editableUser.password" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <div class="mb-4">
              <label for="edit-password-confirmation" class="block text-gray-700 text-sm font-bold mb-2">Confirmar Nova Senha:</label>
              <input type="password" id="edit-password-confirmation" v-model="editableUser.password_confirmation" class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md">
            </div>
            <p v-if="editProfileError" class="text-red-500 text-sm mb-4">{{ editProfileError }}</p>
            <p v-if="editProfileSuccess" class="text-green-500 text-sm mb-4">{{ editProfileSuccess }}</p>

            <div class="flex justify-end space-x-4 mt-6 pt-4 border-t border-gray-200">
              <button type="button" @click="cancelEditing" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md transition-colors duration-200">
                Cancelar
              </button>
              <button type="submit" :disabled="isSavingProfile" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-200">
                <span v-if="isSavingProfile">Salvando...</span>
                <span v-else>Salvar Alterações</span>
              </button>
            </div>
          </form>
        </div>
      </div>

      <div class="bg-gray-50 p-8 rounded-lg shadow-md border border-gray-200">
        <h2 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-3">Segurança (Autenticação de 2 Fatores)</h2>

        <div v-if="!user.is_2fa_enabled" class="mb-6 p-4 border border-blue-300 bg-blue-50 rounded-md">
          <p class="text-blue-800 font-medium mb-4">A autenticação de 2 fatores (2FA) está desabilitada.</p>
          <p class="text-blue-700 text-sm mb-4">Para habilitar, clique no botão e configure seu aplicativo autenticador com o código secreto fornecido.</p>
          <button @click="enable2Fa" :disabled="isLoading2Fa" class="bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200">
            <span v-if="isLoading2Fa">Gerando Código...</span>
            <span v-else>Habilitar 2FA</span>
          </button>
        </div>

        <div v-else class="mb-6 p-4 border border-green-300 bg-green-50 rounded-md">
          <p class="text-green-800 font-semibold mb-4">A autenticação de 2 fatores (2FA) está Habilitada.</p>
          <p class="text-green-700 text-sm mb-4">Sua conta está protegida com 2FA. Você pode desabilitar a qualquer momento.</p>
          <button @click="showDisable2FaModal = true" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md shadow-md transition-colors duration-200">
            Desabilitar 2FA
          </button>
        </div>

        <div v-if="google2FaSecret" class="mt-6 p-4 bg-white border border-gray-300 rounded-lg text-center shadow-inner">
          <p class="text-gray-700 font-semibold mb-3">Seu Código Secreto 2FA (Guarde-o em local seguro!):</p>
          <p class="text-indigo-600 font-mono text-xl select-all break-all p-2 bg-gray-100 rounded-md">{{ google2FaSecret }}</p>
          <p class="text-gray-600 text-sm mt-3">Insira este código manualmente no seu aplicativo autenticador (ex: Google Authenticator, Authy).</p>
        </div>

        <p v-if="errorMessage" class="text-red-500 text-sm mt-4 text-center">{{ errorMessage }}</p>
        <p v-if="successMessage" class="text-green-500 text-sm mt-4 text-center">{{ successMessage }}</p>
      </div>
    </div>

    <!-- Modal para desabilitar 2FA -->
    <div v-if="showDisable2FaModal" class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center z-50 p-4">
      <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-sm">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Desabilitar 2FA</h2>
        <p class="text-gray-700 mb-4">Para desabilitar a autenticação de 2 fatores, por favor, insira sua senha:</p>
        <div class="mb-4">
          <label for="password-disable-2fa" class="sr-only">Senha</label>
          <input
            type="password"
            id="password-disable-2fa"
            v-model="passwordToDisable2Fa"
            required
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            placeholder="Sua Senha"
          >
        </div>
        <p v-if="disable2FaError" class="text-red-500 text-sm mb-4">{{ disable2FaError }}</p>
        <div class="flex justify-end space-x-4">
          <button type="button" @click="showDisable2FaModal = false; passwordToDisable2Fa = ''; disable2FaError = '';" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-md">Cancelar</button>
          <button type="button" @click="disable2Fa" :disabled="isLoading2Fa" class="bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-md">
            <span v-if="isLoading2Fa">Desabilitando...</span>
            <span v-else>Confirmar</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore, api } from '../stores/auth';

const authStore = useAuthStore();

const user = ref(null);
const editableUser = ref({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});
const isEditing = ref(false);
const isSavingProfile = ref(false);
const editProfileError = ref('');
const editProfileSuccess = ref('');

const qrCodeUrl = ref('');
const google2FaSecret = ref('');
const errorMessage = ref('');
const successMessage = ref('');
const isLoading2Fa = ref(false);

const showDisable2FaModal = ref(false);
const passwordToDisable2Fa = ref('');
const disable2FaError = ref('');

onMounted(() => {
  fetchUserProfile();
});

const fetchUserProfile = async () => {
  try {
    const response = await api.get('/user');
    user.value = response.data.user;
    editableUser.value.name = user.value.name;
    editableUser.value.email = user.value.email;

    // Se o 2FA já estiver habilitado e o segredo estiver no perfil do usuário, exibi-lo
    if (user.value.is_2fa_enabled && user.value.google2fa_secret) {
        google2FaSecret.value = user.value.google2fa_secret;
    }

    authStore.user = response.data.user;
  } catch (error) {
    console.error('Erro ao buscar perfil do usuário:', error.response?.data || error.message);
    errorMessage.value = 'Não foi possível carregar as informações do perfil.';
  }
};

const startEditing = () => {
  isEditing.value = true;
  editProfileError.value = '';
  editProfileSuccess.value = '';
  editableUser.value.password = '';
  editableUser.value.password_confirmation = '';
};

const cancelEditing = () => {
  isEditing.value = false;
  editProfileError.value = '';
  editProfileSuccess.value = '';
  editableUser.value.name = user.value.name;
  editableUser.value.email = user.value.email;
};

const saveProfile = async () => {
  editProfileError.value = '';
  editProfileSuccess.value = '';
  isSavingProfile.value = true;
  try {
    const payload = {
      name: editableUser.value.name,
      email: editableUser.value.email,
    };

    if (editableUser.value.password) {
      payload.password = editableUser.value.password;
      payload.password_confirmation = editableUser.value.password_confirmation;
    }

    const response = await api.put(`/users/${user.value.id}`, payload);

    user.value = response.data;
    authStore.user = response.data;
    isEditing.value = false;
    editProfileSuccess.value = 'Perfil atualizado com sucesso!';

    editableUser.value.password = '';
    editableUser.value.password_confirmation = '';

  } catch (error) {
    console.error('Erro ao salvar perfil:', error.response?.data || error.message);
    if (error.response && error.response.data && error.response.data.errors) {
      editProfileError.value = Object.values(error.response.data.errors).flat().join(' ');
    } else {
      editProfileError.value = 'Erro ao salvar perfil. Tente novamente.';
    }
  } finally {
    isSavingProfile.value = false;
  }
};

const enable2Fa = async () => {
  errorMessage.value = '';
  successMessage.value = '';
  isLoading2Fa.value = true;
  try {
    const response = await api.post('/user/2fa/enable');
    // qrCodeUrl.value = response.data.qr_code_url;
    google2FaSecret.value = response.data.secret;
    successMessage.value = 'Código secreto gerado com sucesso. Guarde-o e configure seu aplicativo!';

    user.value.is_2fa_enabled = true;
    user.value.google2fa_secret = google2FaSecret.value;
    authStore.user = { ...authStore.user, is_2fa_enabled: true, google2fa_secret: google2FaSecret.value };
  } catch (error) {
    console.error('Erro ao habilitar 2FA:', error.response?.data || error.message);
    errorMessage.value = 'Erro ao habilitar 2FA. Tente novamente.';
  } finally {
    isLoading2Fa.value = false;
  }
};

const disable2Fa = async () => {
  disable2FaError.value = '';
  successMessage.value = '';
  isLoading2Fa.value = true;
  try {
    await api.post('/user/2fa/disable', { password: passwordToDisable2Fa.value });
    successMessage.value = '2FA desabilitado com sucesso!';
    qrCodeUrl.value = '';
    google2FaSecret.value = '';

    user.value.is_2fa_enabled = false;
    user.value.google2fa_secret = null;
    authStore.user = { ...authStore.user, is_2fa_enabled: false, google2fa_secret: null };
    showDisable2FaModal.value = false;
    passwordToDisable2Fa.value = '';
  } catch (error) {
    console.error('Erro ao desabilitar 2FA:', error.response?.data || error.message);
    if (error.response && error.response.data && error.response.data.message) {
      disable2FaError.value = error.response.data.message;
    } else {
      disable2FaError.value = 'Erro ao desabilitar 2FA. Senha incorreta ou erro no servidor.';
    }
  } finally {
    isLoading2Fa.value = false;
  }
};

const formatDate = (dateString) => {
  const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
  return new Date(dateString).toLocaleDateString('pt-BR', options);
};
</script>
