<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue';
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue';
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
  <VerticalNavGroup
    :item="{
      title: 'Sales',
      icon: 'ri-shopping-bag-2-line',
    }"
  >
  <VerticalNavLink
    v-if="orderNotification > 0"
    :item="{
      title: 'Sales Items',
      badgeContent: orderNotification,
      badgeClass: 'bg-error',
      icon: 'ri-shopping-cart-2-fill',
      to: '/sales-items',
    }"
  />
  <VerticalNavLink
    v-else
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
    v-if="purchasesNotification > 0"
    :item="{
      title: 'Purchase Items',
      badgeContent: purchasesNotification,
      badgeClass: 'bg-error',
      icon: 'ri-shopping-cart-2-fill',
      to: '/purchase-items',
    }"
  />
  <VerticalNavLink
    v-else
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
