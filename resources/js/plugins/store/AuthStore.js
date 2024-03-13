import axios from 'axios';
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('user', () => {
  const user = ref({
    username: null,
    email: null, 
    roles: [],
    token: null, // Add token property to store the user's token
  });

  const roles = computed(() => user.value.roles);
  const isLoggedIn = ref(false);

  function $reset() {
    user.value = {
      username: null,
      email: null, 
      roles: [],
      token: null,
    };
    isLoggedIn.value = false;
  }

  async function login(email, password) {
    try {
      const response = await axios.post('/api/auth/authenticate', { email, password });
      const userData = response.data;

      user.value = {
        username: userData.username,
        email: userData.user.email,
        roles: userData.user.roles,
        token: userData.token,
      };
      isLoggedIn.value = true;


      return [userData, null];
    } catch (error) {
      return [null, error];
    }
  }

  async function logout() {
    try {
      await axios.post('/api/auth/logout')

        $reset();
    } 
    catch(error) {
      console.error(error)
    }
  }

  return { user, roles, $reset, login, isLoggedIn, logout};
});
