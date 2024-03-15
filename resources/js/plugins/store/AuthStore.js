import axios from 'axios';
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('user', () => {
  
  const user = ref({
    username: null,
    email: null, 
    roles: [],
    token: null, // Add token property to store the user's token
    avatar: null,
    phone_number: null,
    user_id: null,
    status:null,

  });

  const roles = computed(() => user.value.roles);
  const isLoggedIn = ref(false);

  function $reset() {
    user.value = {
      username: null,
      email: null, 
      roles: [],
      token: null,
      avatar: null,
      phone_number: null,
      user_id: null,
      status:null,
    };
    isLoggedIn.value = false;
  }


  async function login(email, password) {
    try {
      const response = await axios.post('/api/auth/authenticate', { email, password });
      const userData = response.data;
  
      user.value = {
        username: userData.user.username,
        email: userData.user.email,
        roles: userData.user.roles,
        avatar: userData.user.avatar,
        phone_number: userData.user.phone_number,
        user_id: userData.user.user_id,
        status: userData.user.status,
        token: userData.token,
      };
      // Remove the old token from localStorage
      localStorage.removeItem('token');
      localStorage.removeItem('user');
      // Save the new token to localStorage
      localStorage.setItem('token', userData.token);

      isLoggedIn.value = true;
  
      return [userData, null];
    } catch (error) {
      return [null, error];
    }
  }
  

  //save token to user table
  async function saveToken(token) {
    try {
      const response = await axios.post('/api/auth/save-token', { token });
      
      if (response.status === 200) {
        return [response.data, null];
      } else {
        throw new Error('Failed to save token');
      }
    } catch (error) {
      return [null, error];
    }
  }
  

  async function logout() {
    try {
      await axios.post('/api/auth/logout')
    // Remove the old token from localStorage
      localStorage.removeItem('token');
      isLoggedIn.value = false;
      localStorage.removeItem('user');
      //resetAllStoreState()
      $reset()
    } 
    catch(error) {
      console.error(error)
    }
  }

  async function getCurrentLoggedUser() {
    try {
      if(isLoggedIn.value === false){
        const response = await axios.get('/api/auth/get-user')
        user.value = response.data.data
        isLoggedIn.value = true
        
      }
    } 
    catch(error) {
      console.error(error)
    }
  }



  return { user, roles, $reset, login, isLoggedIn, logout, getCurrentLoggedUser};
});
