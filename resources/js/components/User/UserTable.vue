<script setup>
import axios from 'axios';
import { defineProps } from 'vue';

const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
  usersLoad: {
    type: Function,
    required: true,
  },
});

const clickedUser = ref(null);
const deleteDialog = ref(false);

const onClickDeleteUser = (userSelected) => {
  clickedUser.value = userSelected;
  deleteDialog.value = true;

};

const deleteUser = () => {
  
  //user_id is the primary key of the user table, need to write user_id = clickedUser.user_id
  axios.delete(`/api/users/delete/${clickedUser.value.user_id}`)
    .then(() => {
      deleteDialog.value = false;
      props.usersLoad();
    })
    .catch((error) => {
      console.log(error);
    });

};

</script>

<template>
  <VTable
    height="250"
    fixed-header
  >
    <thead>
      <tr>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-user-line" />
          User ID
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-shield-user-line" />
          Username
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-phone-line" />
          Phone Number
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-mail-line" />
          Email
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-shield-check-line" />
          Status
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-exchange-funds-line" />
          Modify
        </th>
      </tr>
    </thead>

    <tbody>
      <tr
        v-for="item in users"
        :key="item.user_id"
      >
        <td class="text-center">
          {{ item.user_id }}
        </td>
        <td class="font-weight-medium text-high-emphasis text-center text-truncate">
          {{ item.username }}
        </td>
        <td class="text-center">
          {{ item.phone_number }}
        </td>
        <td class="text-center">
          {{ item.email }}
        </td>
        <td class="text-center">
          <VChip
            color="success"
            v-if="item.status === 'active'"
          >
          Active
          <VIcon
            icon="ri-checkbox-circle-line"
            style="margin-inline-start: 5px;"
          />
           
          </VChip>
          <VChip
            color="error"
            v-else
            >
            Inactive
            <VIcon
              icon="ri-close-circle-line"
              style="margin-inline-start: 5px;"
            />
            
          </VChip>
        </td>
        <td class="text-center">
          <VBtn
            color="info"
           
            style="margin-inline: 15px 3px"
            size="small"
          >
            <VIcon
              icon="ri-pencil-line"
            />
            <VTooltip
                open-delay="500"
                location="top"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>Edit</span>
              </VTooltip>
          </VBtn>
          <VBtn
            color="error"
            style="margin-inline: 5px 5px;"
            size="small"
            @click="onClickDeleteUser(item)"
          >
            <VIcon
              icon="ri-delete-bin-line"
            />
            <VTooltip
                open-delay="500"
                location="top"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>Delete</span>
              </VTooltip>
          </VBtn>
        </td>
      </tr>
    </tbody>
  </VTable>

  <!-- Delete Dialog -->
  <VDialog
    v-model="deleteDialog"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog Content -->
    <VCard title="Delete User?">    
      <VCardText>
        <VAlert
          color="error"
          icon="ri-alert-line"
          variant="tonal"
        >
        Are you sure you want to delete <strong>{{ clickedUser.username }}</strong>?
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="secondary"
          @click="deleteDialog = false"
        >
          Cancel
        </VBtn>
        <VBtn
          color="error"
          @click="deleteUser"
        >
          Delete
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

