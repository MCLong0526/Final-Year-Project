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
    password: null,

  });

  const deviceToken = ref(null)
  const isLoggedIn = ref(false);
  const errorMessages = ref(null);

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
      password: null,
    };
    isLoggedIn.value = false;
    deviceToken.value = null;
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
        password: userData.user.password,
      };

      // Save the new token to localStorage
      localStorage.setItem('token', userData.token);

      isLoggedIn.value = true;

  
  
      return [userData, null];
    } catch (error) {
      errorMessages.value = error.response.data.error;
      return [null, error];
    }
  }
  

  async function logout() {
    try {
      await axios.post('/api/auth/logout')
      isLoggedIn.value = false;
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



  return { user,deviceToken, $reset, login, isLoggedIn, logout, getCurrentLoggedUser, errorMessages};
});
