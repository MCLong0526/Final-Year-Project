<script setup>
import avatar1 from '/resources/images/avatars/avatar-16.jpg';
import avatar2 from '/resources/images/avatars/avatar-17.jpg';
import avatar3 from '/resources/images/avatars/avatar-18.jpg';
import avatar4 from '/resources/images/avatars/avatar-19.jpg';
import avatar5 from '/resources/images/avatars/avatar-20.jpg';
import avatar6 from '/resources/images/avatars/avatar-21.jpg';
import avatar7 from '/resources/images/avatars/avatar-22.jpg';
import avatar8 from '/resources/images/avatars/avatar-23.jpg';
import avatar9 from '/resources/images/avatars/avatar-24.jpg';
import avatar10 from '/resources/images/avatars/avatar-25.jpg';
import avatar11 from '/resources/images/avatars/avatar-26.jpg';
import avatar12 from '/resources/images/avatars/avatar-27.jpg';
import avatar13 from '/resources/images/avatars/avatar-28.jpg';



import { requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';

const user = ref({});
const items = ref([]); 
const authUser = ref({});
const editDialog = ref(false);
const isEditAlert = ref(false);
const isProfileUpload = ref(false);
const isProfileUpload2 = ref(false);
const isProfileAlert = ref(false);
const hasErrorAlert = ref(false);
const errorMessages = ref('');


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

    // Add avatar URL if it exists
    if (user.value.avatar) {
      let avatarPath = user.value.avatar.startsWith('http') ? user.value.avatar : `/storage/${user.value.avatar}`;
      user.value.avatarUrl = avatarPath;
  
    }
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
    case 'Admin':
      return 'error';
    case 'Buyer':
      return 'success';
    case 'Seller':
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

  })
    .then((response) => {
      editDialog.value = false;
      isEditAlert.value = true;
      getUser();
    })
    .catch((error) => {
      hasErrorAlert.value = true;
      errorMessages.value = error.response.data.message;
      console.error(error);
    });
};

const refInputEl = ref(null)

const changeAvatar = file => {
  const fileReader = new FileReader()
  const { files } = file.target
  if (files && files.length) {
    fileReader.readAsDataURL(files[0])
    fileReader.onload = () => {
      if (typeof fileReader.result === 'string') {
        user.value.avatar = fileReader.result
        // PASS THE FILE TO THE FUNCTION
        
        saveAvatar(fileReader.result)
      }
    }
  }
}

// save avatar to database
const saveAvatar = (avatar) => {
  axios.put(`/api/users/update-avatar/${user.value.user_id}`, {
    avatar: avatar,
  })
    .then((response) => {
      isProfileUpload.value = false;
      isProfileUpload2.value = false;
      isProfileAlert.value = true;
      getUser();
      // wait 2 second then refresh the page
      setTimeout(() => {
        window.location.reload();
      }, 2000);
      
    })
    .catch((error) => {
      hasErrorAlert.value = true;
      errorMessages.value = error.response.data.message;
      console.error(error);
    });
};

// for change avatar
const avatars = [
  {
    avatar: avatar1,
    name: 'Avatar 1',
    value: 'images/avatars/avatar-16.jpg'
    
  },
  {
    avatar: avatar2,
    name: 'Avatar 2',
    value: 'images/avatars/avatar-17.jpg'

  },
  {
    avatar: avatar3,
    name: 'Avatar 3',
    value: 'images/avatars/avatar-18.jpg'
  },
  {
    avatar: avatar4,
    name: 'Avatar 4',
    value: 'images/avatars/avatar-19.jpg'
  },
  {
    avatar: avatar5,
    name: 'Avatar 5',
    value: 'images/avatars/avatar-20.jpg'
  },
  {
    avatar: avatar6,
    name: 'Avatar 6',
    value: 'images/avatars/avatar-21.jpg'
  },
  {
    avatar: avatar7,
    name: 'Avatar 7',
    value: 'images/avatars/avatar-22.jpg'
  },
  {
    avatar: avatar8,
    name: 'Avatar 8',
    value: 'images/avatars/avatar-23.jpg'
  },
  {
    avatar: avatar9,
    name: 'Avatar 9',
    value: 'images/avatars/avatar-24.jpg'
  },
  {
    avatar: avatar10,
    name: 'Avatar 10',
    value: 'images/avatars/avatar-25.jpg'
  },
  {
    avatar: avatar11,
    name: 'Avatar 11',
    value: 'images/avatars/avatar-26.jpg'
  },
  {
    avatar: avatar12,
    name: 'Avatar 12',
    value: 'images/avatars/avatar-27.jpg'
  },
  {
    avatar: avatar13,
    name: 'Avatar 13',
    value: 'images/avatars/avatar-28.jpg'
  },
]




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
            @click="isProfileUpload=true"
          >
          <VAvatar size="100" class="user-profile-avatar mx-auto">
          <VImg
            :src="user.avatarUrl"
          />

          </VAvatar>  
            <input
              ref="refInputEl"
              type="file"
              name="file"
              accept=".jpeg,.png,.jpg,GIF"
              hidden
              @input="changeAvatar"
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
          <VListItemTitle class="text-h6" style="display: inline-block; inline-size: 120px;"><strong>{{ item.title }}</strong></VListItemTitle>
          <VListItemSubtitle class="text-h6" style="display: inline-block;">: {{ item.value }}</VListItemSubtitle>
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


  

  <!-- Dialog -->
  <VDialog
    v-model="isProfileUpload"
    class="v-dialog-sm"
  >
    <VCard title="Upload Profile Picture">

      <VCardText>
        <VAlert
          border="start"
          color="secondary"
          variant="tonal"
        >
        You can upload your own profile picture or use system avatars.
        </VAlert>
        
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="info"
          @click="refInputEl?.click()"
        >
          Upload Avatar
        </VBtn>
        <VBtn @click="isProfileUpload2 = !isProfileUpload2">
          Use System Avatar
        </VBtn>
        
        
      </VCardActions>
    </VCard>
  </VDialog>

  <!-- Dialog 2 -->
  <VDialog
    v-model="isProfileUpload2"
    scrollable
    max-width="300"
  >
    <VCard title="Choose Avatar">
  

      <VList
        lines="two"
        border
        rounded
        style="block-size: 300px;"
      >
        <template
          v-for="(avatar, index) of avatars"
          :key="avatar.name"
        >
          <VListItem >
            <template #prepend>
              <VAvatar :image="avatar.avatar" />
            </template>
            <VListItemTitle>
              {{ avatar.name }}
            </VListItemTitle>
            <template #append>
              <VBtn
                color="primary"
                @click="saveAvatar(avatar.value)"
              >
                Use
              </VBtn></template> <VSpacer />

          </VListItem>
          <VDivider v-if="index !== avatars.length - 1" />
        </template>
      </VList>
      <VCardActions>
        <VSpacer />
        <VBtn 
          style="margin-block-start: 5px;"
          @click="isProfileUpload2 = false">
          Back
        </VBtn>
      </VCardActions>
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
        User information has been successfully edited.
      
    </VSnackbar>
    <VSnackbar
      v-model="isProfileAlert"
      location="top end"
      transition="scale-transition"
      color="success"
      
    >
      <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
        Profile picture has been successfully edited.
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


  </div>
</template>
