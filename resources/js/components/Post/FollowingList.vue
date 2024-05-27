<script setup>
import Chat from '@/pages/chat.vue';

const props = defineProps({
  followingUsers: {
    type: Object,
    required: true
  }
});

const newChatUser = ref({});
const dialogChat = ref(false);

const openChatDialog = (user) => {
  newChatUser.value = user;
  dialogChat.value = true;
};

</script>

<template>
  <VList
    lines="two"
    border
    rounded
    style="max-block-size: 200px; overflow-y: auto;"
  >
    <template
      v-for="(user, index) in props.followingUsers"
      :key="index"
    > 
      <VListItem>
        <template #prepend>
          <VAvatar v-if="user.avatar" size="50">
            <VImg :src="user.avatar" />
          </VAvatar>
        </template>
        <VListItemTitle class="">
          {{ user.username }}
        </VListItemTitle>
        <VListItemSubtitle class="mt-1">
          <span class="text-caption" >{{ user.email }}</span>
        </VListItemSubtitle>

        <template #append>
          <VBtn variant="tonal" size="small" @click="openChatDialog (user)">
            <VIcon icon="ri-chat-3-line" class="mr-1"/>
             Chat
          </VBtn>
        </template>
      </VListItem>
      <VDivider v-if="index !== props.followingUsers.length - 1" />
    </template>
  </VList>

  <VDialog
    v-model="dialogChat"
    width="600"
  >
    <Chat :newChatUser="newChatUser" />
  </VDialog>
  
</template>
