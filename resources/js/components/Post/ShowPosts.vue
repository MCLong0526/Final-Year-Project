<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';
// import { useRouter } from 'vue-router';
import { VForm } from 'vuetify/components/VForm';

const props = defineProps({
  posts: {
    type: Object,
    required: true,
  },
  getPosts: {
    type: Function,
    required: true,
  },
  showLoading: {
    type: Boolean,
    required: true,
  },
  typePosts: {
    type: String,
    required: true,
  },
})

// const router = useRouter();
const addCommentDialog = ref(false)
const selectedComment = ref(null); // Initialize as null or with a default value

const comments = ref([])
const newComment = ref('')
const selectedPost = ref(null) 
const showLikeMenu = ref(false)
const editPostDialog = ref(false)
const addImage = ref(false)
const isPostSuccessAlert = ref(false)
const deletePostDialog = ref(false)
const snackType = ref(null)
const errorMessage = ref(null)

const followDialog = ref(false)
const clickedUser = ref([])
const user = ref([]);
const isFollowErrorAlert = ref(false)
const isFollowSuccessAlert = ref(false)

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

  axios.post('/api/comments/store', requestData)
    .then(response => {
      addCommentDialog.value = false;
      clearSelectedComment();
      newComment.value = '';     
    
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

//show likes
const showLike = (post) => {
  showLikeMenu.value = true;

};

//delete post
const deleteDialog = (post) => {
  deletePostDialog.value = true;
  selectedPost.value = post;
};

const deletePost = () => {
  axios.delete('/api/posts/delete/' + selectedPost.value.post_id)
    .then(response => {
      snackType.value = 'delete';
      isPostSuccessAlert.value = true;
      deletePostDialog.value = false;
      props.getPosts();
    })
    .catch(error => {
      console.log(error);
    });
};


//image part:
const handleDrop = (event) => {
  event.preventDefault();
  const files = event.dataTransfer.files;
  handleFiles(files);
};


const handleFiles = (files) => {
  const file = files[0]; // Only handle the first file

  if (file && file.type.startsWith('image/')) {
    const reader = new FileReader();
    reader.onload = (e) => {
      const base64Data = e.target.result;

      // Update the selectedPost.picture_path only if a new image is selected
      if (!selectedPost.value.picture || selectedPost.value.picture !== base64Data) {
        selectedPost.value.picture = base64Data;
        selectedPost.value.file = file; // Store the file object for uploading
      }
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

const removeImage = () => {
  if (!selectedPost.value.picture.isNew) {
    // If the removed image is not new, update its picture_path to base64 format
    fetch(selectedPost.value.picture.picture_path)
      .then((response) => response.blob())
      .then((blob) => {
        const reader = new FileReader();
        reader.onload = (e) => {
          selectedPost.value.picture.picture_path = e.target.result;
          // Do something with the updated picture_path, such as sending it to the server
        };
        reader.readAsDataURL(blob);
      });
  }
  // Clear the picture
  selectedPost.value.picture = null;
};

//edit post
const editDialog=(post)=>{
  editPostDialog.value = true;
  selectedPost.value = Object.assign({}, post);
  selectedPost.value.picture = post.picture;
}

const editPost = (post) => {
  console.log(post);

  axios.put('/api/posts/update/' + post.post_id, {
    content: post.content,
    picture: post.picture,
  })
    .then(response => {
      snackType.value = 'edit';
      editPostDialog.value = false;
      isPostSuccessAlert.value = true;
      props.getPosts();
    })
    .catch(error => {
      console.log(error);
    });
};
//edit done.

const openFollowDialog = (selectedUser) => {
  if(selectedUser.user_id!=user.value.user_id){
    

    axios.post('/api/users/follow-user/' + selectedUser.user_id)
      .then(response => {
        console.log(response.data);
        isFollowSuccessAlert.value = true;
        followDialog.value=true;
        clickedUser.value = selectedUser;
      })
      .catch(error => {
        
        errorMessage.value = error.response.data.message;
        isFollowErrorAlert.value = true;
        console.log(error);
      });
  }
};


getUser();
</script>

<template>
  <!--All Posts-->
  <div class="mt-2">
    <VList v-if="posts.length>0" scrollable height="600" style=" background-color: transparent; max-inline-size: 2000px; overflow-x: hidden;">
      <VRow align="center" justify="center">
        <VCol v-for="post in posts" :key="post.post_id" cols="12">
          <VCard variant="outlined" class="mx-auto" max-width="600" style="border-radius: 20px; background-color: #f8f9fa;">
            <VCardItem>
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <VTooltip
                    open-delay="500"
                    location="top"
                    activator="parent"
                    transition="scroll-y-transition"
                  >
                    <span v-if="post.user.user_id==user.user_id">This is your post</span>
                    <span v-if="post.user.user_id!=user.user_id && post.is_following==true">Already following {{ post.user.username }}</span>
                    <span v-if="post.user.user_id!=user.user_id && post.is_following==false">Click to follow {{ post.user.username }}</span>
                  </VTooltip>
                  <VAvatar v-if="post.user.avatar" size="40">
                    <VImg :src="post.user.avatar" />
                  </VAvatar>
                  <div class="ml-2">
                    <div>{{ post.user.username }} 
                      <VChip
                        v-if="post.user.user_id!=user.user_id && post.is_following==true"
                        color="success"
                        label="You"
                        size="small"
                        class="ml-2"
                      >
                        <VIcon size="16" class="me-1">ri-check-line</VIcon>
                        <span class="text-xs">Following</span>
                      </VChip>
                      <VBtn
                        v-if="post.user.user_id!=user.user_id && post.is_following==false"
                        color="primary"
                        size="x-small"
                        class="ml-2"
                        @click="openFollowDialog(post.user)"
                      >
                        <VIcon size="16" class="me-1">ri-user-follow-line</VIcon>
                  
                      <span class="text-xs">Follow</span>
                      </VBtn>
                    </div>
                    
                    <div style="color: #6c757d; font-size: 12px;">{{ post.user.email }} &middot; <span style="font-style: italic;">{{ post.created_at }}</span></div>
                  </div>
                </div>
               
                <div v-if="typePosts=='private'" class="ml-auto">
                  <VMenu transition="slide-x-transition">
                    <template #activator="{ props }">
                      <VBtn v-bind="props"
                        class="ml-auto"
                        icon="ri-more-2-line"
                        variant="text"
                      />
                    </template>
                    <VList >
                      <VListItem>
                        <VBtn variant="text" @click=editDialog(post)>
                          <VIcon icon="ri-pencil-line" />
                        </VBtn>
                          
                      </VListItem>
                      <VListItem>
                        <VBtn  variant="text" @click="deleteDialog(post)">
                          <VIcon icon="ri-delete-bin-6-line" />
                        </VBtn>

                      </VListItem>
                    </VList>
                </VMenu>
             </div>
              
              </div>
              <VImg v-if="post.picture" height="300" :src="post.picture" class=" mt-4" />
            </VCardItem>

            <VCardActions>
              <VBtn icon color="primary" class="ml-3 mr-1" @click="toggleLike(post)">
                <VIcon :class="post.is_liked ? 'ri-thumb-up-fill' : 'ri-thumb-up-line'" />
              </VBtn>
              <VBtn color="primary" icon @click="addComment(post)">
                <VIcon icon="ri-chat-3-line" />
              </VBtn>
            </VCardActions>
            <VBtn
              v-if="post.likes_count > 0"
              color="secondary"
              @click="showLikeMenu=true"
              size="xs-small"
              variant="plain"
              class="ml-2 mb-2"
            >
              {{ post.likes_count }} likes
             
            </VBtn>
            <VBtn
              v-else
              color="secondary"
              @click="showLike(post)"
              size="xs-small"
              variant="plain"
              class="ml-2 mb-2"
            >
              0 like
            </VBtn>

            <div class="text-h6 ml-6 mb-3">{{ post.content }}</div>

            <VCardActions v-if="post.comments.length > 0">
              <VBtn
                color="secondary"
                @click="addComment(post)"
                size="small"
                class="ml-1"
              >
                <span>View all {{ post.comments.length }} Comments</span>
              
              </VBtn>
            </VCardActions>
            <VCardActions v-else>
              <VBtn
                color="secondary"
                @click="addComment(post)"
                size="small"
                class="ml-1"
              >
                <span>Add a comment...</span>
              </VBtn>
            </VCardActions>
            
          </VCard>
        </VCol>
      </VRow>

      <VRow v-if="showLoading" align="center" justify="center">

        <VProgressCircular
          color="rgb(var(--v-theme-primary))"
          indeterminate
          striped
        />
      </VRow>
    </VList>
    <VList v-else style=" background-color: transparent;max-inline-size: 2000px; overflow-x: hidden;">
      <VAlert  
        variant="tonal"
        type="warning"
        class="mt-2 text-center"
        color="primary"
        dense
      >
        No posts available
      </VAlert>
    </VList>
  </div>

  


  <!--Comments-->
 <VDialog
  v-model="addCommentDialog"
  max-width="600"
  scrollable
  >
    <!-- Dialog Content -->
    <VCard title="Comments">

      <VCardText v-if="comments.length >0" class="comment-scrollable">
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
      <VCardText v-else>
        <div class="text-center"><VAlert variant="outlined" color="primary">No comments yet, be the first to comment!</VAlert></div>
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


  <!--Create New Post-->
  <VDialog
    v-model="editPostDialog"
    max-width="600"
  >
    <!-- Dialog Content -->
    <VCard title="Edit Post Information">

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
                  v-model="selectedPost.content"
                  prepend-inner-icon="ri-message-2-line"
                  placeholder="What's on your mind?"
                  :rules="[requiredValidator]"
                />
              </VCol>
            </VRow>
          </VCol>
          
          <VCol cols="12" v-if="addImage === true">
            <VRow no-gutters>
              <VCol cols="12" md="3">
                <label for="emailHorizontalIcons">Post Image</label>
              </VCol>
              <VCol cols="12" md="9">
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
                    <div v-if="selectedPost.picture" class="uploaded-images">
                      <p class="upload-text">Selected picture:</p>
                      <div class="uploaded-image">
                        <img :src="selectedPost.picture" alt="Uploaded Image" />
                        <VBtn @click="removeImage()" icon="ri-close-line" class="remove-btn" />
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
              @click="$refs.refForm.validate().then(() => editPost(selectedPost))"
            >
              Edit
            </VBtn>
            
          </VCol>
        </VRow>
      </VForm>
      </VCardText>
    </VCard>
  </VDialog>

   <!--Delete Dialog-->
   <VDialog
    v-model="deletePostDialog"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog Content -->
    <VCard title="Delete Post">  
      <VCardText>
        <VAlert
          color="error"
          icon="ri-alert-line"
          variant="tonal"
        >
          Are you sure you want to delete this post?
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="secondary"
          @click="deletePostDialog = false"
        >
          Close
        </VBtn>
        <VBtn
          color="error"
          @click="deletePost"
        >
          Delete Post
        
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

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
          You are now following {{ clickedUser.username }}!<br>
          <b>Let's chat with {{ clickedUser.username }}.</b>
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

  <!-- Approved Successfully Order -->
  <VSnackbar
      v-model="isPostSuccessAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
    <span v-if="snackType==='edit'">Post updated successfully!</span>
    <span v-if="snackType==='delete'">Post deleted successfully!</span>
  </VSnackbar>
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
    <span>You are now following {{ clickedUser.username }}!</span>
  </VSnackbar>

 
</template>

<style scoped>
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
</style>
