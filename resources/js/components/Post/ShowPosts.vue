<script setup>
import axios from 'axios';
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

const addCommentDialog = ref(false)
const selectedComment = ref(null); // Initialize as null or with a default value

const comments = ref([])
const newComment = ref('')
const selectedPost = ref(null) 
const showLikeMenu = ref(false)

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
      props.getPosts();
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
const deletePost = (post) => {
  axios.delete('/api/posts/delete/' + post.post_id)
    .then(response => {
      console.log(response.data);
      props.getPosts();
    })
    .catch(error => {
      console.log(error);
    });
};


</script>

<template>
  <!--All Posts-->
  <div class="mt-2">
    <VList scrollable height="600" style=" background-color: transparent;max-inline-size: 2000px; overflow-x: hidden;">
      <VRow align="center" justify="center">
        <VCol v-for="post in posts" :key="post.post_id" cols="12">
          <VCard variant="outlined" class="mx-auto" max-width="600" style="border-radius: 20px; background-color: #f8f9fa;">
            <VCardItem>
              <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                  <VAvatar v-if="post.user.avatar" size="40">
                    <VImg :src="post.user.avatar" />
                  </VAvatar>
                  <div class="ml-2">
                    <div>{{ post.user.username }}</div>
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
                        <VBtn variant="text" >
                          <VIcon icon="ri-pencil-line" />
                        </VBtn>
                          
                      </VListItem>
                      <VListItem>
                        <VBtn  variant="text" @click="deletePost(post)">
                          <VIcon icon="ri-delete-bin-6-line" />
                        </VBtn>

                      </VListItem>
                    </VList>
                </VMenu>
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
                <span>View {{ post.comments.length }} Comments</span>
              
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

</style>
