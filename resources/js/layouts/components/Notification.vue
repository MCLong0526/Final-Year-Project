<script setup>
import axios from 'axios';

const notifications = ref([]);
const userIdForNotification = ref(0);
const unreadNotification = ref(0);

const getNotification = () => {
  axios.get('/api/notifications/get-auth-notifications')
    .then(response => {
      notifications.value = response.data.data
      
      // sort the notifications status by 'Unread' first, and then 'Read', and count the number of unread notifications
      unreadNotification.value = notifications.value.filter(notification => notification.status === 'Unread').length


      //sort the notifications by the latest
     
      notifications.value.sort((a, b) => {
        // change the created_at to date format dd/mm/yyyy hh:mm am/pm
        a.created_at = new Date(a.created_at).toLocaleString('en-US', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        })
        b.created_at = new Date(b.created_at).toLocaleString('en-US', {
          day: '2-digit',
          month: '2-digit',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          hour12: true
        })
        return new Date(b.created_at) - new Date(a.created_at)
      })
      
      if (notifications.value.length > 0)
      {
        userIdForNotification.value = notifications.value[0].receiver_id
      }

    })
    .catch(error => {
      console.log(error)
    })
}

const read = (notification) => {
  axios.put(`/api/notifications/mark-as-read/${notification.id}`)
    .then(response => {
      notification.status = 'Read'
      getNotification()
    })
    .catch(error => {
      console.log(error)
    })
}

const readAll = (user_id) => {
  axios.put('/api/notifications/mark-all-read/' + user_id)
    .then(response => {
      getNotification()
    })
    .catch(error => {
      console.log(error)
    })
}

getNotification()

</script>

<template>
  <IconBtn class="me-2">
    <VBadge
      v-if="unreadNotification > 0"
      color="primary"
      :content="unreadNotification"
    >
      <VIcon
        size="25"
        icon="ri-notification-line"
      />
    </VBadge>
    <VBadge
      v-else
      dot
      color="secondary"
    >
      <VIcon
        size="25"
        icon="ri-notification-line"
      />
    </VBadge>
      <VMenu
        activator="parent"
        :close-on-content-click="false"
        transition="slide-x-transition"
        width="450"
        height="400"
        location="bottom end"
        offset="14px"
      >
      <VList
        lines="two"
        border
        rounded
        scrollable
        lazy
      >
        <VRow>
          <VCol cols="12" md="3">
            <VCardText class="pb-3 pt-3">
                <h3>Notifications</h3>
            </VCardText>
          </VCol>
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3">
            <VBtn
              variant="text"
              icon="ri-mail-open-line"
              class="ml-6 mt-1"
              @click="readAll(userIdForNotification)"
              >
              <VIcon icon="ri-mail-open-line" />
              <VTooltip 
                activator="parent"
                transition="scroll-x-transition"
                location="end"
                open-delay="500"
                >
                Mark all as read
                </VTooltip>
              </VBtn>
          </VCol>
        </VRow>

          <VDivider />
             
            <template
              v-for="(notification, index) of notifications"
              :key="notification.id"
            >
              <VListItem :class="{ 'unread-notification': notification.status === 'Unread' }">
                <template #prepend>
                  <VAvatar :image="notification.sender.avatar" />
                </template>
                <VListItemTitle>
                  {{ notification.sender.username }} <VChip class="ml-1" color="primary" size="x-small">{{ notification.created_at }}</VChip>
                </VListItemTitle>
                <VListItemSubtitle class="mt-1">
                  <div class="status-dot" :class="{ 'status-dot-read': notification.status === 'Read' }">
                    <span class="text-xs text-disabled">{{ notification.information }}</span>
                  </div>

                  
                </VListItemSubtitle>

                <template #append>
                  <VBtn v-if="notification.status=='Unread'" variant="text" color="secondary" icon="ri-close-line" @click="read(notification)">
                    <VIcon icon="ri-close-line" />
                    <VTooltip
                      activator="parent"
                      location="end"
                      transition="scroll-x-transition"
                      open-delay="500"
                    >
                      Mark as read
                    </VTooltip>
                  </VBtn>
                </template>
              </VListItem>
              <VDivider v-if="index !== notifications.length - 1" />
            </template>
          </VList>

      </VMenu>
 </IconBtn>

</template>

<style scoped>
.status-dot::before {
  color: #ffc107;
  content: '\2022'; /* Unicode character for a bullet point */
  font-size: 20px; /* Size of the dot */
  margin-inline-end: 10px; /* Spacing between the dot and the text */
}

.status-dot-read::before {
  color: #ccc; /* Grey color for read notifications */
}

.unread-notification {
  background-color: rgba(161, 161, 183, 23.3%); /* Light red background for unread notifications */
}
</style>
