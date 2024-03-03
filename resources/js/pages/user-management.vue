<script setup>
import { confirmedValidator, passwordValidator, requiredValidator } from '@/@core/utils/validators';
import TableUser from '@/components/User/UserTable.vue';
import axios from 'axios';
import { ref } from 'vue';
import { VForm } from 'vuetify/components/VForm';
const refForm = ref()

const users = ref([]);

const usersLoad = () =>{
  axios.get('/api/users').then(({data})=>{
    users.value = data.data;
    //console.log(users.value);
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

const createUser = () =>{
  axios.post('/api/users/store', {
    username: username.value,
    phone_number: phone_number.value,
    email: email.value,
    password: password.value
  }).then(()=>{
    registerDialog.value = false;
    username.value = '';
    phone_number.value = '';
    email.value = '';
    password.value = '';
    confirmPassword.value = '';
    usersLoad();
  }).catch((error)=>{
    console.log(error);
  })
}

//const requiredValidator = (v) => !!v || 'Field is required'

const emailValidator = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid';
};

const phoneValidator = (v) => /^0[0-9]{9,10}$/.test(v) || 'Phone number must be valid';

usersLoad()
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
  />
</div>

<VDialog
    v-model="registerDialog"
    max-width="600"
  >

    <!-- Dialog Content -->
    <VCard title="User Profile">

      <VCardText>
        <VForm 
        ref="refForm" 
        @submit.prevent>
    <VRow>
      <VCol cols="12">
        <VTextField
          v-model="username"
          prepend-inner-icon="ri-user-line"
          label="Username"
          placeholder="John"
          :rules="[requiredValidator]"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          v-model="email"
          prepend-inner-icon="ri-mail-line"
          label="Email"
          type="email"
          placeholder="12345@siswa.unimas.my"
          :rules="[requiredValidator, emailValidator]"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          v-model="phone_number"
          prepend-inner-icon="ri-smartphone-line"
          label="Mobile"
          placeholder="0123456789"
          :rules="[requiredValidator, phoneValidator]"
        />
      </VCol>

      <VCol cols="12">
        <VTextField
          v-model="password"
          prepend-inner-icon="ri-lock-line"
          label="Password"
          autocomplete="on"
          :type="isPasswordVisible ? 'text' : 'password'"
          :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
          placeholder="············"
          :rules="[requiredValidator, passwordValidator]"
          hint="Your password must be 8-20 characters long."
          @click:append-inner="isPasswordVisible = !isPasswordVisible"
        />
      </VCol>
      <VCol cols="12">
        <VTextField
          v-model="confirmPassword"
          prepend-inner-icon="ri-lock-fill"
          label="Confirm Password"
          :type="isConfirmPasswordVisible ? 'text' : 'password'"
          placeholder="Confirm Password"
          :append-inner-icon="confirmPassword ? 'ri-eye-off-line' : 'ri-eye-line'"
          :rules="[requiredValidator, passwordValidator, confirmedValidator(confirmPassword, password)]"
          autocomplete="on"
          @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
        />
      </VCol>

      <VCol cols="12" sm="4">
        <VBtn
          color="error"
          @click="registerDialog=false"
          
        >
         Close
        </VBtn>
      </VCol>
    
        <VCol cols="12" sm="4">
        <VBtn
          color="secondary"
          type="reset"
          variant="tonal"
          style="margin-inline-start: 44px;"
        >
          Reset
        </VBtn>
      </VCol>
        <VCol cols="12" sm="4">
          <VBtn
          type="submit"
          class="me-4"
          @click="$refs.refForm.validate().then(() => createUser())"
          style="margin-inline-start: 85px;"
        >
          Submit
        </VBtn>
        
      </VCol>
    </VRow>
  </VForm>
      </VCardText>
    </VCard>
  </VDialog>

</template>

<style scoped>
.table-style{
  padding: 0.5px; /* Padding around the table */
  background-color: #848383; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
}
</style>
```


