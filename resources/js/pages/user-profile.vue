<template>
  <div style="display: flex;">
    <div style="inline-size: 50%;">
      <UserProfileHeader class="mb-5" />
    </div>
    <VSpacer class="mx-5" />


    <div style="inline-size: 50%;">
      <VTabs
        v-model="currentTab"
        class="v-tabs-pill justify-end"
      >
      
        <VTab 
          value="security"
          prepend-icon="ri-lock-unlock-line">
          Security
        </VTab>
        <VTab 
          value="notification"
          prepend-icon="ri-notification-line">
          Notification
        </VTab>
      </VTabs>

      <VWindow
        v-model="currentTab"
        class="mt-5"
      >
        <VWindowItem
         value="security"
        >
          <Security />
          
        </VWindowItem>
        <VWindowItem
         value="notification"
        >
        <VChip
          class="d-inline-flex"
          color="primary"
          dark
        
        >
          Notification for orders</VChip>
        </VWindowItem>
      </VWindow>
    </div>
  </div>

</template>


<script setup>
import Security from '@/components/Profile/Security.vue';
import UserProfileHeader from '@/components/Profile/UserProfileHeader.vue';


const currentTab = ref('personal-info');

</script>

