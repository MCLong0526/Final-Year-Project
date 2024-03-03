<script setup>
import VerticalNavSectionTitle from '@/@layouts/components/VerticalNavSectionTitle.vue';
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

  

</template>
