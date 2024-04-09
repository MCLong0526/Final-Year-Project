
<script setup>
import { confirmedValidator, passwordValidator, requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { VForm } from 'vuetify/components/VForm';

const router = useRouter();
const refForm = ref() 
const password = ref('')
const currentPassword = ref('')
const confirmPassword = ref('')
const isPasswordVisible = ref(false)
const isConfirmPasswordVisible = ref(false)
const editPasswordAlert = ref(false)
const passwordMatch = ref(false)
const hasErrorAlert = ref(false)

const passwordRequirements = [
  'Minimum 8 characters long',
  'At least one lowercase, uppercase letter, digit, and special character',
  'Special characters allowed: !@#$%^&*()-_=+[{]}|;:,<.>/?',
];

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
    setTimeout(() => {
      router.push({ path: '/login', query: { forceReload: true } }).catch(() => {});
    }, 3000);
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
     <VCardText class="mb-2">
        <p class="text-base">
          <VIcon
            size="20"
            icon="ri-information-line"
            class="me-3"
          />
          <strong>Password Requirements:</strong>
        </p>

        <ul class="d-flex flex-column gap-y-3">
          <li
            v-for="item in passwordRequirements"
            :key="item"
            class="d-flex"
          >
            <div>
              <VIcon
                size="7"
                icon="ri-circle-fill"
                class="me-3"
              />
            </div>
            <span class="text-base">{{ item }}</span>
          </li>
        </ul>
      </VCardText>

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
      Your password has been successfully changed. Will be redirected to the login page in a moment.
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
