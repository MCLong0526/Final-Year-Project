import axios from 'axios';
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('user', () => {
  const user = ref({
    username: null,
    email: null,
    roles: [],
    token: localStorage.getItem('token') || null, // Retrieve token from localStorage
    avatar: null,
    phone_number: null,
    user_id: null,
    status: null,

  });

  const deviceToken = ref(null);
  const isLoggedIn = computed(() => !!user.value.token); // Check if token exists
  const errorMessages = ref(null);

  const isAdmin = computed(() => {
    return user.value.roles.some(role => role.name === 'Admin');
  });

  function $reset() {
    user.value = {
      username: null,
      email: null,
      roles: [],
      token: null,
      avatar: null,
      phone_number: null,
      user_id: null,
      status: null,
    };
    deviceToken.value = null;
  }

  async function login(email, password) {
    try {
      const response = await axios.post('/api/auth/authenticate', { email, password });
      const userData = response.data.user;
      user.value = {
        username: userData.username,
        email: userData.email,
        roles: userData.roles,
        avatar: userData.avatar,
        phone_number: userData.phone_number,
        user_id: userData.user_id,
        status: userData.status,
        token: response.data.token,
      };

      // Save the new token to localStorage
      localStorage.setItem('token', response.data.token);

      return [response.data, null];
    } catch (error) {
      errorMessages.value = error.response.data.error;
      return [null, error];
    }
  }

  async function logout() {
    try {
      await axios.post('/api/auth/logout');
      localStorage.removeItem('token'); // Remove token from localStorage
      $reset();
    } catch (error) {
      console.error(error);
    }
  }

  async function getCurrentLoggedUser() {
    try {
      const response = await axios.get('/api/auth/get-user');
      const userData = response.data.data;
      user.value = {
        ...userData,
        token: user.value.token, // Keep the existing token
      };
    } catch (error) {
      console.error(error);
    }
  }
  

  return { user, deviceToken, $reset, login, isLoggedIn, logout, getCurrentLoggedUser, errorMessages, isAdmin };
});
