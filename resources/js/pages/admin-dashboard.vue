<script setup>

import axios from 'axios';
import CardStatisticsHorizantal from '/resources/js/@core/components/cards/CardStatisticsHorizontal.vue';
import VChartStatisticsItem from '/resources/js/components/Dashboard/ChartStatisticsItem.vue';
import DonutChartForStatus from '/resources/js/components/Dashboard/DonutChartForStatus.vue';

const numberOfUsers = ref(0);
const newUsersPercentage = ref(0);
const numberOfItems = ref(0);
const newItemsPercentage = ref(0);
const numberOfPosts = ref(0);
const newPostsPercentage = ref(0);
const numberOfServices = ref(0);
const newServicesPercentage = ref(0);
const isActiveUsers = ref(0);
const isInactiveUsers = ref(0);
const activeUserPercentage = ref(0);
const allItemTypes = ref([]);
const isReady = ref(false);
const isReady2 = ref(false);

const getUsers = () => {
  axios.get('/api/admin-dashboard/get-number-of-users')
    .then(response => {
      numberOfUsers.value = response.data.number_of_users
      newUsersPercentage.value = response.data.new_users_percentage;
      
    })
    .catch(error => {
      console.log(error)
    })
}

const getItems = () => {
  axios.get('/api/admin-dashboard/get-number-of-items')
    .then(response => {
      numberOfItems.value = response.data.number_of_items
      newItemsPercentage.value = response.data.new_items_percentage;
      
    })
    .catch(error => {
      console.log(error)
    })
}

const getPosts = () => {
  axios.get('/api/admin-dashboard/get-number-of-posts')
    .then(response => {
      numberOfPosts.value = response.data.number_of_posts
      newPostsPercentage.value = response.data.new_posts_percentage;
      
    })
    .catch(error => {
      console.log(error)
    })
}

const getServices = () => {
  axios.get('/api/admin-dashboard/get-number-of-services')
    .then(response => {
      numberOfServices.value = response.data.number_of_services
      newServicesPercentage.value = response.data.new_services_percentage;
      
    })
    .catch(error => {
      console.log(error)
    })
}

const getStatusUsers = () => {
  axios.get('/api/admin-dashboard/get-status-users')
    .then(response => {
      isActiveUsers.value = response.data.active_users
      isInactiveUsers.value = response.data.inactive_users
      activeUserPercentage.value = response.data.active_users_percentage
      isReady.value = true;
    })
    .catch(error => {
      console.log(error)
    })
}

const getAllItemTypes = () => {
  axios.get('/api/admin-dashboard/get-all-percentage-type-items')
    .then(response => {
      console.log(response.data)
      allItemTypes.value = response.data.type_items_percentage
      isReady2.value = true;
    
    })
    .catch(error => {
      console.log(error)
    })
}

getUsers()
getItems()
getPosts()
getServices()
getStatusUsers()
getAllItemTypes()
</script>

<template>
  <VRow>
    <VCol
      cols="12"
      md="3"
      sm="3"
    >
    <CardStatisticsHorizantal
      v-bind="{
        title: 'Total Users',
        color: 'primary',
        icon: 'ri-user-2-line',
        stats: numberOfUsers,
        change: parseFloat(newUsersPercentage),

      }"
    />
  </VCol>
  <VCol
      cols="12"
      md="3"
      sm="3"
    >
    <CardStatisticsHorizantal
      v-bind="{
        title: 'Total Posts',
        color: 'success',
        icon: 'ri-chat-smile-line',
        stats: numberOfPosts,
        change: parseFloat(newPostsPercentage),

      }"
    />
  </VCol>
  <VCol
      cols="12"
      md="3"
      sm="3"
    >
    <CardStatisticsHorizantal
      v-bind="{
        title: 'Total Selling Items',
        color: 'info',
        icon: 'ri-shopping-cart-2-line',
        stats: numberOfItems,
        change: parseFloat(newItemsPercentage),

      }"
    />
  </VCol>
  <VCol
      cols="12"
      md="3"
      sm="3"
    >
    <CardStatisticsHorizantal
      v-bind="{
        title: 'Total Provide Services',
        color: 'warning',
        icon: 'ri-service-line',
        stats: numberOfServices,
        change: parseFloat(newServicesPercentage),

      }"
    />
  </VCol>
  </VRow>

  <VRow>
    <VCol
      cols="12"
      md="3"
      sm="6"
    >
    <VCard
      title="Users Status"
      :subtitle="`Active Users: ${isActiveUsers} | Inactive Users: ${isInactiveUsers}`"
    >
      <VCardText>
      <DonutChartForStatus
        v-if="isReady"
        v-bind="{
          activeUserPercentage: activeUserPercentage,
          isActiveUsers: isActiveUsers,
          isInactiveUsers: isInactiveUsers,
        }"
      />
      </VCardText>
    </VCard>
    </VCol>
    <VCol
      cols="12"
      md="4"
      sm="6"
    >
      <VCard 
        title="Top Five Selling Item Types"
        subtitle="Percentage of each item type in the system."
        class="mb-0"
      >
        <VChartStatisticsItem
          v-if="isReady2"
          v-bind="{
            itemData: allItemTypes,
          }"
        />
  
      
      </VCard>
    </VCol>
  </VRow>
  


  
  
</template>


