<script setup>
import { passwordValidator, requiredValidator } from '@/@core/utils/validators';
import { useAuthStore } from '@/plugins/store/AuthStore';
import logo from '@images/logos/UnimasLogo.png';
import { useTheme } from 'vuetify';
import { VForm } from 'vuetify/components/VForm';

const authStore = useAuthStore()

const email = ref('')
const password = ref('')
const route = useRoute()
const router = useRouter()

const vuetifyTheme = useTheme()

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark
})

const login = async () => {
 await authStore.login(email.value, password.value)
 if (authStore.isLoggedIn==true) {
    router.replace(route.query.to ? String(route.query.to) : '/test')
  }
  
}


// customize validator
const emailValidator = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid';
};

const isPasswordVisible = ref(false)
</script>

<template>

  <div class="auth-wrapper d-flex align-center justify-center pa-4">
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
              height="100"
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

              <!-- remember me checkbox -->
              <div class="d-flex align-center justify-space-between flex-wrap mt-1 mb-4">
             

                <a
                  class="ms-2 mb-1"
                  href="javascript:void(0)"
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

            <!-- create account -->
            <VCol
              cols="12"
              class="text-center text-base"
            >
              <span>New on our platform?</span>
              <RouterLink
                class="text-primary ms-2"
                to="/"
              >
                Create an account
              </RouterLink>
            </VCol>
            
          </VRow>
        </VForm>
      </VCardText>
    </VCard>

   
  </div>
</template>

<style lang="scss">
@use "@core-scss/pages/page-auth.scss";
</style>
