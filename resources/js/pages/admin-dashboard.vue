<script setup>
import axios from 'axios';
import CardStatisticsHorizantal from '/resources/js/@core/components/cards/CardStatisticsHorizontal.vue';

const numberOfUsers = ref(0);
const newUsersPercentage = ref(0);
const numberOfItems = ref(0);
const newItemsPercentage = ref(0);
const numberOfPosts = ref(0);
const newPostsPercentage = ref(0);

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

getUsers()
getItems()
getPosts()

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
        stats: 10,
        change: 0.5,

      }"
    />
  </VCol>
  </VRow>

  
  
</template>
