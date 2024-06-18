<script setup>
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import { computed, defineEmits, ref } from 'vue';
import { useRouter } from 'vue-router';

const emit = defineEmits(['close-dialog']);

const router = useRouter();

const store = useAuthStore();
const purchasesNotification = ref(0);
const orderItemNotification = ref(0);
const orderServiceNotification = ref(0);

const getPendingItemNotification = () => {
  axios.get('/api/order-items/count-pending-orders')
    .then(response => {
      orderItemNotification.value = response.data.data;
    })
    .catch(error => {
      console.log(error);
    });
}
const getPendingServiceNotification = () => {
  axios.get('/api/order-services/count-pending-orders')
    .then(response => {
      orderServiceNotification.value = response.data.data;
    })
    .catch(error => {
      console.log(error);
    });
}

const isAdmin = computed(() => {
  return store.user.roles.some(role => role.name === 'Admin');
});

const isSeller = computed(() => {
  return store.user.roles.some(role => role.name === 'Seller');
});

const isBuyer = computed(() => {
  return store.user.roles.some(role => role.name === 'Buyer');
});

getPendingItemNotification();
getPendingServiceNotification();

const appsAndPages = [
  { title: isAdmin.value ? 'Admin Dashboard' : 'Dashboard', icon: isAdmin.value ? 'ri-dashboard-fill' : 'ri-dashboard-line', to: isAdmin.value ? '/admin-dashboard' : '/dashboard' },
  { title: 'Upcoming Events', icon: 'ri-calendar-2-line', to: '/calendar' },
  { title: 'Chat', icon: 'ri-chat-1-line', to: '/chat' },
  { title: 'Life Moment Post', icon: 'ri-chat-smile-line', to: '/life-moment-post' },
]

const productsAndServices = [
  { title: 'Selling Item', icon: 'ri-shopping-cart-2-line', to: '/selling-item' },
  { title: 'Provide Service', icon: 'ri-service-line', to: '/provide-service' },
]

const orderInformation = [
  { title: 'Sales Items', icon: 'ri-shopping-cart-2-fill', to: '/sales-items', notification: orderItemNotification },
  { title: 'Sales Services', icon: 'ri-service-fill', to: '/sales-services', notification: orderServiceNotification },
  { title: 'Purchase Items', icon: 'ri-shopping-cart-2-fill', to: '/purchase-items', notification: purchasesNotification },
  { title: 'Purchase Services', icon: 'ri-service-fill', to: '/purchase-services' },
]

const searchQuery = ref('');

const filteredAppsAndPages = computed(() => {
  return appsAndPages.filter(item => item.title.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const filteredProductsAndServices = computed(() => {
  return productsAndServices.filter(item => item.title.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const filteredOrderInformation = computed(() => {
  return orderInformation.filter(item => item.title.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

const handleItemClick = (to) => {
  emit('close-dialog');
  router.push(to);
};
</script>

<template>
  <v-container fluid>
    <v-row>
      <v-col cols="12">
        <v-text-field
          v-model="searchQuery"
          label="Search"
          prepend-inner-icon="ri-search-line"
        />
      </v-col>
    </v-row>
    <v-row style="margin-block:30px 30px;margin-inline:30px 30px">
      <!-- Left Column for Apps & Pages -->
      <v-col cols="12" md="6">
        <h3 class="text-overline mb-2" style="font-size: 20px !important; font-weight: bold !important;">Apps & Pages</h3>
        <VList>
          <VListItem
            v-for="(item, i) in filteredAppsAndPages"
            :key="i"
            :value="item.title"
            :title="item.title"
            :prepend-icon="item.icon"
            link
            @click="handleItemClick(item.to)"
          />
        </VList>
      </v-col>

      <!-- Right Column for Product and Services, Order Information -->
      <v-col cols="12" md="6">
        <h3 class="text-overline mb-2" style="font-size: 20px !important; font-weight: bold !important;">Product and Services</h3>
        <VList>
          <VListItem
            v-for="(item, i) in filteredProductsAndServices"
            :key="i"
            :value="item.title"
            :title="item.title"
            :prepend-icon="item.icon"
            link
            @click="handleItemClick(item.to)"
          />
        </VList>
        <h3 class="text-overline mb-2 mt-2" style="font-size: 20px !important; font-weight: bold !important;">Order Information</h3>
        <VList>
          <VListItem
            v-for="(item, i) in filteredOrderInformation"
            :key="i"
            :value="item.title"
            :title="item.title"
            :prepend-icon="item.icon"
            link
            @click="handleItemClick(item.to)"
          >
            <template v-slot:append v-if="item.notification && item.notification.value > 0">
              <v-badge :content="item.notification.value" class="bg-error" />
            </template>
          </VListItem>
        </VList>
      </v-col>
    </v-row>
  </v-container>
</template>

<style scoped>
.v-list-item {
  margin-block-end: 10px;
}
</style>
