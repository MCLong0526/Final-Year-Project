<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import { VForm } from 'vuetify/components/VForm';

const store = useAuthStore();
const addPostDialog = ref(false)
const addImage = ref(false)
const addCommentDialog = ref(false)
const selectedComment = ref(null); // Initialize as null or with a default value

const content = ref('')
const picture = ref([])
const posts = ref([])
const comments = ref([])
const newComment = ref('')
const selectedPost = ref(null) 
const searchPost = ref('')

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
      getPosts();
    })
    .catch(error => {
      console.log(error);
    });
};

// get all posts
const getPosts = () => {
  axios.get('/api/posts')
    .then(response => {
      posts.value = response.data.data;

      // change the user's avatar of the posts by adding the http://127.0.0.1:8000/storage/
      posts.value.forEach(post => {
        if (post.user.avatar) {
          post.user.avatar = 'http://127.0.0.1:8000/storage/' + post.user.avatar;
        }
      });

      // change also the post's picture by adding the http://
      posts.value.forEach(post => {
        if (post.picture) {
          post.picture = 'http://127.0.0.1:8000/storage/' + post.picture;
        }
      });

      //change the created_at date format to a readable format, and remove seconds
      posts.value.forEach(post => {
        post.created_at = new Date(post.created_at).toLocaleString('en-US', {
          year: 'numeric',
          month: 'long',
          day: 'numeric',
          hour: 'numeric',
          minute: 'numeric',
        });
      });

      //count the number of likes of each post
      posts.value.forEach(post => {
        post.likes_count = post.likes.length;
      });

      //check if the post is liked by the user
      posts.value.forEach(post => {
        post.is_liked = post.likes.some(like => like.user_id === store.user.user_id);
      });

      
      console.log(posts.value);
    })
    .catch(error => {
      console.log(error);
    });
};

//like and unlike a post
const toggleLike = (post) => {
  const postId = post.post_id;

  if (post.is_liked) {
    // Post is already liked, so unlike it
    axios.post('/api/posts/unlike/' + postId)
      .then(response => {
        console.log(response.data);
        post.is_liked = false;
        post.likes_count--;
      })
      .catch(error => {
        console.log(error);
      });
  } else {
    // Post is not liked, so like it
    axios.post('/api/posts/like/' + postId)
      .then(response => {
        console.log(response.data);
        post.is_liked = true;
        post.likes_count++;
      })
      .catch(error => {
        console.log(error);
      });
  }
};

// comment part:  
//add comment
const addComment = async(post) =>{
  addCommentDialog.value = true;
  selectedPost.value = post.post_id;
  await axios.get('/api/comments/get-comments-by-post-id/' + selectedPost.value)
    .then(response => {
      // Separate comments based on parent_id
      const parentComments = response.data.data.filter(comment => comment.parent_id === null);
      const childComments = response.data.data.filter(comment => comment.parent_id !== null);

      // Map child comments to their respective parent comments
      const commentsMap = new Map();
      parentComments.forEach(parentComment => {
        commentsMap.set(parentComment.id, []);
      });
      childComments.forEach(childComment => {
        const parentId = childComment.parent_id;
        if (commentsMap.has(parentId)) {
          commentsMap.get(parentId).push(childComment);
        }
      });

      // Combine parent and child comments
      const combinedComments = parentComments.map(parentComment => ({
        ...parentComment,
        replies: commentsMap.get(parentComment.id) || [],
      }));

      comments.value = combinedComments;

      console.log(comments.value);
    })
    .catch(error => {
      console.log(error);
    });
}


const addNewComment = (post) => {
  const requestData = {
    content: newComment.value,
    post_id: post,
  };
  // Check if the comment is a reply to another comment
  if (selectedComment.value) {
    if(selectedComment.value.parent_id!==null)
    {
      requestData.parent_id = selectedComment.value.parent_id;
      requestData.replier_id = selectedComment.value.user.user_id;
    }
    else{
      requestData.parent_id = selectedComment.value.id;
      requestData.replier_id = selectedComment.value.user.user_id;
    }
  }
  else {
    requestData.parent_id = null;
    requestData.replier_id = null;
  }

  console.log(requestData);

  axios.post('/api/comments/store', requestData)
    .then(response => {
      console.log(response.data);
      addCommentDialog.value = false;
      clearSelectedComment();
      newComment.value = '';
      getPosts();
    })
    .catch(error => {
      console.log(error);
    });
};


// Function to set the selected comment when a user clicks on a comment to reply
const selectComment = (comment) => {
  if (selectedComment.value === comment) {
    clearSelectedComment();
  } else {
    selectedComment.value = comment;
  }

};

// Function to clear the selected comment (e.g., when the user cancels or submits the reply)
const clearSelectedComment = () => {
  selectedComment.value = null;
};

// comment function done. 




getPosts();

</script>


<template>
  <VRow>
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
        prepend-inner-icon="ri-search-line"
        placeholder="Placeholder Text"
      />
    </VCol>
      
  </VRow>
  


  <!--All Posts-->
  <div class="mt-2">
    <VList scrollable height="600" style="max-inline-size: 2000px; overflow-x: hidden;">
      <VRow align="center" justify="center">
        <VCol v-for="post in posts" :key="post.post_id" cols="12">
          <VCard variant="outlined" class="mx-auto" max-width="600">
            <VCardItem>
              <div class="d-flex align-items-center">
                <VAvatar v-if="post.user.avatar" size="40">
                  <VImg :src="post.user.avatar" />
                </VAvatar>
                <div class="ml-2">
                  <div>{{ post.user.username }}</div>
                  <div style="color: #6c757d; font-size: 12px;">{{ post.user.email }} &middot; <span style="font-style: italic;">{{ post.created_at }}</span></div>
                </div>
              </div>
              <VImg v-if="post.picture" height="300" :src="post.picture" class="mb-4" />
            </VCardItem>
            <VCardActions>
              <VBtn icon color="primary" class="ml-3 mr-1" @click="toggleLike(post)">
                <VIcon :class="post.is_liked ? 'ri-thumb-up-fill' : 'ri-thumb-up-line'" />
              </VBtn>
              <VBtn color="primary" icon @click="addComment(post)">
                <VIcon icon="ri-chat-3-line" />
              </VBtn>
            </VCardActions>
            <h4 v-if="post.likes_count > 0" class="ml-6 mb-2">{{ post.likes_count }} Likes</h4>
            <h4 v-else class="ml-6 mb-2"><strong>0</strong> Like</h4>
            <div class="text-h6 ml-6 mb-3">{{ post.content }}</div>

            <VCardActions v-if="post.comments.length > 0">
              <VBtn
                color="secondary"
                @click="addComment(post)"
                size="small"
                class="ml-1"
              >
                <span>View {{ post.comments.length }} Comments</span>
              
              </VBtn>
            </VCardActions>
          </VCard>
        </VCol>
      </VRow>
    </VList>
  </div>


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


 <!--Comments-->
 <VDialog
  v-model="addCommentDialog"
  max-width="600"
  scrollable
>
  <!-- Dialog Content -->
  <VCard title="Comments">

    <VCardText class="comment-scrollable">
      <!-- Display all comments -->
      <template v-for="comment in comments" :key="comment.id">
        <!-- Main comments -->
        <div v-if="!comment.parent_id" class="comment-container">
          <div class="d-flex align-items-center">
            <VAvatar v-if="comment.user.avatar" size="40">
              <VImg :src="comment.user.avatar" />
            </VAvatar>
            <div class="ml-2">
              <div style="font-size: 11px;">{{ comment.user.username }}</div>
              <div style="color: #6c757d; font-size: 16px;">{{ comment.content }}</div>
            </div>
          </div>
          <!-- Reply button -->
          <VBtn style="margin-inline-start: 30px;" size="xs-small" variant="text" @click="selectComment(comment)" class="mb-2">
            <div style="font-size: 12px;">Reply</div>
          </VBtn>
        </div>

        <!-- Replies for this comment -->
        <div v-if="comment.replies.length > 0" class="replies-container">
          <template v-for="reply in comment.replies" :key="reply.id">
            <div class="reply-container">
              <div class="d-flex align-items-center">
                <VAvatar v-if="reply.user.avatar" size="30">
                  <VImg :src="reply.user.avatar" />
                </VAvatar>
                <div class="ml-2">
                  <div style="font-size: 11px;">{{ reply.user.username }}</div>
                  <div v-if="reply.replier_id" style="color: #6c757d; font-size: 12px;">Replying to {{ reply.replier.username }}</div>
                  <div style="color: #6c757d; font-size: 14px;">{{ reply.content }}</div>
                </div>
              </div>
              <!-- Reply button -->
              <VBtn style="margin-inline-start: 30px;" size="xs-small" variant="text" @click="selectComment(reply)" class="mb-2">

                <div style="font-size: 10px;">Reply</div>
              </VBtn>
            </div>
          </template>
        </div>
      </template>
    </VCardText>

      <!-- Add new comment -->
      <VDivider />
      <div class="mb-2 ml-2 mt-2">
      <VForm ref="refForm" @submit.prevent >
        <VTextField
          v-model="newComment"
          :label="selectedComment ? 'Replying to ' + selectedComment.user.username : 'New Comment'"
          required
          outlined
        >
          <template v-slot:append>
            <VBtn class="mr-2" :color="selectedComment ? 'primary' : 'success'" @click="$refs.refForm.validate().then(() => addNewComment(selectedPost))">
              <VIcon :icon="selectedComment ? 'ri-chat-new-line' : 'ri-send-plane-2-line'" />
            </VBtn>
          </template>
        </VTextField>
      </VForm>
    </div>
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

.replies-container {
  margin-inline-start: 30px; /* Indent the replies */
}

.reply {
  border-inline-start: 2px solid #ccc; /* Add a border to indicate it's a reply */
  margin-block-start: 10px; /* Add space between replies */
  padding-inline-start: 10px; /* Add padding to the left of the reply content */
}

.comment-scrollable {
  max-block-size: 400px; /* Set the max height for scrollable area */
  overflow-y: auto; /* Add vertical scrollbar if content exceeds max height */
}

</style>

