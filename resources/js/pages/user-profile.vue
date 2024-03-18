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
          value="personal-info"
          prepend-icon="ri-profile-line">
          Profile
        </VTab>
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
         value="personal-info"
        >
          a
        </VWindowItem>
        <VWindowItem
         value="security"
        >
          <Security />
          
        </VWindowItem>
        <VWindowItem
         value="notification"
        >
          c
        </VWindowItem>
      </VWindow>
    </div>
  </div>

</template>


<script setup>
import UserProfileHeader from '@/components/Profile/UserProfileHeader.vue';
import Security from '@/components/Profile/Security.vue';


const currentTab = ref('personal-info');

</script>

