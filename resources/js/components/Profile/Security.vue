<template>
  <VCard title="Change Password">

    <VAlert
      color="error"
      icon="ri-alert-line"
      variant="tonal"
      closable
      style=" margin-inline: 20px 20px;"
    >
      Please remember your new password.
    </VAlert>

    <VCardText>
      <VForm 
      ref="refForm" 
      @submit.prevent>
      <VRow>
        <VCol
      cols="12"
      md="6"
    >
      <VTextField
        v-model="password"
        label="Password"
        :type="isPasswordVisible ? 'text' : 'password'"
        :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
        placeholder="Enter Password"
        :rules="[requiredValidator, passwordValidator]"
        autocomplete="on"
        @click:append-inner="isPasswordVisible = !isPasswordVisible"
      />
    </VCol>

    <VCol
      cols="12"
      md="6"
    >
      <VTextField
        v-model="confirmPassword"
        label="Confirm Password"
        :type="isConfirmPasswordVisible ? 'text' : 'password'"
        placeholder="Confirm Password"
        :append-inner-icon="confirmPassword ? 'ri-eye-off-line' : 'ri-eye-line'"
        :rules="[requiredValidator, confirmedValidator(confirmPassword, password)]"
        autocomplete="on"
        @click:append-inner="isConfirmPasswordVisible = !isConfirmPasswordVisible"
      />
    </VCol>

        
        <VCol

         style=" display: flex;align-items: center; justify-content: center;"
          cols="12"
          md="12"
        >
        <VBtn
            type="submit"
            color="primary"
            @click="editPassword"
          >
            Change Password
          </VBtn>
          
        </VCol>
      </VRow>
    </VForm>
    </VCardText>
    </VCard>


    <!--Snackbar-->
    <VSnackbar
      v-model="editPasswordAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      Your password has been successfully changed.
    </VSnackbar>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { VForm } from 'vuetify/components/VForm'
import { confirmedValidator, passwordValidator, requiredValidator } from '@/@core/utils/validators';

const refForm = ref()
const password = ref('')
const confirmPassword = ref('')
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const editPasswordAlert = ref(false)

const editPassword = () => {
  axios.put('/api/users/update-password', {
    password: password.value,
    password_confirmation: confirmPassword.value

  })
  .then(response => {
    password.value = '';
    confirmPassword.value = '';
    refForm.value.resetValidation();
    editPasswordAlert.value=true;



  })
}

</script>
