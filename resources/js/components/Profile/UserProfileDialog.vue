<script setup>
import Chat from '@/pages/chat.vue';
import { useAuthStore } from '@/plugins/store/AuthStore'; // Adjust the path based on your project structure
import axios from 'axios';
import { defineProps } from 'vue';

const props = defineProps({
  clickedUser: {
    type: Object,
    required: true
  }

})

const store = useAuthStore();
const emit = defineEmits(['update:clickedUser'])
const numberOfItems = ref(0)
const numberOfServices = ref(0)
const numberOfFollowers = ref(0)
const ratings = ref(0)
const isFollowing = ref(false)
const followDialog = ref(false)
const unFollowDialog = ref(false)
const selectedUser = ref({})
const isFollowSuccessAlert = ref(false)
const isFollowErrorAlert = ref(false)
const successMessage = ref('')
const errorMessage = ref('')
const dialogChat = ref(false)
const newChatUser = ref({})
// Get user details
const getUserDetails = async () => {
  try {
    const response = await axios.get(`/api/users/get-user-details/${props.clickedUser.user_id}`)

    numberOfItems.value = response.data.data.items_count
    numberOfServices.value = response.data.data.services_count
    numberOfFollowers.value = response.data.data.followers_count
    isFollowing.value = response.data.data.is_followed
    ratings.value = response.data.data.ratings

  } catch (error) {
    console.error(error)
  }
}

const openFollowDialog = (user) => {
  
  axios.post('/api/users/follow-user/' + user.user_id)
    .then(response => {
      successMessage.value = response.data.message;
      isFollowSuccessAlert.value = true;
      followDialog.value=true;
      selectedUser.value = user;
      getUserDetails()

    })
    .catch(error => {
      
      errorMessage.value = error.response.data.message;
      isFollowErrorAlert.value = true;
      console.log(error);
    });

};

const openUnfollowDialog = (user) => {
  selectedUser.value = user;
  unFollowDialog.value = true;
};

const unFollowUser = () => {
  axios.post('/api/users/unfollow-user/' + selectedUser.value.user_id)
    .then(response => {
      successMessage.value = response.data.message;
      isFollowSuccessAlert.value = true;
      unFollowDialog.value = false;
      getUserDetails()

      
    })
    .catch(error => {
      errorMessage.value = error.response.data.message;
      isFollowErrorAlert.value = true;
      console.log(error);
    });
};

const openChatDialog = (user) => {
  newChatUser.value = user;
  dialogChat.value = true;
};

const openWhatsApp = (clickedUser) => {

  const message = `Hello! I am ${store.user.username} from UNIMAS Web App. I would like to discuss the order here. Thank you.`;
  const apilink = `https://web.whatsapp.com/send?phone=+60${clickedUser.phone_number}&text=${encodeURIComponent(message)}`;

  // Open the WhatsApp web link
  window.open(apilink, '_blank');
};

getUserDetails()
</script>

<template>
  <VCard >
    <VRow class="text-center">
      <VCol cols="12" md="2" />
      <VCol cols="12" md="8">
        <VAvatar size="70"
          :color="clickedUser.avatar ? '' : 'primary'"
          :class="`${!clickedUser.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
          :variant="!clickedUser.avatar ? 'tonal' : undefined"
   
          class="mt-6"
          >
          <VImg
          :src="clickedUser.avatar"/>
        </VAvatar>
        <VCardTitle style="font-size:13px !important">
          <VIcon icon="ri-user-fill" size="14" class="mr-2"></VIcon>{{ clickedUser.username }}
          <VBtn
            v-if="isFollowing"
            color="success"
            class="ml-2"
            variant="tonal"
            size="x-small"
            @click="openUnfollowDialog(clickedUser)"
          >
          <VTooltip
            open-delay="500"
            location="right"
            activator="parent"
            transition="scroll-x-reverse-transition"
          >
          <span>Click to unfollow</span>
          </VTooltip>
          <VIcon icon="ri-check-line" size="16" class="me-1" />
          <span class="text-xs">Following</span>
          </VBtn>
          <VBtn
            v-else
            color="primary"
            class="ml-2"
            variant="tonal"
            size="x-small"
            @click="openFollowDialog(clickedUser)"
          >
          <VTooltip
            open-delay="500"
            location="right"
            activator="parent"
            transition="scroll-x-reverse-transition"
          >
          <span>Click to follow</span>
          </VTooltip>
          <VIcon icon="ri-add-line" size="16" class="me-1" />
          <span class="text-xs">Follow</span>
          </VBtn>
        </VCardTitle>
        <VCardSubtitle class=" text-overline" style="font-size:12px !important">
          <VIcon icon="ri-mail-fill" size="18" class="mr-2"></VIcon>{{ clickedUser.email }}<br>
          <VChip
              v-if="ratings !== null"
              color="secondary"
              size="small"
              variant="outlined"
              class="mt-2"
            ><VIcon icon="ri-star-fill" class="mr-1" style="color: #fbb400;"/> 
              {{ ratings }} / 5
            </VChip>
            
            <VChip
              v-else
              color="secondary"
              variant="outlined"
              class="mt-2"
              size="small"
            >
              No ratings yet
            </VChip>  
        </VCardSubtitle>

      </VCol>
      <VCol cols="12" md="2" />

      </VRow>

      <VRow>
          <VBtn
            color="primary"
            class="mt-2 mb-3"
            @click="openChatDialog (clickedUser)"
            style=" margin-inline:120px 40px"

          >
            <VIcon icon="ri-chat-3-line" class="mr-2" />
            Chat
          </VBtn>

          <VBtn
            color="warning"
            class="mt-3"
            @click="openWhatsApp(clickedUser)"
          >
            <VIcon icon="ri-whatsapp-line" class="mr-2" />
            Whatsapp
          </VBtn>

      </VRow>
      <VDivider class="mt-3" />
      <VRow class="mb-3 mt-1">
      <VCol cols="12" md="4">
  
          <VCardTitle class="text-center text-overline" style="font-size:20px !important">
            <b>{{ numberOfItems }}</b>
          </VCardTitle>
          <VCardSubtitle class="text-center text-overline" style="font-size:10px !important">
            <VIcon icon="ri-shopping-cart-2-fill" size="18" class="mr-2"></VIcon>Items
          </VCardSubtitle>
      </VCol>
      <VCol cols="12" md="4">
          <VCardTitle class="text-center text-overline" style="font-size:20px !important">
            <b>{{ numberOfServices }}</b>
          </VCardTitle>
          <VCardSubtitle class="text-center text-overline" style="font-size:10px !important">
            <VIcon icon="ri-service-fill" size="18" class="mr-2"></VIcon>Services
          </VCardSubtitle>
      </VCol>
      <VCol cols="12" md="4">
          <VCardTitle class="text-center text-overline" style="font-size:20px !important">
            <b>{{ numberOfFollowers }}</b>
          </VCardTitle>
          <VCardSubtitle class="text-center text-overline" style="font-size:10px !important">
            <VIcon icon="ri-user-fill" size="14" class="mr-2"></VIcon>Followers
          </VCardSubtitle>
      </VCol>

      
      
      
    </VRow>
  </VCard>

  <!--Follow Dialog-->
  <VDialog
    v-model="followDialog"
    max-width="600"
  >
    <!-- Dialog Content -->
    <VCard title="Follow User">  
      <VCardText>
        <VAlert
          color="primary"
          icon="ri-chat-smile-line"
          variant="tonal"
        >
        <span>
          You are now following {{ clickedUser.username }}!<br>
          <b>Let's chat with {{ clickedUser.username }} in the chat section.</b>
        </span>
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="secondary"
          @click="followDialog = false"
        >
          Close
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

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


  <VDialog
    v-model="dialogChat"
    width="600"
  >
    <Chat :newChatUser="newChatUser" />
  </VDialog>

  <!--Alerts-->
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

</template>

