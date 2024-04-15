<template>
  <VContainer>
    <VRow>
      <VCol cols="12">
        <h1>Message Test</h1>

        <VCard>
          <!-- Receiver information -->
          <div v-if="receiver" class="font-weight-medium text-high-emphasis text-truncate mt-3 mb-3 ml-3" style="display: flex; align-items: center;">
            <VAvatar size="45"
                      :color="receiver.avatar ? '' : 'primary'"
                      :class="`${!receiver.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
                      :variant="!receiver.avatar ? 'tonal' : undefined"
                      style="margin-inline-end: 8px;">
                      <VImg
                      :src="receiver.avatar"/>
                    </VAvatar>
            <div style="display: flex; flex-direction: column; align-items: flex-start;">
              <VCardTitle>{{ receiver.username }}</VCardTitle>
              <VCardSubtitle>{{ receiver.email }}</VCardSubtitle>
            </div>
          </div>

          <VDivider />
          <VCardText>
            <!-- Display messages -->
            <div v-for="(message, index) in messages" :key="message.id" :class="{'right-message': message.sender.user_id === store.user.user_id, 'left-message': message.sender.user_id !== store.user.user_id}">
              <div v-if="message.sender.user_id !== store.user.user_id" class="message-container left-message">
                <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
                <span><VChip label class="mt-1 ml-2 mb-1">{{ message.message }}</VChip></span>
              </div>
              <div v-else class="message-container right-message">
                <span><VChip label color="primary" class="mt-1 mr-2 mb-1">{{ message.message }}</VChip></span>
                <img v-if="showAvatar(index)" :src="message.sender.avatar" alt="Avatar" class="avatar">
              </div>
            </div>

            <!-- Message input -->
            <VRow class="mt-2">
              <VCol cols="12" md="11">
                <VTextField v-model="newMessage" label="Type your message here"></VTextField>
              </VCol>
              <VCol cols="12" md="1">
                <VBtn  @click="sendMessage"><VIcon icon="ri-send-plane-fill"/></VBtn>
              </VCol>
            </VRow>

          </VCardText>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>

<script setup>
import { useAuthStore } from '@/plugins/store/AuthStore';
import axios from 'axios';
import { ref } from 'vue';
const store = useAuthStore();


const newMessage = ref('');
const messages = ref([]);
const receiver = ref({});
const user = ref({});

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


const fetchMessages = async () => {
  const response = await axios.get('/api/chat/messages');
  messages.value = response.data.data;

  messages.value.forEach(message => {
    if (message.sender.avatar) {
      message.sender.avatar = 'http://127.0.0.1:8000/storage/' + message.sender.avatar;
    }
    if (message.receiver.avatar) {
      message.receiver.avatar = 'http://127.0.0.1:8000/storage/' + message.receiver.avatar;
    }
  });

  if (messages.value[0].receiver.user_id !== store.user.user_id) {
    receiver.value = messages.value[0].receiver;
  } else {
    receiver.value = messages.value[0].sender;
  }
};

const sendMessage = async () => {
  await axios.post('/api/chat/messages', { message: newMessage.value, receiver_id: receiver.value.user_id});
  newMessage.value = '';
  fetchMessages();
};

const showAvatar = (index) => {
  if (index === 0) {
    return true; // Always show the avatar for the first message
  }

  const currentHasAvatar = messages.value[index].sender.avatar !== null;
  const previousHasAvatar = messages.value[index - 1].sender.avatar !== null;

  return currentHasAvatar || previousHasAvatar;
};

fetchMessages();
//listen for new messages
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
getUser();
</script>

<style scoped>
.left-message {
  text-align: start;
}

.right-message {
  text-align: end;
}

.avatar {
  border-radius: 50%;
  block-size: 30px;
  inline-size: 30px;
}

.message-container {
  display: flex;
  flex-wrap: nowrap; /* Prevent messages from wrapping */
  align-items: center;
  margin-block-end: 10px; /* Add margin between messages */
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

</style>
