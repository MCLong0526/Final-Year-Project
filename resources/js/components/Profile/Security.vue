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

        <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="passwordHorizontalIcons">Current Password</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                  <VTextField
                    v-model="currentPassword"
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
                <label for="passwordHorizontalIcons">New Password</label>
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
                <label for="passwordHorizontalIcons">Confirm New Password</label>
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
    <!--Snackbar-->
    <VSnackbar
      v-model="hasErrorAlert"
      location="top end"
      transition="scale-transition"
      color="error"
    >
    <VIcon size="30" class="me-2">ri-error-warning-line</VIcon>
There was an error changing your password.<br>
<span style="padding-inline-start: 50px;">Your current password does not match.</span>
</VSnackbar>

</template>

<script setup>
import { confirmedValidator, passwordValidator, requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';
import { ref } from 'vue';
import { VForm } from 'vuetify/components/VForm';

const refForm = ref() 
const password = ref('')
const currentPassword = ref('')
const confirmPassword = ref('')
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const editPasswordAlert = ref(false)
const passwordMatch = ref(false)
const hasErrorAlert = ref(false)

const editPassword = () => {
  checkPassword();
  
  if (passwordMatch.value=='false') {
    password.value = '';
    currentPassword.value = '';
    confirmPassword.value = '';
    
   refForm.value.resetValidation();
    hasErrorAlert.value=true;
    
    
    return;
  }


    axios.put('/api/users/update-password', {
    password: password.value,
    password_confirmation: confirmPassword.value

  })
  .then(response => {
    password.value = '';
    currentPassword.value = '';
    confirmPassword.value = '';

    refForm.value.resetValidation();
    editPasswordAlert.value=true;


  })
  
}

const checkPassword = () => {
    axios.post('/api/users/check-password', {
        current_password: currentPassword.value,
    }).then(response => {
        passwordMatch.value = response.data.password_match;
        console.log(response.data.password_match);
        
    }).catch(error => {
      
        console.error(error);
    });
};

</script>
