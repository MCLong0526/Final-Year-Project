<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue';
import { useAuthStore } from '@/plugins/store/AuthStore';
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue';
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue';

const store = useAuthStore();
const purchasesNotification = ref(0);
const orderItemNotification = ref(0);
const orderServiceNotification = ref(0);



const isAdmin = computed(() => {
  return store.user.roles.some(role => role.name === 'Admin');
});

const isSeller = computed(() => {
  return store.user.roles.some(role => role.name === 'Seller');
});

const isBuyer = computed(() => {
  return store.user.roles.some(role => role.name === 'Buyer');
});



</script>

<template>
  
  <VerticalNavSectionTitle
    :item="{
      heading: 'Apps & Pages',
    }"
  />
  
  <VerticalNavLink
     v-if="isAdmin === true" 
    :item="{
      title: 'Admin Dashboard',
      icon: 'ri-dashboard-fill',
      to: '/admin-dashboard',
    }"
  />
  <VerticalNavLink
    v-if="isBuyer === true || isSeller === true"
    :item="{
      title: 'Dashboard',
      icon: 'ri-dashboard-line',
      to: '/dashboard',
    }"
  />

  <VerticalNavLink
   v-if="isAdmin === true" 
    :item="{
      title: 'User Management',
      icon: 'ri-folder-user-line',
      to: '/user-management',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Upcoming Events',
      icon: 'ri-calendar-2-line',
      to: '/calendar',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Chat',
      icon: 'ri-chat-1-line',
      to: '/chat',
    }"
  />

  
  <VerticalNavLink
    :item="{
      title: 'Life Moment Post',
      icon: 'ri-chat-smile-line',
      to: '/life-moment-post',
    }"
  />
  <VerticalNavSectionTitle
    :item="{
      heading: 'Product and Services',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Selling Item',
      icon: 'ri-shopping-cart-2-line',
      to: '/selling-item',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Provide Service',
      icon: 'ri-service-line',
      to: '/provide-service',
    }"
  />
  <VerticalNavSectionTitle
    :item="{
      heading: 'Order Information',
    }"
  />
  <VerticalNavGroup
    v-if="isSeller === true"
    :item="{
      title: 'Sales',
      icon: 'ri-shopping-bag-2-line',
    }"
  >

  <VerticalNavLink

    :item="{
      title: 'Sales Items',
      icon: 'ri-shopping-cart-2-fill',
      to: '/sales-items',
    }"
  />

  <VerticalNavLink
 
    :item="{
      title: 'Sales Services',
      icon: 'ri-service-fill',
      to: '/sales-services',
    }"
  />
  </VerticalNavGroup> 
  

  <VerticalNavGroup
    :item="{
      title: 'Purchases',
      icon: 'ri-shopping-basket-line',
    }"
  >


  <VerticalNavLink
    :item="{
      title: 'Purchase Items',
      icon: 'ri-shopping-cart-2-fill',
      to: '/purchase-items',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Purchase Services',
      icon: 'ri-service-fill',
      to: '/purchase-services',
    }"
  />
  </VerticalNavGroup>



</template>
