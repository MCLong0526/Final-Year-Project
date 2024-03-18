<script setup>
const user = ref({});
import { requiredValidator } from '@/@core/utils/validators';

let csrfToken = '';

const rules = [fileList => !fileList || !fileList.length || fileList[0].size < 5000000 || 'Avatar size should be less than 5 MB!']

import axios from 'axios';

const items = ref([]);
const authUser = ref({});
const editDialog = ref(false);
const isEditAlert = ref(false);
const profileButton = ref(false);

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
    user.value.created_at = new Date(user.value.created_at).toDateString();
    user.value.status = user.value.status === 'active' ? 'Active' : 'Inactive';


    items.value = [
      {
        title: 'User ID',
        value: user.value.user_id,
      },
      {
        title: 'Username',
        value: user.value.username,
      },
      {
        title: 'Email',
        value: user.value.email,
      },
      {
        title: 'Phone',
        value: user.value.phone_number,
      },
      {
        title: 'Status',
        value: user.value.status,
      },
      {
        title: 'Register At',
        value: user.value.created_at,
      },
   
    ];
  } catch (error) {

    console.error(error);
  }
};

// change the role color
const getRoleColor = (role) => {
  switch (role) {
    case 'admin':
      return 'error';
    case 'buyer':
      return 'success';
    case 'seller':
      return 'warning';
    default:
      return 'info';
  }
};

// open the edit dialog
const editDialogOpen = () => {
  authUser.value = Object.assign({}, user.value);
  editDialog.value = true;
  console.log(user.value.user_id);
};

// edit user profile function
const editUserProfile = () => {
  axios.put(`/api/users/update-profile/${user.value.user_id}`, {
    username: authUser.value.username,
    email: authUser.value.email,
    phone_number: authUser.value.phone_number,
    avatar: authUser.value.avatar,
  })
    .then((response) => {
      editDialog.value = false;
      isEditAlert.value = true;
      getUser();
    })
    .catch((error) => {
      console.error(error);
    });
};

// customize validator
const emailValidator = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid';
};

const phoneValidator = (v) => /^0[0-9]{9,10}$/.test(v) || 'Phone number must be valid';

getUser();


</script>

<template>
  <div>
    <VCard>
    
      <VCardText class="d-flex flex-column align-items-center gap-y-4">
        <div class="text-center">
          
          <VBadge
            location="bottom end"
            color="secondary"
            icon="ri-pencil-line"
            
          >
            <VAvatar
              rounded
              size="100"
              :image="user.avatar"
              class="user-profile-avatar mx-auto"
            />
            
          </VBadge>
          
        </div>
        
        <div class="user-profile-info text-center">
          <h5 class="text-h5">{{ user.username ?? 'Unknown' }}</h5>
          <VSpacer />
          <VChip
            v-for="role in user.roles"
            :key="role.role_id"
            class="mx-1 mt-3"
            :color="getRoleColor(role.name)"
            variant="outlined"

          >
            {{ role.name }}
          </VChip>
        </div>
      </VCardText>
    
      <div style="margin-block: 0;margin-inline: 30px;">
        <h5 class="text-h5 text-center text-sm-start mt-2 ml-5" >
          <VIcon class="me-2" size="20">ri-information-line</VIcon>
          About
        </h5>

        <VDivider class="my-2" />
     
        <VListItem v-for="item in items" :key="item.title">
          <VListItemTitle style="display: inline-block; inline-size: 120px;"><strong>{{ item.title }}</strong></VListItemTitle>
          <VListItemSubtitle style="display: inline-block;">: {{ item.value }}</VListItemSubtitle>
        </VListItem>

        

        <div class="text-center mb-4">
          <VBtn class="mt-4" color="primary" @click="editDialogOpen()">
            Edit Profile
          </VBtn>
        </div>
        

      </div>
    </VCard>

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
                  v-model="authUser.username"
                  prepend-inner-icon="ri-user-line"
            
                  placeholder="John"
                  :rules="[requiredValidator]"
                />
              </VCol>
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
                  v-model="authUser.email"
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
                    v-model="authUser.phone_number"
                    prepend-inner-icon="ri-smartphone-line"
             
                    placeholder="0123456789"
                    :rules="[requiredValidator, phoneValidator]"
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
              @click="$refs.refForm.validate().then(() => editUserProfile())"
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

  <!--Snackbar-->
  <VSnackbar
      v-model="isEditAlert"
      location="top end"
      transition="scale-transition"
      color="success"
      
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      User <strong>{{ authUser.username }}</strong> has been successfully edited.
      
    </VSnackbar>

  </div>
</template>
