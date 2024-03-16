<script setup>
const user = ref([]);
import axios from 'axios';

const hasError = ref(false);

// get the authenticated user
const getUser = async () => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Token not found');
    }

    const response = await axios.get('/api/auth/get-user-by-token', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    user.value = response.data.user;
  } catch (error) {
    hasError.value = true;
    //await getUser2();
    console.error(error);
  }
};

const getUser2 = () => {
  if(hasError.value==true){
    window.location.reload();
  }
}
getUser();


</script>

<template>
  <div>
    <VCard>
     
      
      <VCardText class="d-flex align-bottom flex-sm-row flex-column justify-center gap-x-6">
        <div class="d-flex h-0">
          <div style=" position: relative;display: inline-block;">
            <VAvatar
              rounded
              size="55"
              :image="user.avatar"
              class="user-profile-avatar mx-auto"
            />
          </div>
          
         
        </div>
        
        <div class="user-profile-info w-100 mt-16 pt-6 pt-sm-0 mt-sm-0">
          <h5 class="text-h5 text-center text-sm-start">
            {{ user.username ?? 'Unknown' }}
          </h5>
          <div class="d-flex align-center justify-center justify-sm-space-between flex-wrap gap-4">
            <div class="d-flex flex-wrap justify-center justify-sm-start flex-grow-1">
              @{{ user.email }}
            </div>
            
          </div>
        </div>
      </VCardText>
    </VCard>
  </div>
</template>
