<script setup>
import { passwordValidator, requiredValidator } from '@/@core/utils/validators';
import { useAuthStore } from '@/plugins/store/AuthStore';
import logo from '@images/logos/logo2.png';
import { ref } from 'vue';
import { useTheme } from 'vuetify';
import { VForm } from 'vuetify/components/VForm';


const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const route = useRoute()
const router = useRouter()
const errorMessages = ref('')
const hasErrorAlert = ref(false)

const vuetifyTheme = useTheme()

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark
})

const login = async () => {
 await authStore.login(email.value, password.value)

 if (authStore.isLoggedIn==true) {
    await authStore.getCurrentLoggedUser()
    router.replace(route.query.to ? String(route.query.to) : '/dashboard')

  }
  else {
    errorMessages.value = authStore.errorMessages
    console.log(authStore.errorMessages)
    hasErrorAlert.value=true;
  }
  
}

const navigateToForgotPassword = () => {
  router.replace(route.query.to ? String(route.query.to) : '/forgot-password')
}

// customize validator
const emailValidator = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid';
};

const isPasswordVisible = ref(false)

</script>

<template>

  <div class="auth-wrapper">
    <div class="auth-content">
    <VCard
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
              height="180"
              width="200"
            />
            
          </div>
        </template>

       
      </VCardItem>

      <VCardText class="pt-2">
        <h5 class="text-h5 font-weight-semibold mb-1">
          Welcome to UNIMAS! 
        </h5>
        <p class="mb-0">
          Please login to your account.
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
              <VTextField
                v-model="password"
                label="Password"
                placeholder="············"
                :rules="[requiredValidator, passwordValidator]"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />

              <VAlert
                v-if="hasErrorAlert"
                type="error"
                variant="tonal"
                closable
                class="mt-2"
              >
             
                {{ errorMessages }}
              </VAlert>

              <!-- remember me checkbox -->
              <div class="d-flex align-center justify-space-between flex-wrap mt-1 mb-4">
                <a
                  style="cursor: pointer;"
                  class="ms-2 mb-1 text-primary"
                  @click="navigateToForgotPassword"
                >
                  Forgot Password?
                </a>
              </div>

              <!-- login button -->
              <VBtn
                block
                type="button"
                @click="$refs.refForm.validate().then(() => login())"
              >
                Login
              </VBtn>
            </VCol>
          </VRow>
        </VForm>
      </VCardText>
    </VCard>
    </div>
  </div>
</template>

<style scoped>
.auth-wrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  background: url('/resources/images/background/background.jpg') no-repeat center center;
  background-size: cover;
  block-size: 100vh;
}

.auth-content {
  position: absolute;
  z-index: 1;
  border-radius: 10px;
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
