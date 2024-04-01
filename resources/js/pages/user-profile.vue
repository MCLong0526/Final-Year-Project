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

// get posts
const getPosts = debounce(() => {
  axios.get('/api/posts/get-auth-posts')
    .then(response => {
      posts.value = response.data.data;

      // Change the user's avatar of the new posts by adding the http://127.0.0.1:8000/storage/
      posts.value.forEach(post => {
        if (post.user.avatar) {
          post.user.avatar = 'http://127.0.0.1:8000/storage/' + post.user.avatar;
        }
      });

      // Change also the new posts' picture by adding the http://
      posts.value.forEach(post => {
        if (post.picture) {
          post.picture = 'http://127.0.0.1:8000/storage/' + post.picture;
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

watch(currentTab, (newValue) => {
  if (newValue === 'my-posts') {
    getPosts();
  }
});



getPosts();

</script>


<template>
  <div style="display: flex;">
    <div style="inline-size: 50%;">
      <UserProfileHeader class="mb-5" />
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
      </VWindow>
    </div>
  </div>

</template>



