<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import TableUser from '@/components/User/UserTable.vue';
import axios from 'axios';
import { debounce } from 'lodash';
import { ref } from 'vue';
import { VForm } from 'vuetify/components/VForm';


const refForm = ref()
const users = ref([]);
const roles = ref([]);
//create user
const registerDialog = ref(false)
const username = ref('')
const phone_number = ref('')
const email = ref('')
const password = ref('')
const isAddAlert = ref(false)
const loading = ref(false)
const usernameForAlert = ref('')
const rowPerPage = ref(5)
const currentPage = ref(1)
const totalPages = ref(0)
const searchValue = ref('')
const roleSearch = ref([])
const statusSearch = ref([])
const errorMessages = ref('')
const hasErrorAlert = ref(false)
const statusValues = [
  { name: 'Active', value: 'active' },
  { name: 'Inactive', value: 'inactive' },
]

const usersLoad = debounce(() =>{
 
  //get the user using the rowPerPage, don direct get all users
  let requestURL='/api/users?per_page='+rowPerPage.value+'&page='+currentPage.value;
  if(searchValue.value && searchValue.value.length > 2){
    requestURL += '&search='+searchValue.value;
  }
  if(roleSearch.value && roleSearch.value.length > 0){
    requestURL += '&role='+roleSearch.value.map((role) => role.role_id).join(',');
  }
  if(statusSearch.value.value){
    //only get the value of the statusSearch and it is an array
    requestURL += '&status='+statusSearch.value.value;
  }
  axios.get(requestURL).then(({data})=>{
    totalPages.value = Math.ceil(data.data.total / rowPerPage.value);
    users.value = data.data.data;
    
    users.value.forEach((user) => {
      user.avatar = 'http://127.0.0.1:8000/storage/' + user.avatar;
    });
    
    //console.log(users.value);
  }).catch((error)=>{
    console.log(error);
  })
}, 800)


const rolesLoad = () =>{
  axios.get('/api/roles').then(({data})=>{
    roles.value = data.data;
    //console.log(roles.value);
  }).catch((error)=>{
    console.log(error);
  })
}

// generate random password for user
const generatePassword = () => {
  const uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  const lowercase = 'abcdefghijklmnopqrstuvwxyz';
  const specialChars = '!@#$%^&*()-_=+[{]}|;:,<.>/?';
  const numbers = '0123456789';

  // Initialize the password with one character from each character type
  let password = '';
  password += uppercase[Math.floor(Math.random() * uppercase.length)];
  password += lowercase[Math.floor(Math.random() * lowercase.length)];
  password += specialChars[Math.floor(Math.random() * specialChars.length)];
  password += numbers[Math.floor(Math.random() * numbers.length)];

  // Fill the remaining characters randomly
  const allChars = uppercase + lowercase + specialChars + numbers;
  for (let i = 0; i < 4; i++) {
    const char = allChars[Math.floor(Math.random() * allChars.length)];
    password += char;
  }

  // Shuffle the password to randomize the order of characters
  password = password.split('').sort(() => Math.random() - 0.5).join('');

  return password;
};

// createUser function
const createUser = () => {
  loading.value = true;
  usernameForAlert.value = username.value;
  password.value = generatePassword();
  
  axios.post('/api/users/store', {
    username: username.value,
    phone_number: phone_number.value,
    email: email.value,
    password: password.value,
    status: 'active',
  }).then(() => {
    registerDialog.value = false;
    isAddAlert.value = true;
    username.value = '';
    phone_number.value = '';
    email.value = '';
    usernameForAlert.value = '';
    loading.value = false;
    usersLoad();
  }).catch((error) => {
    loading.value = false;
    hasErrorAlert.value = true;
    errorMessages.value = error.response.data.message;
    console.log(error);
  });
};

// watch the changes of the rowPerPage and currentPage
watch([rowPerPage, currentPage], ([newRowPerPage, newCurrentPage]) => {
  usersLoad()
})

// watch the changes of the searchValue
watch(searchValue, (newSearchValue) => {
  usersLoad()
})

// watch the changes of the roleSearch
watch(roleSearch, (newRoleSearch) => {
  usersLoad()
})

// watch the changes of the statusSearch
watch(statusSearch, (newStatusSearch) => {
  usersLoad()
})

// customize validator
const emailValidator = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid';
};

const phoneValidator = (v) => /^0[0-9]{9,10}$/.test(v) || 'Phone number must be valid';

usersLoad()
rolesLoad()


</script>

<template>

<div class="box-style">
  <h2 class="mt-3 ml-3 text-overline-4" style="font-weight: 400;">Filter User</h2>

<VRow class="mt-2 mb-2 ">
    <VCol cols="12" md="4" class="ml-2">
      <VCombobox
        v-model="roleSearch"
        multiple
        chips
        :items="roles"
        prepend-inner-icon="ri-user-star-line"
        item-title="name"
        placeholder="Select role"
        return-object
        label="Roles"
        clearable
      />
    </VCol>
    <VCol cols="12" md="4">
      <VCombobox
        v-model="statusSearch"
        :items="statusValues"
        prepend-inner-icon="ri-shield-check-line"
        item-title="name"
        placeholder="Select Status"
        return-object
        label="Status"
        clearable
        @click:clear="statusSearch = []"
  
      />
    </VCol>
  </VRow>
</div>


  <div class="box-style">
  <VRow>
    <VCol cols="12" md="3">
      <div class="mt-5 ml-5 mb-4">
        <VBtn @click="registerDialog=true">
          Register User
          <v-icon
            end
            icon="ri-registered-line"
          />
        </VBtn>
      </div>
    </VCol>
    <VCol cols="12" md="3"/>
    <VCol cols="12" md="3"/>
    <VCol cols="12" md="3">
      <VTextField
      class="mt-5 mr-5 mb-4"
        v-model="searchValue"
        placeholder="Search"
        label="Search username/email/phone"
        clearable
        dense
      />
    </VCol>
  </VRow>


<div class="table-style">
  <TableUser
    :users="users"
    :usersLoad="usersLoad"
    :roles="roles"
  />
</div>

  <VCardText>
    
    <VRow>
      <VCol cols="12" md="2">
        <VCombobox
          v-model="rowPerPage"
          :items="[5, 10, 15, 20]"
          label="Rows per page"
          dense
        />
      </VCol>
      <VCol cols="12" md="6"/>
        
      <VCol cols="12" md="4">
        <VPagination
          v-model="currentPage"
          variant="outlined"
          :length="totalPages"
          rounded="circle"
        />
      </VCol>
    </VRow>
    
  </VCardText>
  </div>


<VDialog
    v-model="registerDialog"
    max-width="600"
  >
  
    <!-- Dialog Content -->
    <VCard :disabled="loading" title="Register New User">
      <div v-if="loading" class="absolute inset-0 flex items-center justify-center bg-white bg-opacity-80 z-50" style=" margin-block-start: 180px;margin-inline-start: 280px">
      <VProgressCircular  :size="50" color="success" indeterminate  />
    </div>
      
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
                  v-model="username"
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
              <VChip
                color="success"
                prepend-icon="ri-checkbox-circle-line"
                style="cursor: not-allowed;"
              >
                Active
              </VChip>
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
                  v-model="email"
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
                    v-model="phone_number"
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
              @click="$refs.refForm.validate().then(() => createUser())"
            >
              Submit
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
      v-model="isAddAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      User <strong>{{ usernameForAlert }}</strong> has been successfully registered.
    </VSnackbar>

    <!--Snackbar-->
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

<style scoped>
.table-style{
  padding: 0.5px; /* Padding around the table */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
}

.box-style {
  padding: 1.5px; /* Padding around the table */
  border: 0.4px solid #282828;
  border-radius:10px;
  background-color: #fff; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
  margin-block-end: 15px
}

.relative {
  position: relative;
}

.absolute {
  position: absolute;
}
</style>
