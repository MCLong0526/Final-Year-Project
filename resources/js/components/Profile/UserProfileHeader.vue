<script setup>
import { ref } from 'vue'
import profileImg2 from '@images/avatars/avatar-1.png'


const profileHeaderData = ref({
  fullName: 'User Name',
  username: 'username',
  profileImg: profileImg2,

})
</script>

<template>
  <div>
    <VCard v-if="profileHeaderData">
     
      
      <VCardText class="d-flex align-bottom flex-sm-row flex-column justify-center gap-x-6">
        <div class="d-flex h-0">
          <div style=" position: relative;display: inline-block;">
            <VAvatar
              rounded
              size="55"
              :image="profileHeaderData.profileImg"
              class="user-profile-avatar mx-auto"
            />
          </div>
          
         
        </div>
        
        <div class="user-profile-info w-100 mt-16 pt-6 pt-sm-0 mt-sm-0">
          <h5 class="text-h5 text-center text-sm-start">
            {{ profileHeaderData.fullName ?? 'Unknown' }}
          </h5>
          <div class="d-flex align-center justify-center justify-sm-space-between flex-wrap gap-4">
            <div class="d-flex flex-wrap justify-center justify-sm-start flex-grow-1">
              @{{ profileHeaderData.username }}
            </div>
            
          </div>
        </div>
      </VCardText>
    </VCard>
  </div>
</template>
