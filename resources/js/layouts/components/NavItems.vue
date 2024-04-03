<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue';
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue';
import axios from 'axios';

const purchasesNotification = ref(0);
const orderNotification = ref(0);

const getUnreadNotification = () => {
  axios.get('/api/order-items/count-pending-orders')
    .then(response => {
      orderNotification.value = response.data.data

    })
    .catch(error => {
      console.log(error)
    })
}

getUnreadNotification()

</script>

<template>
  
  <VerticalNavSectionTitle
    :item="{
      heading: 'Apps & Pages',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Dashboard',
      icon: 'ri-dashboard-line',
      to: '/dashboard',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Admin Dashboard',
      icon: 'ri-dashboard-fill',
      to: '/admin-dashboard',
    }"
  />


  

  <VerticalNavLink
    :item="{
      title: 'Test',
      icon: 'ri-test-tube-line',
      to: '/test',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'User Management',
      icon: 'ri-folder-user-line',
      to: '/user-management',
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
  <VerticalNavLink
    v-if="orderNotification > 0"
    :item="{
      title: 'Sales Orders',
      badgeContent: orderNotification,
      badgeClass: 'bg-error',
      icon: 'ri-shopping-bag-2-line',
      to: '/sales-orders',
    }"
  />
  <VerticalNavLink
    v-else
    :item="{
      title: 'Sales Orders',
      icon: 'ri-shopping-bag-2-line',
      to: '/sales-orders',
    }"
  />

  <VerticalNavLink
    v-if="purchasesNotification > 0"
    :item="{
      title: 'Track Purchases',
      badgeContent: purchasesNotification,
      badgeClass: 'bg-error',
      icon: 'ri-shopping-cart-2-line',
      to: '/track-purchases',
    }"
  />
  <VerticalNavLink
    v-else
    :item="{
      title: 'Track Purchases',
      icon: 'ri-shopping-cart-2-line',
      to: '/track-purchases',
    }"
  />


</template>
