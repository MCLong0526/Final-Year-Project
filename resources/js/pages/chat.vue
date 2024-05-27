
<script setup>
import FollowingList from '@/components/Post/FollowingList.vue';
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import 'emoji-picker-element';
import { debounce } from 'lodash';
import { ref } from 'vue';
import { errorMessages } from 'vue/compiler-sfc';
import chatBackground from '/resources/images/avatars/chatbackground.png';

const props = defineProps({
  newChatUser: {
    type: Object,
    required: false
  }
});

const store = useAuthStore();
const newMessage = ref('');
const messages = ref([]);
const messagesShowed = ref([]);
const receiver = ref({});
const clickedMessage = ref({});
const messageClicked = ref(false);
const searchUser = ref('');
const messagePerPage = ref(10);
let page = 1;
const user = ref({});
const showLoading = ref(false);
const showEmoji = ref(false);
const showFile = ref(false);
const images = ref([]);
const isError = ref(false);
const followingUsers = ref([]);
const openFollowedUsersDialog = ref(false);


const addEmoji = (event) => {
  // Access the emoji data from the event object
  const emoji = event.detail.unicode;
  newMessage.value += emoji;

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

// fetch messages of all message related to the authenticated user
const fetchMessages = debounce(() => {
  let requestURL = '/api/chat/messages?page='+page+'&per_page='+messagePerPage.value;
  if(searchUser.value && searchUser.value.length > 2 && searchUser.value !== null && searchUser.value !== '') {
    requestURL += '&search_user=' + searchUser.value;
  }

  axios.get(requestURL)
    .then(({data}) => {
      messages.value = data.data;

      messages.value.forEach(message => {
        if (message.sender.avatar) {
          message.sender.avatar = 'http://127.0.0.1:8000/storage/' + message.sender.avatar;
        }
        if (message.receiver.avatar) {
          message.receiver.avatar = 'http://127.0.0.1:8000/storage/' + message.receiver.avatar;
        }
        if(message.pictures){
          message.pictures.forEach(picture => {
            picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path;
          });
        }
        
      });

  if(props.newChatUser !== null && props.newChatUser !== undefined){
      messageClicked.value = true;
      clickedMessage.value = props.newChatUser;    // so that can know which user is clicked
      receiver.value = props.newChatUser;
      messagesShowed.value = messages.value.filter(message => message.sender.user_id === props.newChatUser.user_id || message.receiver.user_id === props.newChatUser.user_id);
      if(messagesShowed.value.length < 1){
        newMessage.value = 'Hi, I am ' + store.user.username + '. Let\'s start chatting';
      }else{
        setTimeout(() => {
        const chat = document.querySelector('.message');
        chat.scrollTop = chat.scrollHeight;
      }, 100);
      }
      
    }


    // show the messages of the clicked user
    if ((messageClicked.value || showLoading.value === true) && clickedMessage.value.user !== undefined) {
      messagesShowed.value =Object.assign([], messages.value.filter(message => message.sender.user_id === clickedMessage.value.user.user_id || message.receiver.user_id === clickedMessage.value.user.user_id));
      setTimeout(() => {
      const chat = document.querySelector('.message');
      chat.scrollTop = chat.scrollHeight;
      }, 100);
    }

  })
  .catch(error => {
    console.log(error);
  });

  
}, 800);

// show the avatar of the user
const showAvatar = (index) => {
  if (index === 0) {
    return true; // Show avatar for the first message
  }

  const prevMessage = messagesShowed.value[index - 1];
  const currentMessage = messagesShowed.value[index];

  return prevMessage.sender.user_id !== currentMessage.sender.user_id || !prevMessage.sender.avatar;
};


// the list of users with the latest message
const latestUserMessages = computed(() => {
  const userMap = new Map(); // Map to store latest message for each user
  messages.value.forEach(message => {
    const userId = message.sender.user_id !== store.user.user_id ? message.sender.user_id : message.receiver.user_id;
    if (!userMap.has(userId) || message.created_at > userMap.get(userId).created_at) {
      userMap.set(userId, { user: message.sender.user_id !== store.user.user_id ? message.sender : message.receiver, message: message.message, created_at: message.created_at });
    }
  });
  // check also whether there is a picture in the last message or not, if there is a picture, then isPicture will be true
  messages.value.forEach(message => {
    const userId = message.sender.user_id !== store.user.user_id ? message.sender.user_id : message.receiver.user_id;
    if (userMap.has(userId) && message.created_at === userMap.get(userId).created_at) {
      if(message.pictures && message.pictures.length > 0){
        userMap.get(userId).isPicture = true;
      }else{
        userMap.get(userId).isPicture = false;
      }
    }
  });
  return Array.from(userMap.values()); // Convert map values to array
});

// get the messages of the clicked user
const getMessages = (userMessage) => {
  messageClicked.value = true;
  clickedMessage.value = userMessage;    // so that can know which user is clicked
  const userId = userMessage.user.user_id;
  receiver.value = userMessage.user;
  messagesShowed.value = messages.value.filter(message => message.sender.user_id === userId || message.receiver.user_id === userId);
  if(messagesShowed.value.length < 1){
    newMessage.value = 'Hi, I am ' + store.user.username + '. Let\'s start chatting';
  }
  setTimeout(() => {
    const chat = document.querySelector('.message');
    chat.scrollTop = chat.scrollHeight;
  }, 100);
};

//send message
const sendMessage = async () => {
  if(images.value.length==0 && newMessage.value==''){
    errorMessages.value = 'Please enter a message or upload an image';
    isError.value=true;
    return;
  }
  showEmoji.value = false;
  await axios.post('/api/chat/messages', { message: newMessage.value, receiver_id: receiver.value.user_id, images: images.value});
  newMessage.value = '';
  images.value = [];
  showFile.value = false;
  
  // Immediately fetch messages after sending a new message
  fetchMessages();
  
  setTimeout(() => {
    const chat = document.querySelector('.message');
    chat.scrollTop = chat.scrollHeight;
  }, 100);
};


//send message on enter
const sendMessageOnEnter = () => {
    if (newMessage.value.trim() !== '') { // Check if message is not empty
      sendMessage(); // Call sendMessage method
    }
  };

//fetch more messages
const fetchMoreMessage = () => {
  messagePerPage.value += 10;

  fetchMessages();
};

//image part:
const handleDrop = (event) => {
  event.preventDefault();
  const files = event.dataTransfer.files;
  handleFiles(files);
};

const handleFiles = (files) => {
  for (let i = 0; i < files.length; i++) {
    const file = files[i];
    if (file.type.startsWith('image/')) {
      const reader = new FileReader();
      reader.onload = (e) => {
        images.value.push({ url: e.target.result, file });
      };
      reader.readAsDataURL(file);
    }
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
  images.value.splice(index, 1);
};


//listen for new messages, so that the messages can be updated in real-time
var pusher = new Pusher('bfdcd4030f09a5a101b7',{
  cluster: 'ap1'
});
var channel = pusher.subscribe('my-channel');
    channel.bind('MessageSent', function(data) {
      fetchMessages();
      app.messages.push(JSON.stringify(data));
    });

const app = {
  messages: []
};

//watch for changes in the search input
watch(searchUser, debounce(() => {
  
  if(searchUser.value.length > 2) {
    page = 1;
    //clear latestUserMessages
    messages.value = [];
    latestUserMessages.value = [];

    fetchMessages();
  }else{
    fetchMessages();
    return;
  }
  
}, 800));

fetchMessages();
getUser();
getFollowingUsers();
</script>

<template>
  <VContainer>

    <!-- Chat box in chat page-->
    <h1 v-if="props.newChatUser==null">Messages</h1>
    <VCard v-if="props.newChatUser==null" class="chat-box">
    <VRow no-gutters>
      <VCol cols="12" md="3">
        <VCard class="user-list">
          <div class="d-flex mt-3 mb-3 ml-3" >
            <VRow>
              <VCol cols="12" md="2">
                <VAvatar v-if="user.avatar" size="40">
                  <VImg :src="user.avatar" />
                  
                </VAvatar>
              </VCol>
              <VCol cols="12" md="10">
                <VTextField
                  v-model="searchUser"
                  class="mr-3"
                  label="Search User"
                  prepend-inner-icon="ri-search-line"
                  placeholder="Placeholder Text"
                  rounded
                  density="compact"

                />
              </VCol>
            </VRow>
          </div>
          <VDivider />
          <VCardTitle>
            <VRow align="center" justify="space-between">
              <VCol class="d-flex align-start">
                <span style="font-size: 20px; font-weight: 100;">Chats</span>
              </VCol>
              <VCol class="d-flex justify-end">
                <VTooltip
                  open-delay="500"
                  location="right"
                  activator="parent"
                  transition="scroll-x-transition"
                >
                  <span>Click to have a chat with your friends</span>
                </VTooltip>
                <VBtn
                  icon="ri-chat-new-line"
                  variant="text"
                  @click="openFollowedUsersDialog = true"
                />
              </VCol>
            </VRow>
          </VCardTitle>
          <VCardText>
            <!-- Display latest message for each user -->
            <VList density="compact">
              
              <VListItem v-for="userMessage in latestUserMessages" :key="userMessage.user_id" @click="getMessages(userMessage)"
                class="font-weight-medium text-high-emphasis text-truncate">
                <div class="d-flex align-items-center">
                  <VAvatar v-if="userMessage.user.avatar" size="35">
                    <VImg :src="userMessage.user.avatar" />
                  </VAvatar>
                  <div class="ml-2">
                    <div style="font-size: 13px;">{{ userMessage.user.username }}</div>
                    <div v-if="userMessage.message !== null" style="color: #6c757d; font-size: 12px;">{{ userMessage.message }}</div>
                    <div v-else-if="userMessage.isPicture && userMessage.message==null" style="color: #6c757d; font-size: 12px;"><VIcon icon="ri-camera-3-line" color="secondary"/> Photo</div>
                    <div v-else-if="!userMessage.isPicture && userMessage.message == null" style="color: #6c757d; font-size: 12px;">No message yet</div>

                  </div>
                </div>
                <hr style="position: absolute; border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;">
              </VListItem>
            </VList>
          </VCardText>

        </VCard>
        </VCol>

        <!-- Chat box -->
        <VCol v-if="messageClicked===true" cols="12" md="9" >

        <VCard class="user-list">
          <!-- Receiver information -->

          <div class="d-flex align-items-center mt-3 mb-3 ml-3" >
            <VAvatar v-if="receiver.avatar" size="40">
              <VImg :src="receiver.avatar" />
            </VAvatar>
            <div class="ml-2">
              <div>{{ receiver.username }}</div>
              <div style="color: #6c757d; font-size: 12px;">{{ receiver.email }}</div>
            </div>
          </div>

          <VDivider />
          <VCardText>
            <!-- Display messages -->
            
            <div v-if="messagesShowed.length > 0" class="message">
              <!--button to load more message-->
              <div class="text-center">
                <VBtn v-if="messagesShowed.length > 9" variant="text" @click="fetchMoreMessage" color="primary">
                  <VIcon class="mr-1" icon="ri-arrow-up-circle-line" /> Load more messages
                </VBtn>
              </div>

              <div v-for="(message, index) in messagesShowed" :key="message.id" :class="{'right-message': message.sender.user_id === store.user.user_id, 'left-message': message.sender.user_id !== store.user.user_id}">
                <div v-if="message.sender.user_id !== store.user.user_id" class="message-container left-message">
                  <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
                  <span >
                    <VChip v-if="showAvatar(index)" label class="mt-1 ml-2 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="display: inline-block; text-align: start;">
                          <div v-if="message.pictures && message.pictures.length">
                            <div class="mt-2"></div>
                            <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                            <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                          </div>
                          <div v-if="message.message !== null">
                            <template v-if="message.message.length > 40">
                                <span v-html="message.message.match(/.{1,40}/g).join('<br>')"></span>
                            </template>
                              <template v-else>
                              {{ message.message}}
                            </template>
                          </div>
                      </div>
                    </VChip>
                    <VChip v-else label class="mt-1 ml-11 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="display: inline-block; text-align: start;">
                        <div v-if="message.pictures && message.pictures.length" >
                          <div class="mt-2"></div>
                            <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                            <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                            <template v-if="message.message.length > 40">
                                <span v-html="message.message.match(/.{1,40}/g).join('<br>')"></span>
                            </template>
                              <template v-else>
                              {{ message.message}}
                            </template>
                          </div>
                    </div>
                    </VChip>
                  </span>
                </div>
                <div v-else class="message-container right-message">
                  <span>
                    <VChip v-if="showAvatar(index)" label class="mt-1 mr-2 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="flex-wrap: wrap;" >
                        <div v-if="message.pictures && message.pictures.length">
                          <div class="mt-2"></div>
                          <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                          <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                            <template v-if="message.message.length > 40">
                                <span v-html="message.message.match(/.{1,40}/g).join('<br>')"></span>
                            </template>
                              <template v-else>
                              {{ message.message}}
                            </template>
                          </div>
                      </div>
                    </VChip>
                    <VChip v-else label class="mt-1 mr-11 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="flex-wrap: wrap;" >
                        <div v-if="message.pictures && message.pictures.length">
                          <div class="mt-2"></div>
                          <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                          <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                            <template v-if="message.message.length > 40">
                                <span v-html="message.message.match(/.{1,40}/g).join('<br>')"></span>
                            </template>
                              <template v-else>
                              {{ message.message}}
                            </template>
                          </div>
                      </div>
                    </VChip>
                  </span>
                  <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
      
                </div>
              </div>
            </div>

            <div v-else class="text-center" style=" margin-block:40px 40px">
              <VCardTitle>Messages</VCardTitle>
              <VImg :src="chatBackground" style=" block-size: auto;inline-size: 100px; margin-block-start: 20px;margin-inline-start:220px"/>
              <VCardTitle class="text-overline">You guys are friends on UNIMAS Web Application</VCardTitle>
              <VCardText>
                <VList>
                  <VListItem>
                    <VListItemTitle>Let's start chatting</VListItemTitle>
                  </VListItem>
                </VList>
              </VCardText>
            </div>
            
            <!-- Message input -->
            <VRow class="mt-4 mb-0 ml-2 mr-2" style="position: absolute; inset-block-end: 0; inset-inline: 0 0;">
              <emoji-picker
                  v-if="showEmoji===true" 
                  @emoji-click="addEmoji" 
                  theme="light"
                  :native="true"
                  class="light ml-3"
   
        
                ></emoji-picker>

                <VCard v-if="showFile">
                  <div class="upload-container ml-2 mt-2 mr-2">
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
                      <p class="upload-text">Drag & Drop your images here.</p>
                      <p class="upload-text">Click here to upload images (PNG, JPEG, JPG).</p>
                      <div v-if="images.length > 0" class="uploaded-images">
                        <p class="upload-text">Selected images: </p>
                      <div v-for="(image, index) in images" :key="index" class="uploaded-image">
                        <img :src="image.url" alt="Uploaded Image" />
                        <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
                      </div>
                    </div>
                    </div>
                  </div>
                </VCard>
               
              <VCol cols="12">
         
                <VTextField 
                  v-model="newMessage" 
                  label="Type your message here" 
                  @keyup.enter="sendMessageOnEnter" 
                  :append-inner-icon="showEmoji ? 'ri-emotion-happy-line' : 'ri-emotion-happy-line'"
                  @click:append-inner="showEmoji = !showEmoji"
                >
                  <template #append>
                    <VBtn @click="showFile = !showFile" variant="text" class="mr-1"><VIcon :icon="showFile ? 'ri-close-line' : 'ri-attachment-line'"/></VBtn>
                    <VBtn @click="sendMessage"><VIcon icon="ri-send-plane-fill"/></VBtn>
                  </template>
                  
                </VTextField>
                
                
              </VCol>
              
            </VRow>
            
          </VCardText>
          
        </VCard>
        
      </VCol>
      <VCol v-else cols="12" md="9" >
        <VRow>
          <VCol cols="12" md="3">
            <!--Add a image here (avatar-11) by using the import on script setup-->
            <VImg :src="chatBackground" style=" block-size: auto;inline-size: 600px; margin-block-start: 160px; margin-inline-start: 50px;"/>
          </VCol>
          <VCol cols="12" md="9">
            <div class="text-center" style=" margin-block:180px 180px">
          <VCardTitle><VIcon icon="ri-emotion-happy-line" color="success"/> Chit Chat with Friends</VCardTitle>
          <VCardTitle class="text-overline">Try to make friends on UNIMAS Web Application</VCardTitle>
          <VCardText>
            <VList>
              <VListItem>
                <VListItemTitle class="text-overline">If you haven't followed anyone,</VListItemTitle>
                <VListItemTitle class="text-overline"><b> you can follow someone in the Life Moment Post page</b></VListItemTitle>
              
              </VListItem>
            </VList>
          </VCardText>
          </div>
          </VCol>
        </VRow>
      </VCol>
    </VRow>
  </VCard>




  <!-- Chat box in life moment post-->
  <VCard v-else>
    <VCard class="user-list">
      <!-- Receiver information -->

      <div class="d-flex align-items-center mt-3 mb-3 ml-3" >
        <VAvatar v-if="props.newChatUser.avatar" size="50">
          <VImg :src="props.newChatUser.avatar" />
        </VAvatar>
        <div class="ml-2">
          <div>{{ props.newChatUser.username }}</div>
          <div style="color: #6c757d; font-size: 12px;">{{ props.newChatUser.email }}</div>
        </div>
      </div>

      <VDivider />
      <VCardText>
        <!-- Display messages -->
        
        <div v-if="messagesShowed.length>0" class="message">
          <!--button to load more message-->
          <div class="text-center">
            <VBtn v-if="messagesShowed.length>9" variant="text" @click="fetchMoreMessage" color="primary"><VIcon class="mr-1" icon="ri-arrow-up-circle-line"/> Load more messages</VBtn>
          </div>
          
          <div v-for="(message, index) in messagesShowed" :key="message.id" :class="{'right-message': message.sender.user_id === store.user.user_id, 'left-message': message.sender.user_id !== store.user.user_id}">
            <div v-if="message.sender.user_id !== store.user.user_id" class="message-container left-message">
              <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
              <span>
                <VChip v-if="showAvatar(index)" label class="mt-1 ml-2 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                  <div style="display: inline-block; text-align: start;">
                      <div v-if="message.pictures && message.pictures.length">
                        <div class="mt-2"></div>
                        <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                        <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                      </div>
                      <div v-if="message.message !== null">
                        <template v-if="message.message.length > 40">
                            <span v-html="message.message.match(/.{1,40}/g).join('<br>')"></span>
                        </template>
                          <template v-else>
                          {{ message.message}}
                        </template>
                      </div>
                  </div>
                </VChip>
                <VChip v-else label class="mt-1 ml-11 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                  <div style="display: inline-block; text-align: start;">
                    <div v-if="message.pictures && message.pictures.length" >
                      <div class="mt-2"></div>
                        <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                        <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                    </div>
                    <div v-if="message.message !== null">
                        <template v-if="message.message.length > 40">
                            <span v-html="message.message.match(/.{1,40}/g).join('<br>')"></span>
                        </template>
                          <template v-else>
                          {{ message.message}}
                        </template>
                      </div>
                </div>
                </VChip>
              </span>
            </div>
            <div v-else class="message-container right-message">
              <span>
                <VChip v-if="showAvatar(index)" label class="mt-1 mr-2 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="flex-wrap: wrap;" >
                        <div v-if="message.pictures && message.pictures.length">
                          <div class="mt-2"></div>
                          <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                          <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                            <template v-if="message.message.length > 40">
                                <span v-html="message.message.match(/.{1,40}/g).join('<br>')"></span>
                            </template>
                              <template v-else>
                              {{ message.message}}
                            </template>
                          </div>
                      </div>
                    </VChip>
                    <VChip v-else label class="mt-1 mr-11 mb-1" dense :style="{ height: 'auto', 'min-height': '32px', 'line-height': '16px' }">
                      <div style="flex-wrap: wrap;" >
                        <div v-if="message.pictures && message.pictures.length">
                          <div class="mt-2"></div>
                          <VImg v-for="picture in message.pictures" :key="picture.picture_id" :src="picture.picture_path" alt="Message Image" class="mb-2" :class="{'right-message-picture': message.sender.user_id === store.user.user_id, 'left-message-picture': message.sender.user_id !== store.user.user_id}" style=" block-size: auto;inline-size: 200px;" />
                          <hr class="mt-1 mb-1" style="border-block-start: 0 solid; inset-block-end: 0; inset-inline: 0 0;"/>
                        </div>
                        <div v-if="message.message !== null">
                            <template v-if="message.message.length > 40">
                                <span v-html="message.message.match(/.{1,40}/g).join('<br>')"></span>
                            </template>
                              <template v-else>
                              {{ message.message}}
                            </template>
                          </div>
                      </div>
                    </VChip>
              </span>
              <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
            </div>
          </div>
        </div>

        <div v-else class="text-center" style=" margin-block:40px 40px">
          <VCardTitle>Messages</VCardTitle>
          <VImg :src="chatBackground" style=" block-size: auto;inline-size: 100px; margin-block-start: 20px;margin-inline-start:220px"/>
          <VCardTitle class="text-overline">You guys are friends on UNIMAS Web Application</VCardTitle>
          <VCardText>
            <VList>
              <VListItem>
                <VListItemTitle>Let's start chatting</VListItemTitle>
              </VListItem>
            </VList>
          </VCardText>
        </div>
        
         <!-- Message input -->
         <VRow class="mt-4 mb-0 ml-2 mr-2" style="position: absolute; inset-block-end: 0; inset-inline: 0 0;">
              <emoji-picker
                  v-if="showEmoji===true" 
                  @emoji-click="addEmoji" 
                  theme="light"
                  :native="true"
                  class="light ml-3"
   
        
                ></emoji-picker>

                <VCard v-if="showFile">
                  <div class="upload-container ml-2 mt-2 mr-2">
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
                      <p class="upload-text">Drag & Drop your images here.</p>
                      <p class="upload-text">Click here to upload images (PNG, JPEG, JPG).</p>
                      <div v-if="images.length > 0" class="uploaded-images">
                        <p class="upload-text">Selected images: </p>
                      <div v-for="(image, index) in images" :key="index" class="uploaded-image">
                        <img :src="image.url" alt="Uploaded Image" />
                        <VBtn @click="removeImage(index)" icon="ri-close-line" class="remove-btn"/>
                      </div>
                    </div>
                    </div>
                  </div>
                </VCard>
               
              <VCol cols="12">
         
                <VTextField 
                  v-model="newMessage" 
                  label="Type your message here" 
                  @keyup.enter="sendMessageOnEnter" 
                  :append-inner-icon="showEmoji ? 'ri-emotion-happy-line' : 'ri-emotion-happy-line'"
                  @click:append-inner="showEmoji = !showEmoji"
                >
                  <template #append>
                    <VBtn @click="showFile = !showFile" variant="text" class="mr-1"><VIcon :icon="showFile ? 'ri-close-line' : 'ri-attachment-line'"/></VBtn>
                    <VBtn @click="sendMessage"><VIcon icon="ri-send-plane-fill"/></VBtn>
                  </template>
                  
                </VTextField>
                
                
              </VCol>
              
            </VRow>
      </VCardText> 
    </VCard>
  </VCard>


  </VContainer>

  <VDialog
    v-model="openFollowedUsersDialog"
    width="400"
    scrollable
    style="max-block-size: 350px; overflow-y: auto;"
  >
      <VCard
        title="Followed Users"
      >
        <FollowingList
          :followingUsers="followingUsers"
        />
      </VCard>
  </VDialog>

  <!-- Error Snackbar -->
  <VSnackbar
      v-model="isError"
      location="top end"
      transition="scale-transition"
      color="error"
    >
    <VIcon size="20" class="me-2">ri-error-warning-line</VIcon>
    <span>{{ errorMessages.value }}</span>
  </VSnackbar>
</template>


<style scoped>
.chat-box {
  block-size: 550px; /* Set the desired height */
  inline-size: 900px; /* Set the width to 100% */
}

.user-list {
  block-size: 550px; /* Set the desired height */
  overflow-y: auto; /* Add a scrollbar */
}

.chat-list{
  block-size: 550px; /* Set the desired height */
}

.chat{
  block-size: 400px; /* Set the desired height */
  overflow-y: auto; /* Add a scrollbar */
}

.left-message {
  text-align: end;
}

.right-message {
  text-align: start;
}

.avatar {
  border-radius: 50%;
  block-size: 35px;
  inline-size: 35px;
}

.message{
  max-block-size: 390px; /* Set a maximum height for the message container */
  overflow-y: auto; /* Enable vertical scrolling */
  
}

.message-container {
  display: flex;
  flex-wrap: nowrap; /* Prevent messages from wrapping */
  margin-block-end: 5px; /* Add margin between messages */
  margin-inline: 0; /* Reset left and right margins */
}


.left-message .message-container {
  justify-content: flex-start;
}

.right-message .message-container {
  justify-content: flex-end;
}

/* Ensure that messages without avatars align properly */
.message-container.no-avatar {
  justify-content: flex-start; /* Align to the left for messages without avatars */
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
  inline-size: 400px; /* Adjust the width as needed */
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


.left-message-picture {
  border-radius: 8px;
  margin-block-end:20px;
  max-block-size: 200px; /* Adjust the size as needed */
  max-inline-size: 200px; /* Adjust the size as needed */
}

.right-message-picture {
  border-radius: 8px;
  max-block-size: 200px; /* Adjust the size as needed */
  max-inline-size: 200px; /* Adjust the size as needed */
}

.image-box {
  padding: 5px; /* Add some padding to give space around the images */
  border: 1px solid #ccc; /* You can adjust the border style and color as needed */
  margin-block-end: 10px; /* Add margin at the bottom to separate from other content */
}


</style>
