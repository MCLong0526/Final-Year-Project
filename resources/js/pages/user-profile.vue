<script setup>
import ShowPosts from '@/components/Post/ShowPosts.vue';
import Security from '@/components/Profile/Security.vue';
import UserProfileHeader from '@/components/Profile/UserProfileHeader.vue';
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import { debounce } from 'lodash';

const currentTab = ref('personal-info');
const posts = ref([]);
const showLoading = ref(false);
const store = useAuthStore();
const typePosts = ref('private');
const user = ref({});
const registerAsSeller = ref(false);
const isRegisterAlert = ref(false);
const updateToSeller = ref(false);

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

    // is in user.roles array has role.name === 'Seller' then user.isSeller = true
    user.value.isSeller = user.value.roles.some(role => role.name === 'Seller');

  } catch (error) {

    //go to error page
    router.push('/error-unauthorized');

    console.error(error);
  }
};


// get posts
const getPosts = debounce(() => {
  axios.get('/api/posts/get-auth-posts')
    .then(response => {
      posts.value = response.data.data;

      // Change the user's avatar of the new posts by adding the http://127.0.0.1:8000/storage/
      posts.value.forEach(post => {
        if (post.user.avatar) {
          post.user.avatar = '/storage/' + post.user.avatar;
        }
      });

      // Change also the new posts' picture by adding the http://
      posts.value.forEach(post => {
        if (post.picture) {
          post.picture = '/storage/' + post.picture;
        }
      });

      // Change the created_at date format to a readable format, and remove seconds
      posts.value.forEach(post => {
        post.created_at = new Date(post.created_at).toLocaleString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: 'numeric',
          minute: 'numeric',
        });
      });

      // Count the number of likes of each new post
      posts.value.forEach(post => {
        post.likes_count = post.likes.length;
      });

      // Check if the new post is liked by the user
      posts.value.forEach(post => {
        post.is_liked = post.likes.some(like => like.user_id === store.user.user_id);
      });

    })
    .catch(error => {
      console.log(error);
    });
}, 800);


const registerSeller = () => {

  axios.post('/api/auth/register-seller')
    .then(response => {
      registerAsSeller.value = false;
      user.value.isSeller = true;
      isRegisterAlert.value = true;

      updateToSeller.value = true;

      // go to tab security
      currentTab.value = 'security';
    })
    .catch(error => {
      console.log(error);
    });
};

getPosts();
getUser();

</script>


<template>
  <div style="display: flex;">
    <div style="inline-size: 50%;">
      <UserProfileHeader class="mb-5" 
        :update-to-seller="updateToSeller"
      />
    </div>
    <VSpacer class="mx-5" />


    <div style="inline-size: 50%;">
      <VTabs
        v-model="currentTab"
        class="v-tabs-pill justify-end"
      >
      
        <VTab 
          value="security"
          prepend-icon="ri-lock-unlock-line">
          Security
        </VTab>
        <VTab 
          value="my-posts"
          prepend-icon="ri-chat-settings-line">
          My Posts
        </VTab>
        <VTab 
           v-if="user.isSeller ===false"
          value="register-as-seller"
          prepend-icon="ri-user-3-line">
          Register As Seller
        </VTab>
      </VTabs>

      <VWindow
        v-model="currentTab"
        class="mt-5"
      >
        <VWindowItem
         value="security"
        >
          <Security />
          
        </VWindowItem>
        <VWindowItem
         value="my-posts"
        >
          <ShowPosts 
            :posts="posts"
            :getPosts="getPosts"
            :showLoading="showLoading"
            :typePosts="typePosts"
          />
        
        </VWindowItem>
        <VWindowItem
         value="register-as-seller"
        >
        <VCard title="Register As Seller" class="relative">
          <div v-if="showLoading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-50">
            <VProgressCircular  :size="50" color="success" indeterminate  />
          </div>
          <VCardText>
            <VAlert
              color="error"
              icon="ri-alert-line"
              variant="tonal"
              closable
            
            >
              You are not registered as a seller yet. <br><strong>Please tick the checkbox below to register as a seller.</strong>
            </VAlert>
            <VCheckbox
              v-model="registerAsSeller"
              label="I want to register as a seller"
              class="mt-5"

            />
          </VCardText>
        </VCard>
          

        </VWindowItem>
      
      </VWindow>
    </div>
  </div>

    <!-- Register As Seller Dialog -->
    <VDialog
    v-model="registerAsSeller"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog Content -->
    <VCard title="Confirmation to Register As Seller">
      <VCardText>
        <VAlert
          color="error"
          icon="ri-alert-line"
          variant="tonal"
        >
        Are you sure you want to register as a seller?
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="secondary"
          @click="registerAsSeller = false"
        >
          Cancel
        </VBtn>
        <VBtn
          color="error"
          @click="registerSeller()"
        >
          Confirm
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <VSnackbar
      v-model="isRegisterAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      You have successfully registered as a seller.
  </VSnackbar>


</template>



