<script setup>
import misc404 from '@images/pages/404_3.png'
import miscMaskDark from '@images/pages/misc-mask-dark.png'
import miscMaskLight from '@images/pages/misc-mask-light.png'
import axios from 'axios'
import { useTheme } from 'vuetify'

const vuetifyTheme = useTheme()

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? miscMaskLight : miscMaskDark
})

const user = ref([])
const isLogged = ref(false)
const isAdmin = ref(false)
const getUser = async () => {
  try {
    const token = localStorage.getItem('token');
    
    if (!token) {
      throw new Error('Token not found');
    }

    const response = await axios.get('/api/auth/get-user-by-token', {
      headers: {
        Authorization: `Bearer ${token}`,
      },
    });

    user.value = response.data.user;

    if (user.value.roles.some(role => role.name === 'Admin')) {
      isAdmin.value = true
    }

    isLogged.value = true
  } catch (error) {

    //go to error page
    router.push('/error-unauthorized');

    console.error(error);
  }
};

getUser();
</script>

<template>
  <div class="misc-wrapper">
    <ErrorHeader
      status-code="404"
      title="Page Not Found ⚠️"
      description="We couldn't find the page you are looking for."
    />

    <!-- 👉 Image -->
    <div class="misc-avatar w-100 text-center">
      <VImg
        :src="misc404"
        alt="Coming Soon"
        :max-width="580"
        class="mx-auto"
      />
      <VBtn
        v-if="isLogged && isAdmin"
        to="/admin-dashboard"
        class="mt-10"
      >
        Back to Admin Dashboard
      </VBtn>
      <VBtn
        v-else-if="isLogged && !isAdmin"
        to="/dashboard"
        class="mt-10"
      >
        Back to Dashboard
      </VBtn>
      <VBtn
        v-else
        to="/"
        class="mt-10"
      >
        Back to login
      </VBtn>
    </div>

    <!-- 👉 Footer -->
    <VImg
      :src="tree"
      class="misc-footer-tree d-none d-md-block"
    />

    <!-- <VImg
      :src="authThemeMask"
      class="misc-footer-img d-none d-md-block"
    /> -->
  </div>
</template>

<style lang="scss">
@use "@core-scss/pages/misc.scss";

.misc-footer-tree {
  inline-size: 15.625rem;
  inset-block-end: 3.5rem;
  inset-inline-start: 0.375rem;
}
</style>
