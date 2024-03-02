<script setup>
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
    usersLoad();
  }).catch((error)=>{
    console.log(error);
  })
}

const requiredValidator = (v) => !!v || 'Field is required'

const emailValidator = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid';
};

const phoneValidator = (v) => /^0[0-9]{9,10}$/.test(v) || 'Phone number must be valid'

const confirmedValidator = (confirmPassword, password) => (v) => {
  return v === password || 'Passwords do not match';
};

const passwordValidator = (v) => {
  // Check if the password is between 8 and 20 characters long
  if (v.length < 8 || v.length > 20) {
    return 'Password must be between 8 and 20 characters long';
  }

  // Check if the password contains at least one uppercase letter, one lowercase letter, and one digit
  if (!/[A-Z]/.test(v) || !/[a-z]/.test(v) || !/[0-9]/.test(v)) {
    return 'Password must contain at least one uppercase letter, one lowercase letter, and one digit';
  }

  // Check if the password contains any special characters
  if (!/[^A-Za-z0-9]/.test(v)) {
    return 'Password must contain at least one special character';
  }

  return true; // Password is valid
};


usersLoad()
</script>

<template>
  
  <div class="mb-5">
  <VBtn @click="registerDialog=true">
      Register User
      <VIcon
        end
        icon="ri-checkbox-circle-line"
      />
    </VBtn>
</div>
<div class="table-style">
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
          </VChip>
          <VChip
            color="error"
            v-else
            >Inactive
          </VChip>
        </td>
      </tr>
    </tbody>
  </VTable>
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

      <VCol cols="12" sm="3">
        <VBtn
          type="submit"
          class="me-4"
          @click="$refs.refForm.validate().then(() => createUser())"
        >
          Submit
        </VBtn>
      </VCol>
        <VCol cols="12" sm="3">
        <VBtn
          color="secondary"
          type="reset"
          variant="tonal"
        >
          Reset
        </VBtn>
        </VCol>
        <VCol cols="12" sm="3"/>
        <VCol cols="12" sm="3">
        <VBtn
          color="error"
          @click="registerDialog=false"
          style="margin-inline-start: 45px;"
        >
         Close
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


