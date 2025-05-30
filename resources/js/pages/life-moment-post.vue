<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import FollowingList from '@/components/Post/FollowingList.vue';
import ShowPosts from '@/components/Post/ShowPosts.vue';
import UserList from '@/components/Post/UserList.vue';
import { useAuthStore } from '@/plugins/store/AuthStore';
import { p } from '@antfu/utils';
import axios from 'axios';
import { debounce } from 'lodash';
import { watch } from 'vue';
import { VForm } from 'vuetify/components/VForm';

import findNewUsersBackground from '/resources/images/avatars/Find.png';

//define props relatedId from other component
const props = defineProps({
  relatedId: {
    type: String,
    default: '',
  },
});

const emit = defineEmits(['update:relatedId']);
const store = useAuthStore();
const user = ref([]);
const addPostDialog = ref(false)
const addImage = ref(false)
const postPerPage = ref(5)
let page = 1
const showLoading = ref(false)
const content = ref('')
const picture = ref([])
const posts = ref([])
const postsFollow = ref([])
const searchPost = ref('')
const searchPostFollow = ref('')
const typePosts = ref('all')
const isPostSuccessAlert = ref(false)
const followingUsers = ref([])
const followingUsersInPost = ref([])
const isTagging = ref(false)
const taggedPost = ref({})
const isTaggedPostDialog = ref(false)
const currentTab = ref('tab-1')
const openFindUserDialog = ref(false)
const searchUsername = ref('')

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

    console.error(error);
  }
};


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
  axios.post('/api/posts/store', {
    content: content.value,
    picture: picture.value,
  })
    .then(response => {
      addPostDialog.value = false;
      content.value = null;
      picture.value = [];
      // must reset the posts array to empty to fetch the new post
      posts.value = [];
      isPostSuccessAlert.value = true;
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
  if(props.relatedId){
    posts.value = [];
    requestURL += '&related_id=' + props.relatedId;
  }
  axios.get(requestURL)
    .then(({data}) => {
      const newPosts = data.data.unrelated_posts.data;
      // Change the user's avatar of the new posts by adding the http://127.0.0.1:8000/storage/
      newPosts.forEach(post => {
        if (post.user.avatar) {
          post.user.avatar = '/storage/' + post.user.avatar;
        }
      });

      // Change also the new posts' picture by adding the http://
      newPosts.forEach(post => {
        if (post.picture) {
          post.picture = '/storage/' + post.picture;
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

      // Update isFollowing status for each tagged post based on API data
      posts.value.forEach(post => {
        if (followingUsers.value.some(user => user.user_id === post.user.user_id)) {
          post.is_following = true;
        } else {
          post.is_following = false;
        }
      });

      posts.value = [...posts.value, ...newPosts];

      // make sure there is no duplicate post, check by post_id, if got duplicate, remove the first one, keep the last one (for update the following status)
      posts.value = posts.value.reverse().filter((post, index, self) =>
        index === self.findIndex((t) => t.post_id === post.post_id)
      ).reverse();

      // Check if the related post is not empty
      if(props.relatedId){
        taggedPost.value = data.data.related_posts.data;

        // Change also the new posts' picture by adding the http://
        taggedPost.value.forEach(post => {
          if (post.picture) {
            post.picture = '/storage/' + post.picture;
          }
        });

        //make sure the post.user.avatar is in full path
        taggedPost.value.forEach(post => {
          if (post.user.avatar) {
            post.user.avatar = '/storage/' + post.user.avatar;
          }
        });

        // Change the created_at date format to a readable format, and remove seconds
        taggedPost.value.forEach(post => {
          post.created_at = new Date(post.created_at).toLocaleString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: 'numeric',
            minute: 'numeric',
          });
        });
        // Count the number of likes of each tagged post
        taggedPost.value.forEach(post => {
          post.likes_count = post.likes.length;
        });

        // Check if the new post is liked by the user
        taggedPost.value.forEach(post => {
          post.is_liked = post.likes.some(like => like.user_id === store.user.user_id);
        });

        isTaggedPostDialog.value = true;

      }
      
    })
    .catch(error => {
      console.log(error);
    });
}, 500);


// get posts
const getFollowPosts = debounce(() => {
  let requestURL = '/api/posts/follows?page='+page+'&per_page='+postPerPage.value;
  if (searchPostFollow.value && searchPostFollow.value.length > 2 && searchPostFollow.value !== null) {
    requestURL += '&search=' + searchPostFollow.value;
  }

  axios.get(requestURL)
    .then(({data}) => {
      const newPostsFollow = data.data.follows_posts.data;
      // Change the user's avatar of the new posts by adding the http://127.0.0.1:8000/storage/
      newPostsFollow.forEach(post => {
        if (post.user.avatar) {
          post.user.avatar = '/storage/' + post.user.avatar;
        }
      });

      // Change also the new posts' picture by adding the http://
      newPostsFollow.forEach(post => {
        if (post.picture) {
          post.picture = '/storage/' + post.picture;
        }
      });

      // Change the created_at date format to a readable format, and remove seconds
      newPostsFollow.forEach(post => {
        post.created_at = new Date(post.created_at).toLocaleString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: 'numeric',
          minute: 'numeric',
        });
      });

      // Count the number of likes of each new post
      newPostsFollow.forEach(post => {
        post.likes_count = post.likes.length;
      });

      // Check if the new post is liked by the user
      newPostsFollow.forEach(post => {
        post.is_liked = post.likes.some(like => like.user_id === store.user.user_id);
      });

      // Update isFollowing status for each tagged post based on API data
      postsFollow.value.forEach(post => {
        if (followingUsers.value.some(user => user.user_id === post.user.user_id)) {
          post.is_following = true;
        } else {
          post.is_following = false;
        }
      });

      postsFollow.value = [...postsFollow.value, ...newPostsFollow];

      // make sure there is no duplicate post, check by post_id, if got duplicate, remove the first one, keep the last one (for update the following status)
      postsFollow.value = postsFollow.value.reverse().filter((post, index, self) =>
        index === self.findIndex((t) => t.post_id === post.post_id)
      ).reverse();
      
    })
    .catch(error => {
      console.log(error);
    });
}, 500);



// Check if the user has reached the bottom of the page
const isBottomOfPage = () => {
  return window.innerHeight + window.scrollY <= document.body.offsetHeight;
};


let fetching = false; // Flag to track fetching process

const fetchPostsIfAtBottom = () => {
  if (!fetching && isBottomOfPage()&& posts.value.length % postPerPage.value === 0) {
    fetching = true; // Set fetching flag to true
    page += 1; // Increment page number
    
    showLoading.value = true; // Show loading indicator
    setTimeout(() => {
      if(currentTab.value === 'tab-1'){
        getPosts(); // Fetch posts
      } else {
        getFollowPosts(); // Fetch posts
      }

      fetching = false; // Reset fetching flag once posts are fetched
      showLoading.value = false; // Hide loading indicator
    }, 1500);
  }
};


const getNextPosts = () => {
  page += 1;
  if(currentTab.value === 'tab-1'){
    getPosts();
  } else {
    getFollowPosts();
  }
};

const getFollowingUsers = () => {
  axios.get('/api/users/get-following')
    .then(response => {
    
      followingUsers.value = response.data.data;
    })
    .catch(error => {
      console.log(error)
    })
}

window.addEventListener('scroll', fetchPostsIfAtBottom);

watch(searchPost, debounce(() => {
  page = 1;
  posts.value = [];
  getPosts();
}, 500));

watch(searchPostFollow, debounce(() => {
  page = 1;
  postsFollow.value = [];
  getFollowPosts();
}, 500));

// Tagging users in post
watch(content, () => {
  setTimeout(() => {
    if (content.value.charAt(content.value.length - 1) === '@') {
      isTagging.value = true;
      followingUsersInPost.value = followingUsers.value;
    } else if (content.value.includes('@') && isTagging.value===false && followingUsersInPost.value.length > 0) {
      isTagging.value = true;
      const searchTerm = content.value.split('@').slice(-1)[0].split(' ')[0].toLowerCase();
      followingUsersInPost.value = followingUsers.value.filter(user => user.username.toLowerCase().includes(searchTerm));
    } else {
      isTagging.value = false;
      
    }
  }, 500);
});

const tagUser = (user) => {
  // Remove the word that is being typed to search for the user before tagging the user
  const contentWithoutSearchTerm = content.value.split('@').slice(0, -1).join('@');
  // Add the tagged user's username to the content with a space
  content.value = `${contentWithoutSearchTerm}@${user.username} `;
  followingUsersInPost.value = [];
  isTagging.value = false;
};

//end tagging users in post

const newUsers = ref([]);

const searchNewUsers = debounce(() => {
  axios.get('/api/users/search-new-users?search=' + searchUsername.value)
    .then(response => {
      newUsers.value = response.data.data;
    })
    .catch(error => {
      console.log(error);
    });
}, 500);

watch(() => searchUsername.value, () => {
  if (searchUsername.value.length > 2) {
    searchNewUsers();
  } else {
    newUsers.value = [];
  }
});

// watch when props.relatedId is change, then get the posts
watch(() => props.relatedId, (value) => {
  if (value) {
    getPosts();
  }
});

// watch when props.relatedId is change to '', then refresh the page
watch(() => props.relatedId, (value) => {
  if (value === '') {
    window.location.reload();
  }
});

watch(() => currentTab, (value) => {
  if (value === 'tab-1') {
    getPosts();
  } else {
    getFollowPosts();
  }
});

watch(() => openFindUserDialog.value, (value) => {
  // if the openFindUserDialog is false, then reset the followingUsers array and run the getFollowingUsers function
  if (!value) {
    followingUsers.value = [];
    getFollowingUsers();
    searchUsername.value = '';
    newUsers.value = [];
  }
});


getPosts();
getFollowPosts();
getUser();
getFollowingUsers();

</script>


<template>
  <VRow>
    <VCol cols="12" md="8">
      <div class="box-style">
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
                  md="1"
                >
                <div class="d-flex align-items-center" >
                  <VAvatar v-if="user.avatar" size="50">
                    <VImg :src="user.avatar" />
                  </VAvatar>
                </div>
                </VCol>

                <VCol
                  cols="12"
                  md="11"
                >
                  <VTextField
                    v-model="content"
                    prepend-inner-icon="ri-message-2-line"
                    rounded
                    placeholder="What's on your mind?"
                    :rules="[requiredValidator]"
                  />
                  <VList 
                    v-if="isTagging && followingUsers.length > 0"  
                    style="max-block-size: 80px; overflow-y: auto;"
                  >
                    <VListItem
                      v-for="(user, index) in followingUsersInPost"
                      :key="index"
                      @click="tagUser(user)"
                     
                    >
                    <VAvatar size="32"
                      :color="user.avatar ? '' : 'primary'"
                      :class="`${!user.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
                      :variant="!user.avatar ? 'tonal' : undefined"
                      style="margin-inline-end: 8px;">
                      <VImg
                      :src="user.avatar"/>
                    </VAvatar>
                    {{ user.username }}
                    </VListItem>
                  </VList>
                </VCol>
                
              </VRow>
            </VCol>
            
            <VCol cols="12" v-if="addImage===true">
              <VRow no-gutters>
                <VCol
                  cols="12"
                  md="3"
                />
                
                <VCol
                  cols="12"
                  md="8"
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
              <span class="ml-2">Upload Image</span>
              <VTooltip
                  open-delay="500"
                  location="top"
                  activator="parent"
                  transition="scroll-y-transition"
                >
                  <span>Click here to upload image!</span>
                </VTooltip>
            </VBtn>
            <VBtn
              type="submit"
              @click="$refs.refForm.validate().then(() => storePost())"
            >
              <VIcon icon="ri-send-plane-2-line" size="20" />
              <span class="ml-2">Create Post </span>
              <VTooltip
                  open-delay="500"
                  location="top"
                  activator="parent"
                  transition="scroll-y-transition"
                >
                  <span>Click here to add new post!</span>
                </VTooltip>
            </VBtn>
            
          </VCol>
          </VRow>
        </VForm>
        </VCardText>
        <!-- </VCard> -->
      </div>
      
      <div class="box-style">
        

        <VTabs
          v-model="currentTab"
          grow
          stacked
        >
          <VTab value="tab-1">
            <VIcon
              icon="ri-message-2-line"
              class="mb-2"
            />
            <span>All Posts</span>
          </VTab>

          <VTab value="tab-2">
            <VIcon
              icon="ri-chat-heart-line"
              class="mb-2"
            />
            <span>Followed Users Posts</span>
          </VTab>
          </VTabs>

        <VWindow
          v-model="currentTab"
          class="mt-5"
        >
          <VWindowItem
            value="tab-1"
          >
            <VRow class="mt-1 ml-2 mr-2">
              <VCol cols="12" md="7" >
                
                <VBtn 
                  @click="getNextPosts()"
                  color="primary"
                  class="mb-2"
                >
                  <VIcon icon="ri-arrow-down-s-line" size="20" />
                  <span class="ml-2">Load More</span>
                </VBtn>

                
              </VCol>
              <VCol cols="12" md="5" >
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
              :getFollowingUsers="getFollowingUsers"
              :showLoading="showLoading"
              :typePosts="typePosts"
              :followingUsers="followingUsers"
              
            />

          </VWindowItem>
          <VWindowItem
            value="tab-2"
          >
            <VRow class="mt-1 ml-2 mr-2">
              <VCol cols="12" md="7" />
              
              <VCol cols="12" md="5" >
                <VTextField
                  v-model="searchPostFollow"
                  label="Search"
                  clearable
                  prepend-inner-icon="ri-search-line"
                  placeholder="Placeholder Text"
                />
              </VCol>
            </VRow>
            <!--All Posts-->
            <ShowPosts 
              :posts="postsFollow"
              :getPosts="getFollowPosts"
              :getFollowingUsers="getFollowingUsers"
              :showLoading="showLoading"
              :typePosts="typePosts"
              :followingUsers="followingUsers"
              
            />
          </VWindowItem>
        </VWindow>
        
        
        </div>

    </VCol>
    <VCol cols="12" md="4">
      <VCard>
        <VCardTitle>
          <VRow class="d-flex align-center justify-space-between">
            <VCol>
              <span>Followed Users</span>
            </VCol>
            <VCol class="d-flex justify-end">
              <VBtn icon="ri-user-search-line" variant="text" @click="openFindUserDialog=true"/>
            </VCol>
          </VRow>
        </VCardTitle>

        <FollowingList :followingUsers="followingUsers" />
      </VCard>
      
    </VCol>
    
  </VRow>


  <VDialog
    v-model="openFindUserDialog"
    max-width="450"
  >
    <VCard class="mt-2">
      <VCardTitle>
        <VRow class="d-flex align-center justify-space-between">
          <VCol>
            <span>Find New Users</span>
          </VCol>
          <VCol class="d-flex justify-end">
            <VTextField
              v-model="searchUsername"
              label="Search New Users"
              append-inner-icon="ri-user-search-line"
        
              placeholder="Enter username"
            />
          </VCol>
        </VRow>
      </VCardTitle>
      <VDivider />
      <div class="dialog-content" style="max-block-size: 400px; overflow-y: auto;">
        <VRow v-if="newUsers.length > 0">
            <UserList :newUsers="newUsers" :searchNewUsers="searchNewUsers" />
        </VRow>
        <VRow v-else-if="newUsers.length===0 && searchUsername.length>2">
          <VAlert  
            variant="tonal"
            type="warning"
            class="mt-2 mb-2 ml-2 mr-2 text-center"
            color="primary"
            dense
          >
            No new users found
          </VAlert>
        </VRow>
        <VRow v-else-if="newUsers.length===0 && searchUsername.length<=2">
          <VCol cols="12" md="3">
            <VImg :src="findNewUsersBackground" style="block-size: auto; inline-size: 600px; margin-block-start: 65px; margin-inline-start: 50px;" />
          </VCol>
          <VCol cols="12" md="9">
            <div class="text-center" style="margin-block: 70px 70px">
              <VCardTitle>
                <span>Find New Users</span>
              </VCardTitle>
              <VCardText>
                <span>Find new users to follow and connect with them.</span>
              </VCardText>
            </div>
          </VCol>
        </VRow>
      </div>
    </VCard>
  </VDialog>


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

  <!-- Tagged Posts Popup -->
  <VDialog
  v-model="isTaggedPostDialog"
  max-width="600"
  persistent
>
  <VCard>
    <VCardTitle class="text-overline text-center mt-2" style="font-size: 20px !important;">
      Related Post
    </VCardTitle>
    <VCardText style=" block-size: 260px; overflow-y: auto;">
      <ShowPosts 
        :posts="taggedPost"
        :getPosts="getPosts"
        :getFollowingUsers="getFollowingUsers"
        :showLoading="showLoading"
        :typePosts="typePosts"
        :followingUsers="followingUsers"
      />
    </VCardText>
    <VCardActions>
      <VSpacer />
      <VBtn 
        :to="{ name: 'life-moment-post', params: { relatedId: '' } }"
        color="primary"
        @click="isTaggedPostDialog = false"
        class="ma-2"
      >
      <VTooltip
          open-delay="500"
          location="top"
          activator="parent"
          transition="scroll-y-transition"
        >
          <span>Click here to view all posts!</span>
        </VTooltip>
        <VIcon icon="ri-chat-3-line" size="20" />
        <span class="ml-2">All Posts</span>
      </VBtn>
    </VCardActions>
  </VCard>
</VDialog>



   <!-- Approved Successfully Order -->
   <VSnackbar
      v-model="isPostSuccessAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
    <span>Post created successfully!</span>
  </VSnackbar>


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

.dialog-content {
  max-block-size: 400px; /* Adjust the height as needed */
  overflow-y: auto;
}

.background-image {
  block-size: auto;
  inline-size: 600px;
  margin-block-start: 50px;
  margin-inline-start: 50px;
}
</style>

