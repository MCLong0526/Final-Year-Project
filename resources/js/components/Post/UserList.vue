<script setup>

import UserProfileDialog from '@/components/Profile/UserProfileDialog.vue';
import axios from 'axios';
import { defineProps, ref } from 'vue';
import { useRouter } from 'vue-router';

const router = useRouter();

const props = defineProps({
  newUsers: {
    type: Object,
    required: true
  },
  searchNewUsers: {
    type: Function,
    required: true
  }
});

const currentUser = ref([]);
const followDialog = ref(false);
const unFollowDialog = ref(false);
const clickedUser = ref([]);
const isFollowErrorAlert = ref(false);
const isFollowSuccessAlert = ref(false);
const errorMessage = ref(null);
const successMessage = ref(null);
const openDialog = ref(false);
const clickedUser2 = ref([]);


const openProfileDialog = (user) => {

  openDialog.value = true;
  clickedUser2.value = user;
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

    currentUser.value = response.data.user;
  } catch (error) {

    //go to error page
    router.push('/error-unauthorized');

    console.error(error);
  }
};

getUser();

const openFollowDialog = (selectedUser) => {
    axios.post('/api/users/follow-user/' + selectedUser.user_id)
      .then(response => {
        successMessage.value = response.data.message;
        isFollowSuccessAlert.value = true;
        followDialog.value=true;
        clickedUser.value = selectedUser;

        props.searchNewUsers();

      })
      .catch(error => {
        
        errorMessage.value = error.response.data.message;
        isFollowErrorAlert.value = true;
        console.log(error);
      });
};

const openUnfollowDialog = (selectedUser) => {
  clickedUser.value = selectedUser;
  unFollowDialog.value = true;
};

const unFollowUser = () => {
  axios.post('/api/users/unfollow-user/' + clickedUser.value.user_id)
    .then(response => {
      successMessage.value = response.data.message;
      isFollowSuccessAlert.value = true;
      unFollowDialog.value = false;
      
      props.searchNewUsers();
      
    })
    .catch(error => {
      errorMessage.value = error.response.data.message;
      isFollowErrorAlert.value = true;
      console.log(error);
    });
};


const resolveStatusColor = {
  active: 'success',
  inactive: 'error',
};

</script>

<template>
  <VList
    v-if="newUsers.length > 0"
    lines="two"
    border
    rounded
    class="new-users-list"
    style="inline-size: 100%;"
  >
    <template
      v-for="(user, index) of props.newUsers"
      :key="user.name"
    >
      <VListItem class="ml-3 mr-3" >
        <template #prepend>
          <VAvatar :image="user.avatar" />
        </template>
        <VListItemTitle 
          v-if="user.user_id !== currentUser.user_id"
          style="cursor: pointer;"
          @click="openProfileDialog(user)"
        >
          <VTooltip
            location="top"
            open-delay="500"
            activator="parent"
            transition="scroll-y-transition"
          >
            <span>Click to view profile</span>
          </VTooltip>
          {{ user.username }}
        </VListItemTitle>
        <VListItemTitle 
          v-else
          style="cursor: pointer;"
        >
          <VTooltip
            location="top"
            open-delay="500"
            activator="parent"
            transition="scroll-y-transition"
          >
            <span>This is your profile</span>
          </VTooltip>
          {{ user.username }}
        </VListItemTitle>
        
        <VListItemSubtitle class="mt-1">
          <VBadge
            dot
            location="start center"
            offset-x="2"
            :color="resolveStatusColor[user.status]"
            class="me-3"
          >
            <span 
            v-if="user.status === 'active'"
              class="ms-4"
            >
              Active
            </span>
            <span
              v-else-if="user.status === 'inactive'"
              class="ms-4"
            >
              Inactive
            </span>
          </VBadge>
        </VListItemSubtitle>

        <template #append>
         
          <VBtn
            v-if="currentUser.user_id !== user.user_id && user.is_following"
            color="success"
            size="x-small"
            variant="tonal"
            class="ml-2"
            @click="openUnfollowDialog(user)"
          >
            <VIcon icon="ri-check-line" size="16" class="me-1" />
            <span class="text-xs">Following</span>
          </VBtn>
          <VBtn
            v-else-if="currentUser.user_id !== user.user_id && !user.is_following"
            color="primary"
            size="x-small"
            variant="tonal"
            class="ml-2"
            @click="openFollowDialog(user)"
          >
            <VIcon icon="ri-user-follow-line" size="16" class="me-1"/>
      
          <span class="text-xs">Follow</span>
          </VBtn>

        </template>
      </VListItem>
      <VDivider v-if="index !== props.newUsers.length - 1" />
    </template>
  </VList>
  <VList v-else>
      <VAlert  
        variant="tonal"
        type="warning"
        class="mt-2 mb-2 ml-2 mr-2 text-center"
        color="primary"
        dense
      >
        No new users found
      </VAlert>
    </VList>




    <!--Unfollow Dialog-->
  <VDialog
    v-model="unFollowDialog"

    max-width="600"
  >
    <!-- Dialog Content -->
    <VCard title="Are you sure you want to unfollow?">
      <VCardText>
        <VAlert
          color="error"
          icon="ri-chat-smile-line"
          variant="tonal"
        >
          You are about to unfollow {{ clickedUser.username }}. Are you sure you want to proceed?
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="secondary"
          @click="unFollowDialog = false"
        >
          Close
        </VBtn>
        <VBtn
          color="error"
          @click="unFollowUser"
        >
          Unfollow
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>


    <VSnackbar
      v-model="isFollowErrorAlert"
      location="top end"
      transition="scale-transition"
      color="error"
    >
    <VIcon size="20" class="me-2">ri-error-warning-line</VIcon>
    <span>{{ errorMessage }}</span>
  </VSnackbar>
  <VSnackbar
      v-model="isFollowSuccessAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
    <span>{{ successMessage }}</span>
  </VSnackbar>

  <!--Profile Dialog-->
  <VDialog
    v-model="openDialog"
    max-width="450"
  >
    <UserProfileDialog :clickedUser="clickedUser2" />
  </VDialog>


</template>

<style scoped>
.new-users-list {
  inline-size: 100%; /* Ensures the list takes the full width of the container */
}
</style>
