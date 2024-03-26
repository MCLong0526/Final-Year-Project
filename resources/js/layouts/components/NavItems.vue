<script setup>import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue';
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue';

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
  <VerticalNavLink
    :item="{
      title: 'Selling Item',
      icon: 'ri-shopping-cart-2-line',
      to: '/selling-item',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Order Information',
      icon: 'ri-shopping-bag-2-line',
      to: '/order-information',
    }"
  />
  
  

  

</template>
