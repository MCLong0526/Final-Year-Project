<script setup>
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import { useRouter } from 'vue-router';
import avatar1 from '/resources/images/avatars/avatar-1.png';
 
const router = useRouter();
const authStore = useAuthStore();

const user = ref([]);
const hasError = ref(false);

const logout = () => {
  authStore.logout();
  //remove all the auth data from local storage
  localStorage.removeItem('token');
  localStorage.removeItem('user');
  //redirect to login page
  router.push('/login');
};

// get the authenticated user
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
  } catch (error) {

    //go to error page
    router.push('/error-unauthorized');

    console.error(error);
  }
};

getUser();


</script>

<template>
  <VBadge
    
    dot
    location="bottom right"
    offset-x="3"
    offset-y="3"
    color="success"
    bordered
  >
    <VAvatar
      class="cursor-pointer"
      color="primary"
      variant="tonal"
    >
      <VImg :src="user.avatar ?? avatar1" />

      <!-- SECTION Menu -->
      <VMenu
        activator="parent"
        width="240"
        location="bottom end"
        offset="14px"
      >
        <VList>
          <!-- 👉 User Avatar & Name -->
          <VListItem>
            <template #prepend>
              <VListItemAction start>
                <VBadge
                  v-if="user.status === 'active'"
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="success"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="user.avatar" />
                  </VAvatar>
                </VBadge>
                <VBadge
                  v-else
                  dot
                  location="bottom right"
                  offset-x="3"
                  offset-y="3"
                  color="error"
                >
                  <VAvatar
                    color="primary"
                    variant="tonal"
                  >
                    <VImg :src="user.avatar" />
                  </VAvatar>
                </VBadge>
              </VListItemAction>
            </template>

            <VListItemTitle class="font-weight-semibold">
              {{ user.username }}
            </VListItemTitle>
            <VListItemSubtitle>
              <span class="text-small">{{ user.email }}</span>
    
            </VListItemSubtitle>
          </VListItem>
          <VDivider class="my-2" />

          <!-- 👉 Profile -->
          <VListItem to="/user-profile">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="ri-user-line"
                size="22"
              />
            </template>

            <VListItemTitle>Profile</VListItemTitle>
          </VListItem>

          <!-- 👉 Settings -->
          <VListItem link>
            <template #prepend>
              <VIcon
                class="me-2"
                icon="ri-settings-4-line"
                size="22"
              />
            </template>

            <VListItemTitle>Settings</VListItemTitle>
          </VListItem>

          <!-- Divider -->
          <VDivider class="my-2" />

          <!-- 👉 Logout -->
          <VListItem @click="logout()">
            <template #prepend>
              <VIcon
                class="me-2"
                icon="ri-logout-box-r-line"
                size="22"
              />
            </template>

            <VListItemTitle>Logout</VListItemTitle>
          </VListItem>
        </VList>
      </VMenu>
      <!-- !SECTION -->
    </VAvatar>
  </VBadge>
</template>
