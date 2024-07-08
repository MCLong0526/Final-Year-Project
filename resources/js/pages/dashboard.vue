<script setup>
import axios from 'axios';
import HorizontalEarnCard from '/resources/js/components/Dashboard/ApexCardHorizontalEarn.vue';
import CardStatisticsHorizantal from '/resources/js/components/Dashboard/CardStatisticsHorizontal.vue';
import TotalEarnedCard from '/resources/js/components/Dashboard/EarnWeeklyCard.vue';

const numberOfItems = ref(0);
const newItemsPercentage = ref(0);
const newPostsPercentage = ref(0);
const numberOfServices = ref(0);
const newServicesPercentage = ref(0);
const totalLikes = ref(0);
const newLikesPercentage = ref(0);
const numberOfMessages = ref(0);

const number_of_approved_service = ref(0);
const number_of_pending_service = ref(0);
const number_of_rejected_service = ref(0);
const number_of_cancelled_service = ref(0);
const total_earned_service = ref(0);
const total_earned_item = ref(0);
const number_of_approved_item = ref(0);
const number_of_pending_item = ref(0);
const number_of_rejected_item = ref(0);
const number_of_cancelled_item = ref(0);

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
  axios.get('/api/dashboard/get-number-of-unseen-messages')
    .then(response => {
      console.log(response.data)
      numberOfMessages.value = response.data.number_of_unseen_messages
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

const getAuthEarned = async () => {
  try {
    const response = await axios.get('/api/dashboard/get-auth-earned');
    number_of_approved_service.value = response.data.data.number_of_approved_service;
    number_of_pending_service.value = response.data.data.number_of_pending_service;
    number_of_rejected_service.value = response.data.data.number_of_rejected_service;
    number_of_cancelled_service.value = response.data.data.number_of_cancelled_service;
    total_earned_service.value = response.data.data.total_earned_service;
    total_earned_item.value = response.data.data.total_earned_item;
    number_of_approved_item.value = response.data.data.number_of_approved_item;
    number_of_pending_item.value = response.data.data.number_of_pending_item;
    number_of_rejected_item.value = response.data.data.number_of_rejected_item;
    number_of_cancelled_item.value = response.data.data.number_of_cancelled_item;
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
        title: 'Total Unseen Messages',
        color: 'secondary',
        icon: 'ri-message-2-line',
        stats: numberOfMessages,
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
      lg="3"

    >
    <TotalEarnedCard
      v-if="ready1==true"
      :number_of_approved_service="number_of_approved_service"
      :number_of_pending_service="number_of_pending_service"
      :number_of_rejected_service="number_of_rejected_service"
      :number_of_cancelled_service="number_of_cancelled_service"
      :total_earned_service="total_earned_service"
      :total_earned_item="total_earned_item"
      :number_of_approved_item="number_of_approved_item"
      :number_of_pending_item="number_of_pending_item"
      :number_of_rejected_item="number_of_rejected_item"
      :number_of_cancelled_item="number_of_cancelled_item"
      />
    </VCol>
    <VCol
      cols="12"
      md="9"
      lg="9"
    >
    <div class="card">
      <div class="card-body">
        <HorizontalEarnCard />
      </div>
    </div>
    </VCol>
  </VRow>
</template>

<style scoped>
.card {
  border: 1px solid #e0e0e0;
  border-radius: 5px;
  background-color: #fff;
  box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 10%);
  margin-block-end: 20px;
}

.box-inside {
  margin: 20px;
}


.card-title {
  font-size: 18px;
  font-weight: 500;
  margin-block-end: 0;
}



</style>
