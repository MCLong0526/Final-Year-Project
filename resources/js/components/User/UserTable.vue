<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';
import { ref } from 'vue';


const props = defineProps({
  users: {
    type: Object,
    required: true,
  },
  usersLoad: {
    type: Function,
    required: true,
  },
  roles: {
    type: Object,
    required: true,
  },
});

const clickedUser = ref(null);
const deleteDialog = ref(false);
const isDeleteAlert = ref(false);
const editDialog = ref(false);
const isEditAlert = ref(false);
const deactivatedDialog = ref(false);
const hasErrorAlert = ref(false);
const errorMessages = ref('');

const deactivatedButton = (status) => {
  if (status === 'active') {
    //change status to inactive
    clickedUser.value.status = 'inactive';
    deactivatedDialog.value = true;
  }
};


// edit and delete button function
const onClickDeleteUser = (userSelected) => {
  clickedUser.value = userSelected;
  deleteDialog.value = true;
};

const onClickEditUser = (userSelected) => {
  //use object.assign to prevent table and form data from being linked
  clickedUser.value = Object.assign({},userSelected);
  editDialog.value = true;
};

// called api to delete user
const deleteUser = () => {
  axios.delete(`/api/users/delete/${clickedUser.value.user_id}`)
    .then(() => {
      deleteDialog.value = false;
      isDeleteAlert.value = true;
      props.usersLoad();
    })
    .catch((error) => {
      hasErrorAlert.value = true;
      errorMessages.value = error.response.data.message;
      console.log(error);
    });

};

// called api to edit user
const editUser = () => {
  //move out the data of the user roles, because now the roles is in the array proxy
  clickedUser.value.roles = clickedUser.value.roles.map((role) => role.role_id);

  axios.put(`/api/users/update/${clickedUser.value.user_id}`, {
    username: clickedUser.value.username,
    email: clickedUser.value.email,
    phone_number: clickedUser.value.phone_number,
    status: clickedUser.value.status,
    roles: clickedUser.value.roles,
  })
    .then(() => {
      editDialog.value = false;
      isEditAlert.value = true;
      props.usersLoad();
    })
    .catch((error) => {
      hasErrorAlert.value = true;
      errorMessages.value = error.response.data.message;
      console.log(error);
    });
};

// count roles
const countRoles = (roles) => {
  return roles.length;
};

// customize validator
const emailValidator = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid';
};

const phoneValidator = (v) => /^0[0-9]{9,10}$/.test(v) || 'Phone number must be valid';

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
          <VIcon icon="ri-user-star-line" />
          Roles
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
        <td class="font-weight-medium text-high-emphasis text-center text-truncate" style="display: flex; align-items: center;">
        <VAvatar size="32"
            :color="item.avatar ? '' : 'primary'"
            :class="`${!item.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
            :variant="!item.avatar ? 'tonal' : undefined"
            style="margin-inline-end: 8px;">
            <VImg
            :src="item.avatar"/>
        </VAvatar>
        {{ item.username }}
      </td>

        <td class="text-center">
          {{ item.phone_number }}
        </td>
        <td class="text-center">
          {{ item.email }}
        </td>
        <td class="text-center">
          <VBadge
            :content="countRoles(item.roles)"
            inline
          >
            <VAvatar size="30">
              <VIcon icon="ri-user-star-line" />
              <VMenu
                activator="parent"
                transition="slide-x-transition"
                location="right"
                offset="14px"
              >
              <VCard>
                <VList>
                  <VListItemTitle class="ml-4">
                    Roles
                  </VListItemTitle>
                  <VListItem>
                    <template v-for="role in item.roles" :key="role.role_id">
                      <VChip color="primary" size="small" class="me-2">
                        {{ role.name }}
                      </VChip>
                    </template>
                  </VListItem>
                </VList>
              </VCard>

              </VMenu>
            </VAvatar>
        </VBadge>
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
            @click="onClickEditUser(item)"
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

  <!--Edit User-->
  <VDialog
    v-model="editDialog"
    max-width="600"
  >
    <!-- Dialog Content -->
    <VCard title="Edit User Information">

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
                <label for="firstNameHorizontalIcons">Username</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextField
                  v-model="clickedUser.username"
                  prepend-inner-icon="ri-user-line"
            
                  placeholder="John"
                  :rules="[requiredValidator]"
                />
              </VCol>
            </VRow>
          </VCol>

           <!-- 👉 Status -->
           <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="status">Status</label>
              </VCol>
              <VBtn
                v-if="clickedUser.status === 'active'"
                color="success"
                variant="tonal"
                rounded="pill"
                @click="deactivatedButton(clickedUser.status)"
              >
                Active
                <VIcon
                  icon="ri-checkbox-circle-line"
                  style="margin-inline-start: 5px;"
                />
                <VTooltip
                  activator="parent"
                  open-delay="500"
                  location="end"
                >
                  Click to deactivate account
                </VTooltip>
              </VBtn>
              <VBtn
                v-else
                color="error"
                variant="tonal"
                rounded="pill"
                @click="clickedUser.status = 'active'"
              >
                Inactive
                <VIcon
                  icon="ri-close-circle-line"
                  style="margin-inline-start: 5px;"
                />
                <VTooltip
                  activator="parent"
                  open-delay="500"
                  location="end"
                >
                  Click to reactivate account
                </VTooltip>
              </VBtn>
            </VRow>
          </VCol>

          <!-- 👉 Email -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="emailHorizontalIcons">Email</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextField
                  v-model="clickedUser.email"
                  prepend-inner-icon="ri-mail-line"
            
                  type="email"
                  placeholder="12345@siswa.unimas.my"
                  :rules="[requiredValidator, emailValidator]"
                />
              </VCol>
            </VRow>
          </VCol>

          <!-- 👉 Mobile -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="mobileHorizontalIcons">Mobile</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                  <VTextField
                    v-model="clickedUser.phone_number"
                    prepend-inner-icon="ri-smartphone-line"
             
                    placeholder="0123456789"
                    :rules="[requiredValidator, phoneValidator]"
                  />
              </VCol>
            </VRow>
          </VCol>

          <!-- 👉 Role -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="rolesIcons">Role</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
              <VCombobox
                v-model="clickedUser.roles"
                multiple
                chips
                :items="roles"
                item-title="name"
            
                placeholder="Select role"
                return-object
                label="Roles"
                clearable
              />
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
              type="submit"
              @click="$refs.refForm.validate().then(() => editUser())"
            >
              Edit
            </VBtn>
            <VBtn
              color="secondary"
              type="reset"
              variant="tonal"
            >
              Reset
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
      </VCardText>
    </VCard>
  </VDialog>

  <!--Deactivate Dialog-->
  <VDialog
    v-model="deactivatedDialog"
    persistent
    class="v-dialog-sm"
  >

    <!-- Dialog Content -->
    <VCard title="Account Status">

      <VCardText>
        <VAlert
          color="error"
          icon="ri-alert-line"
          variant="tonal"
        >
          This account have been deactivated.
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        
        <VBtn 
          color="error"
          @click="deactivatedDialog = false"
        >
          I accept
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!--Snackbar-->
  <VSnackbar
      v-model="isDeleteAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      User <strong>{{ clickedUser.username }}</strong> has been successfully deleted.
    </VSnackbar>

    <VSnackbar
      v-model="isEditAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      User <strong>{{ clickedUser.username }}</strong> information has been successfully edited.
    </VSnackbar>

    <VSnackbar
      v-model="hasErrorAlert"
      location="top end"
      transition="scale-transition"
      color="error"
    >
      <VIcon size="20" class="me-2">ri-alert-line</VIcon>
      {{ errorMessages }}
    </VSnackbar>
</template>

