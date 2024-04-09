<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import logo from '@images/logos/LOGO1.png';
import axios from 'axios';
import { useTheme } from 'vuetify';
import { VForm } from 'vuetify/components/VForm';
const vuetifyTheme = useTheme()

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark
})

const route = useRoute()
const router = useRouter()
const email = ref('')
const errorMessages = ref('')
const hasErrorAlert = ref(false)
const isSuccessAlert = ref(false)
const startLoading = ref(false)

// customize validator
const emailValidator = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid (12345@siswa.unimas.my)';
};

const sendResetInstructions = async () => {
  startLoading.value = true;
  // Validate the email format
  if (!emailValidator(email.value)) {
    errorMessages.value = 'Email format is invalid (12345@siswa.unimas.my)';
    hasErrorAlert.value = true;
    return;
  }

  try {
    // Send the reset instructions if the email is valid
    await axios.post('/api/auth/forgot-password', { email: email.value });
    // Clear the email value after sending the reset instructions
    startLoading.value = false;
    email.value = null;
    isSuccessAlert.value = true;
  } catch (error) {
    // Handle any errors that occur during the API request
    hasErrorAlert.value = true;
    errorMessages.value = error.response.data.message;
    console.error('Error:', error);
  }
};

const goToLoginPage = () => {
  router.push({ path: '/login', query: { forceReload: true } }).catch(() => {});
};


</script>

<template>
  <div class="auth-wrapper">
    <div class="auth-content">
    <VCard
      :loading="startLoading"
      class="auth-card pa-4 pt-7"
      max-width="448"
    >
      <VCardItem class="justify-center">
        <template #prepend>
          <div class="d-flex">
            <img
              :src="logo"
              alt="Materio"
              class="mr-2"
              height="150"
              width="100"
            />
            
          </div>
        </template>

       
      </VCardItem>

      <VCardText class="pt-2">
        <h5 class="text-h5 font-weight-semibold mb-1">
          <VIcon size="20" class="mr-2" icon="ri-lock-password-line" />Forgot Password?
        </h5>
        <p class="mb-0">
          Enter your account email and we will send you instructions to reset your password.
        </p>

      </VCardText>

      <VCardText>
        <VForm 
        ref="refForm"
       >
          <VRow>
            <!-- email -->
            <VCol cols="12">
              <VTextField
                v-model="email"
                label="Email"
                type="email"
                :rules="[requiredValidator, emailValidator]"
              />
            </VCol>

            <!-- password -->
            <VCol cols="12">

              <VAlert
                v-if="hasErrorAlert"
                type="error"
                variant="tonal"
                closable
                class="mb-2"
              >
             
                {{ errorMessages }}
              </VAlert>

              <!-- forgot password -->
              <VBtn
                block
                type="button"
                @click="$refs.refForm.validate().then(() => sendResetInstructions())"
                
              >
                Send Reset Instructions
              </VBtn>
            
            </VCol>

            <!-- create account -->
            <VCol
              cols="12"
              class="text-center text-base"
            > 
            <RouterLink
              class="text-primary ms-2"
              to="/login"
              @click="goToLoginPage()"
            >
              <VIcon class="mb-1" icon="ri-arrow-left-double-line" />
              Go to Login
            </RouterLink>

            </VCol>
            
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
    </div>
  </div>

  <!--Snackbar-->
  <VSnackbar
      v-model="isSuccessAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
        Password has been reset successfully. Please check your email.
    </VSnackbar>
</template>


<style scoped>
.auth-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  background: url('/resources/images/background/background3.jpg') no-repeat center center;
  background-size: cover;
  block-size: 100vh;
}

.auth-content {
  position: absolute;
  z-index: 1;
  border-radius: 8px;
  background: rgba(255, 255, 255, 90%);
  box-shadow: 10px 10px 10px rgba(0, 0, 0, 20%);
  inline-size: 100%;
  max-inline-size: 448px;
}

.auth-card {
  padding: 24px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 90%);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 10%);
}
</style>
