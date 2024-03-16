<script setup>

import { useAuthStore } from '@/plugins/store/AuthStore';
import DefaultLayoutWithVerticalNav from './components/DefaultLayoutWithVerticalNav.vue';

const authStore = useAuthStore()

authStore.getCurrentLoggedUser()
</script>

<template>
  <DefaultLayoutWithVerticalNav>
    <RouterView />
  </DefaultLayoutWithVerticalNav>
</template>

<style lang="scss">
// As we are using `layouts` plugin we need its styles to be imported
@use "@layouts/styles/default-layout";
</style>
