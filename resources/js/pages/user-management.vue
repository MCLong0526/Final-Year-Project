<script setup>
import { confirmedValidator, passwordValidator, requiredValidator } from '@/@core/utils/validators';
import TableUser from '@/components/User/UserTable.vue';
import axios from 'axios';
import { ref } from 'vue';
import { VForm } from 'vuetify/components/VForm';
const refForm = ref()

const users = ref([]);
const roles = ref([]);

const usersLoad = () =>{
  axios.get('/api/users').then(({data})=>{
    users.value = data.data;
    //console.log(users.value);
  }).catch((error)=>{
    console.log(error);
  })
}

const rolesLoad = () =>{
  axios.get('/api/roles').then(({data})=>{
    roles.value = data.data;
    //console.log(roles.value);
  }).catch((error)=>{
    console.log(error);
  })
}

//create user
const registerDialog = ref(false)
const username = ref('')
const phone_number = ref('')
const email = ref('')
const password = ref('')
const isPasswordVisible = ref(false)
const confirmPassword = ref('')
const isConfirmPasswordVisible = ref(false)
const isAddAlert = ref(false)
const usernameForAlert = ref('')

const createUser = () =>{
  usernameForAlert.value = username.value
  axios.post('/api/users/store', {
    username: username.value,
    phone_number: phone_number.value,
    email: email.value,
    password: password.value,
    
  }).then(()=>{
    registerDialog.value = false;
    isAddAlert.value = true;
    username.value = '';
    phone_number.value = '';
    email.value = '';
    password.value = '';
    confirmPassword.value = '';
    usernameForAlert.value = '';
    usersLoad();
  }).catch((error)=>{
    console.log(error);
  })
}

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
  
  <div class="mb-5">
  <VBtn @click="registerDialog=true">
      Register User
      <VIcon
        end
        icon="ri-registered-line"
      />
    </VBtn>
</div>
<div class="table-style">
  <TableUser
    :users="users"
    :usersLoad="usersLoad"
    :roles="roles"
  />
</div>

<VDialog
    v-model="registerDialog"
    max-width="600"
  >
    <!-- Dialog Content -->
    <VCard title="Register New User">

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

         

          <!-- 👉 Password -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="passwordHorizontalIcons">Password</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                  <VTextField
                    v-model="password"
                    prepend-inner-icon="ri-lock-line"

                    autocomplete="on"
                    :type="isPasswordVisible ? 'text' : 'password'"
                    :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                    placeholder="············"
                    :rules="[requiredValidator, passwordValidator]"
                    hint="Your password must be 8-20 characters long."
                    @click:append-inner="isPasswordVisible = !isPasswordVisible"
                  />
              </VCol>
            </VRow>
          </VCol>

          <!-- 👉 Password -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="passwordHorizontalIcons">Confirm Password</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextField
                  v-model="confirmPassword"
                  prepend-inner-icon="ri-lock-fill"
         
                  :type="isConfirmPasswordVisible ? 'text' : 'password'"
                  placeholder="············"
                  :append-inner-icon="confirmPassword ? 'ri-eye-off-line' : 'ri-eye-line'"
                  :rules="[requiredValidator, passwordValidator, confirmedValidator(confirmPassword, password)]"
                  autocomplete="on"
                  @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
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
      variant="flat"
      color="success"
    >
      User <strong>{{ usernameForAlert }}</strong> has been successfully registered.
    </VSnackbar>

</template>

<style scoped>
.table-style{
  padding: 0.5px; /* Padding around the table */
  background-color: #848383; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
}
</style>
```


