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
      title: 'User Table',
      icon: 'ri-folder-user-line',
      to: '/user-table',
    }"
  />

  <VerticalNavLink
    :item="{
      title: 'Tables',
      icon: 'ri-table-alt-line',
      to: '/tables',
    }"
  />

</template>
