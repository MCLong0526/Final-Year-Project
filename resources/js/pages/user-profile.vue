<template>
  <div>
    <UserProfileHeader class="mb-5" />
    
    <VTabs
    v-model="currentTab"
    class="v-tabs-pill"
  >
    <VTab prepend-icon="ri-profile-line">
      Profile
    </VTab>
    <VTab prepend-icon="ri-lock-unlock-line">
      Security
    </VTab>
    <VTab prepend-icon="ri-notification-line">
      Notification
    </VTab>
  </VTabs>

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      v-for="item in 3"
      :key="item"
    >
      {{ tabItemContent }}
    </VWindowItem>
  </VWindow>
  </div>
</template>

<script setup>
import UserProfileHeader from '@/components/Profile/UserProfileHeader.vue';


const currentTab = ref(0)
const tabItemContent = 'Biscuit cheesecake gingerbread oat cake tiramisu. Marzipan tiramisu jelly-o muffin biscuit jelly cake pie. Chocolate cookie candy croissant brownie cupcake powder cheesecake. Biscuit sesame snaps biscuit topping tiramisu croissant.'


</script>
