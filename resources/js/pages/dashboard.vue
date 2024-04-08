<script setup>
import axios from 'axios';
import CardStatisticsHorizantal from '/resources/js/components/Dashboard/CardStatisticsHorizontal.vue';
import EarnWeeklyCard from '/resources/js/components/Dashboard/EarnWeeklyCard.vue';

const numberOfItems = ref(0);
const newItemsPercentage = ref(0);
const numberOfPosts = ref(0);
const newPostsPercentage = ref(0);
const numberOfServices = ref(0);
const newServicesPercentage = ref(0);
const totalLikes = ref(0);
const newLikesPercentage = ref(0);

const allSchedules = ref([]);
const number_of_approved = ref(0);
const number_of_pending = ref(0);
const number_of_rejected = ref(0);
const total_earned = ref(0);
const ready1 = ref(false);

const getAuthPostLikes = () => {
  axios.get('/api/dashboard/get-number-of-likes')
    .then(response => {
      totalLikes.value = response.data.total_likes
      newLikesPercentage.value = response.data.new_likes_percentage;
      
    })
    .catch(error => {
      console.log(error)
    })
}

const getAuthItems = () => {
  axios.get('/api/dashboard/get-number-of-items')
    .then(response => {
      numberOfItems.value = response.data.number_of_items
      newItemsPercentage.value = response.data.new_items_percentage;
      
    })
    .catch(error => {
      console.log(error)
    })
}

const getAuthPosts = () => {
  axios.get('/api/dashboard/get-number-of-posts')
    .then(response => {
      numberOfPosts.value = response.data.number_of_posts
      newPostsPercentage.value = response.data.new_posts_percentage;
      
    })
    .catch(error => {
      console.log(error)
    })
}

const getAuthServices = () => {
  axios.get('/api/dashboard/get-number-of-services')
    .then(response => {
      numberOfServices.value = response.data.number_of_services
      newServicesPercentage.value = response.data.new_services_percentage;
      
    })
    .catch(error => {
      console.log(error)
    })
}

// const getSchedules = async () => {
//   try {
//     const response = await axios.get('/api/dashboard/get-schedules');
//     allSchedules.value = response.data.data;
//     console.log(allSchedules.value);
//   } catch (error) {
//     console.error(error);
//   }
// };
const getAuthEarned = async () => {
  try {
    const response = await axios.get('/api/dashboard/get-auth-earned');
    number_of_approved.value = response.data.data.number_of_approved;
    number_of_pending.value = response.data.data.number_of_pending;
    number_of_rejected.value = response.data.data.number_of_rejected;
    total_earned.value = response.data.data.total_earned;
    ready1.value = true;
  } catch (error) {
    console.error(error);
  }
};

getAuthPostLikes();
getAuthEarned();
getAuthItems();
getAuthPosts();
getAuthServices();
// getSchedules();
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
        title: 'Total Likes on Posts',
        color: 'success',
        icon: 'ri-thumb-up-line',
        stats: totalLikes,
        change: parseFloat(newLikesPercentage),

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
        color: 'secondary',
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
    <!-- <VCol
      cols="12"
      md="6"
      lg="4"
    >
      <ScheduleMeetUp />

    </VCol> -->
    <VCol
      cols="12"
      md="3"
      lg="3"

    >
    <EarnWeeklyCard 
    v-if="ready1==true"
    :number_of_approved="number_of_approved"
    :number_of_pending="number_of_pending"
    :number_of_rejected="number_of_rejected"
    :total_earned="total_earned"
    />
    </VCol>
  </VRow>
</template>
