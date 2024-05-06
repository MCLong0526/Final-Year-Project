<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import ShowPosts from '@/components/Post/ShowPosts.vue';
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import { debounce } from 'lodash';
import { watch } from 'vue';
import { VForm } from 'vuetify/components/VForm';


const store = useAuthStore();
const addPostDialog = ref(false)
const addImage = ref(false)
const postPerPage = ref(5)
let page = 1
const showLoading = ref(false)
const content = ref('')
const picture = ref([])
const posts = ref([])
const searchPost = ref('')
const typePosts = ref('all')

//image part:
const handleDrop = (event) => {
  event.preventDefault();
  const files = event.dataTransfer.files;
  handleFiles(files);
};

const handleFiles = (files) => {
  const file = files[0]; // Only handle the first file
  if (file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = (e) => {
      picture.value = [{ url: e.target.result, file }]; // Replace the pictures array with a single image
    };
    reader.readAsDataURL(file);
  }
};

const fileInput = ref(null);

const openFileInput = () => {
  if (fileInput.value) {
    fileInput.value.click();
  }
};

const handleFileInputChange = (event) => {
  const files = event.target.files;
  handleFiles(files);
};

const removeImage = (index) => {
  picture.value.splice(index, 1);
};

//store post
const storePost = () => {
  console.log(picture.value)
  axios.post('/api/posts/store', {
    content: content.value,
    picture: picture.value,
  })
    .then(response => {
      addPostDialog.value = false;
      content.value = '';
      picture.value = [];
      // must reset the posts array to empty to fetch the new post
      posts.value = [];
      getPosts();
    })
    .catch(error => {
      console.log(error);
    });
};

// get posts
const getPosts = debounce(() => {
  let requestURL = '/api/posts?page='+page+'&per_page='+postPerPage.value;
  if (searchPost.value && searchPost.value.length > 2 && searchPost.value !== null) {
    requestURL += '&search=' + searchPost.value;
  }
  axios.get(requestURL)
    .then(({data}) => {
      const newPosts = data.data.data;

      // Change the user's avatar of the new posts by adding the http://127.0.0.1:8000/storage/
      newPosts.forEach(post => {
        if (post.user.avatar) {
          post.user.avatar = 'http://127.0.0.1:8000/storage/' + post.user.avatar;
        }
      });

      // Change also the new posts' picture by adding the http://
      newPosts.forEach(post => {
        if (post.picture) {
          post.picture = 'http://127.0.0.1:8000/storage/' + post.picture;
        }
      });

      // Change the created_at date format to a readable format, and remove seconds
      newPosts.forEach(post => {
        post.created_at = new Date(post.created_at).toLocaleString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: 'numeric',
          minute: 'numeric',
        });
      });

      // Count the number of likes of each new post
      newPosts.forEach(post => {
        post.likes_count = post.likes.length;
      });

      // Check if the new post is liked by the user
      newPosts.forEach(post => {
        post.is_liked = post.likes.some(like => like.user_id === store.user.user_id);
      });

      posts.value = [...posts.value, ...newPosts]; // Append new posts to the existing posts

    })
    .catch(error => {
      console.log(error);
    });
}, 500);


const isBottomOfPage = () => {
  return window.innerHeight + window.scrollY >= document.body.scrollHeight;
};

const fetchPostsIfAtBottom = () => {
  
  if (isBottomOfPage() && posts.value.length % postPerPage.value === 0) {
    showLoading.value = true;
    setTimeout(() => {
      page++;
      getPosts();
      showLoading.value = false;
    }, 1500);
  }
};

window.addEventListener('scroll', fetchPostsIfAtBottom);


watch(searchPost, debounce(() => {
  page = 1;
  posts.value = [];
  getPosts();
}, 500));

getPosts();

</script>


<template>
  <VRow>
    <VCol cols="12" md="9">
      
      <div class="box-style">
        <VRow class="mt-2 ml-2 mr-2">
          <VCol cols="12" md="3">
            <VBtn
              color="primary"
              @click="addPostDialog = true"
            >
              
              <span>Add Post</span>
              <VIcon
                icon="ri-chat-new-line"
                class="ml-2"
              />
            </VBtn>
          </VCol>
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3" >
            <VTextField
              v-model="searchPost"
              label="Search"
              clearable
              prepend-inner-icon="ri-search-line"
              placeholder="Placeholder Text"
            />
          </VCol>
        </VRow>
        <!--All Posts-->
          <ShowPosts 
            :posts="posts"
            :getPosts="getPosts"
            :showLoading="showLoading"
            :typePosts="typePosts"
          />
        </div>

    </VCol>
    <VCol cols="12" md="3">
      <VCard
        title="Followed Users"
      >
        <VCardText>
          <p>
            This is a list of users that you are following.
          </p>
        </VCardText>
      </VCard>
    </VCol>
  </VRow>



  <!--Create New Post-->
  <VDialog
    v-model="addPostDialog"
    max-width="600"
  >
    <!-- Dialog Content -->
    <VCard title="New Post Information">

      <VCardText>
        <VForm 
        ref="refForm" 
        @submit.prevent>
        <VRow>
          <!-- 👉 First Name -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Post Content</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextField
                  v-model="content"
                  prepend-inner-icon="ri-message-2-line"
                  placeholder="What's on your mind?"
                  :rules="[requiredValidator]"
                />
              </VCol>
            </VRow>
          </VCol>
          
          <VCol cols="12" v-if="addImage===true">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="emailHorizontalIcons">Post Image</label>
              </VCol>
              <VCol
                cols="12"
                md="9"
              >
                <div class="upload-container">
                  <input
                    type="file"
                    ref="fileInput"
                    style="display: none"
                    accept="image/*"
                    @change="handleFileInputChange"
                  />
                  <div
                    class="upload-box"
                    @drop.prevent="handleDrop"
                    @dragover.prevent
                    @click="openFileInput"
                  >
                    <VIcon class="upload-icon">ri-image-add-line</VIcon>
                    <p class="upload-text">Drag & Drop your picture here.</p>
                    <p class="upload-text">Click here to upload picture (PNG, JPEG, JPG).</p>
                    <div v-if="picture.length > 0" class="uploaded-images">
                      <p class="upload-text">Selected picture: </p>
                    <div v-for="(image, index) in picture" :key="index" class="uploaded-image">
                      <img :src="image.url" alt="Uploaded Image" />
                      <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
                    </div>
                  </div>
                  </div>
                </div>
              </VCol>
            </VRow>
          </VCol>
       

          <!-- 👉 submit and reset button -->
          <VCol
            offset-md="3"
            cols="12"
            md="9"
            class="d-flex gap-4"
          >
          <VBtn
            color="secondary"
            variant="tonal"
            
            @click="addImage = !addImage"
            >
            <VIcon icon="ri-chat-upload-line" size="20" />
            <VTooltip
                open-delay="500"
                location="top"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>Click here to upload image</span>
              </VTooltip>
          </VBtn>
          <VBtn
              type="submit"
              @click="$refs.refForm.validate().then(() => storePost())"
            >
              Post
            </VBtn>
            
          </VCol>
        </VRow>
      </VForm>
      </VCardText>
    </VCard>
  </VDialog>


</template>

<style scoped>
.upload-container {
  display: flex;
  flex-direction: column;
  align-items: center;
}

.upload-box {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border: 2px dashed #ccc;
  border-radius: 8px;
  block-size: 300px; /* Adjust the height as needed */
  cursor: pointer;
  inline-size: 422px; /* Adjust the width as needed */
  margin-block-end: 10px;
}

.upload-icon {
  font-size: 48px;
}

.upload-text {
  margin-block-start: 8px;
}

.uploaded-images {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin-block-start: 20px;
}

.uploaded-image {
  position: relative;
}

.uploaded-image img {
  border-radius: 8px;
  max-block-size: 100px; /* Adjust the size as needed */
  max-inline-size: 100px; /* Adjust the size as needed */
}

.remove-btn {
  position: absolute;
  inset-block-start: 5px;
  inset-inline-end: 5px;
}

.button-container {
  display: flex;
  justify-content: space-between;
  gap: 440px;
}

.box-style {
  padding: 1.5px; /* Padding around the table */
  border: 0.4px solid #282828;
  border-radius:10px;
  background-color: #fff; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
  margin-block-end: 15px
}

</style>

