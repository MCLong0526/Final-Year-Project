<script setup>
import { passwordValidator, requiredValidator } from '@/@core/utils/validators';
import logo from '@images/logos/logo3.png';
import axios from 'axios';
import { computed, ref } from 'vue';
import { useTheme } from 'vuetify';


const email = ref('');
const password = ref('');
const phoneNumber = ref('');

const router = useRouter();
const hasErrorAlert = ref(false);
const username = ref('');
const confirmPassword = ref('');
const currentTab = ref('tab-1');
const errorMessages = ref('');
const correctEmail = ref(false);
const correctPhoneNumber = ref(false);
const isSeller = ref(false);
const hasSuccessAlert = ref(false);
const successMessages = ref('');
const correctUsername = ref(false);

const vuetifyTheme = useTheme();

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark;
});

// customize validator
const emailValidator2 = (v) => {
  const regex = /^[0-9]{5}@siswa\.unimas\.my$/;
  return regex.test(v) || 'Email format is invalid, please use your unimas email';
};

const phoneNumberValidator = (v) => /^0[0-9]{9,10}$/.test(v) || 'Phone number must be valid';

const isPasswordVisible = ref(false);


const checkValidations = () => {

  errorMessages.value = '';
  hasErrorAlert.value = false;

  // Check if username and email fields are filled
  if (username.value === '' || email.value === '' || phoneNumber.value === '') {
    errorMessages.value = 'Please fill in all the fields';
    hasErrorAlert.value = true;
    return;
  }

  if(username.value!==''&& correctUsername.value === false){
    // Check if the username is at least 6 characters
    if (username.value.length < 6) {
      errorMessages.value = 'Username must be at least 6 characters';
      hasErrorAlert.value = true;
      return;
    }else{
      checkUsername();
    }
  }else if(email.value !== ''){
    if (emailValidator2(email.value) !== true ){
      errorMessages.value = 'Email format is invalid, please use your unimas email';
      hasErrorAlert.value = true;
    } else {
      // Check if the email already exists
      checkEmail();
    }
  }

    // Check if the email is a valid unimas email
  

  // Check if the email field is not empty or null
 

  // Check if the phone number field is not empty or null
  if (phoneNumber.value !== '') {
    // Check if the phone number is a valid phone number
    if (phoneNumberValidator(phoneNumber.value) !== true) {
      errorMessages.value = 'Phone number format is invalid';
      hasErrorAlert.value = true;
    }else {
      correctPhoneNumber.value = true;
    }
  }

  if(correctEmail.value === true && correctPhoneNumber.value === true && correctUsername.value === true){
    currentTab.value = 'tab-2';
  }

};


const checkEmail = () => {
  errorMessages.value = '';
  hasErrorAlert.value = false;
  axios.get('/api/auth/check-email-exists/', {
    params: {
      email: email.value,
    },
  }).then((response) => {
    if (response.data.exists) {
      errorMessages.value = 'Email already exists';
      hasErrorAlert.value = true;
    } else {
      // Handle case where email doesn't exist
      // For example, clear error messages or set hasErrorAlert to false
      errorMessages.value = '';
      hasErrorAlert.value = false;
      correctEmail.value = true;

      if(correctEmail.value === true && correctPhoneNumber.value === true && correctUsername.value === true){
        currentTab.value = 'tab-2';
      }

    }
  }).catch((error) => {
    console.error('Error checking email:', error);
    // Handle error appropriately, e.g., show a generic error message to the user
    errorMessages.value = 'An error occurred while checking email.';
    hasErrorAlert.value = true;
  });
};

const checkUsername = () => {
  errorMessages.value = '';
  hasErrorAlert.value = false;
  axios.get('/api/auth/check-username-exists/', {
    params: {
      username: username.value,
    },
  }).then((response) => {
    if (response.data.exists) {
      errorMessages.value = 'Username already exists';
      hasErrorAlert.value = true;
    } else {
      // Handle case where username doesn't exist
      // For example, clear error messages or set hasErrorAlert to false
      errorMessages.value = '';
      hasErrorAlert.value = false;
      correctUsername.value = true;

      if(correctEmail.value === true && correctPhoneNumber.value === true && correctUsername.value === true){
        currentTab.value = 'tab-2';
      }
    }
  }).catch((error) => {
    console.error('Error checking username:', error);
    // Handle error appropriately, e.g., show a generic error message to the user
    errorMessages.value = 'An error occurred while checking username.';
    hasErrorAlert.value = true;
  });
};

watch(currentTab, (newValue) => {

  if (newValue === 'tab-2') {
    // Check email, username, and phone number validations
    checkValidations();
    if (errorMessages.value !== '') {
      currentTab.value = 'tab-1';
    }
    
  }
});


const register = async () => {
  //check password and confirm password
  if (password.value !== confirmPassword.value) {
    errorMessages.value = 'Password and Confirm Password do not match';
    hasErrorAlert.value = true;
    return;
  }
  await axios.post('/api/auth/register/', {
    username: username.value,
    email: email.value,
    phone_number: phoneNumber.value,
    password: password.value,
    isSeller: isSeller.value,
    status: 'active',
  }).then((response) => {
    // Handle successful registration
    successMessages.value = 'User registered successfully';
    hasSuccessAlert.value = true;

    //wait for 2 seconds before redirecting to login page
    setTimeout(() => {
      router.replace('/login');
    }, 2000);
  }).catch((error) => {
    console.error('Error registering user:', error);
    // Handle error appropriately, e.g., show a generic error message to the user
    errorMessages.value = error.response.data.message;
    hasErrorAlert.value = true;
  });
};




</script>

<template>
  <div class="auth-wrapper">
    <div class="auth-content">
      <VCard class="auth-card pa-4 pt-7" max-width="448">
        <RouterLink
              class="text-primary ms-2"
              to="/login"
          
            >
              <VIcon class="mb-1" icon="ri-arrow-left-double-line" />
              Go to Login
            </RouterLink>
        <VCardItem class="justify-center">
          <template #prepend>
            <div class="d-flex">
              <img :src="logo" alt="Materio" class="mr-2" height="120" width="160" />
            </div>
          </template>
        </VCardItem>

        <VCardText class="pt-2">
          <h5 class="text-h5 font-weight-semibold mb-1">Register Account</h5>
          <p class="mb-0">Register account to access the platform.</p>
        </VCardText>

        <VTabs v-model="currentTab" grow stacked>
          <VTab value="tab-1">
            <VIcon icon="ri-user-line" class="mb-1" />
            <span>Personal Information</span>
          </VTab>
          <VTab value="tab-2">
            <VIcon icon="ri-lock-line" class="mb-1" />
            <span>Setup Password</span>
          </VTab>
        </VTabs>

        <VWindow v-model="currentTab" class="mt-5">
          <VWindowItem value="tab-1">
            <VForm ref="refForm">
              <VRow class="mb-1">
                <!-- username-->
                <VCol cols="12" class="mt-1">
                  <VTextField v-model="username" label="Username" :rules="[requiredValidator]" />
                </VCol>
                <!-- email -->
                <VCol cols="12">
                  <VTextField v-model="email" label="Email" type="email" :rules="[requiredValidator, emailValidator2]" />
                </VCol>
                <!-- phone number --> 
                <VCol cols="12">
                  <VTextField v-model="phoneNumber" label="Phone Number" type="tel" :rules="[requiredValidator, phoneNumberValidator]" />
                </VCol>

                <VCol cols="6" class="ml-2">
                  <VCheckbox
                    v-model="isSeller"
                    label="Register as a seller"
                    :true-value="true"
                  />
                  <VTooltip
                  open-on-focus
                    open-delay="200"
                    location="end"
                    activator="parent"
                    transition="scroll-x-transition"
                  >
                    <span>After registration, you can also register as a seller in your profile settings.</span>
                  </VTooltip>
                </VCol>
               
              </VRow>
            </VForm>
          </VWindowItem>

          <VWindowItem value="tab-2">
            <VForm ref="refForm">
            <!-- password -->
            <VCol cols="12">
              <VTextField v-model="password" label="Password" placeholder="············" :rules="[requiredValidator, passwordValidator]" :type="isPasswordVisible ? 'text' : 'password'" :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'" @click:append-inner="isPasswordVisible = !isPasswordVisible" />
            </VCol>
            <VCol cols="12">
              <VTextField v-model="confirmPassword" label="Confirm Password" placeholder="············" :rules="[requiredValidator, passwordValidator]" :type="isPasswordVisible ? 'text' : 'password'" :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'" @click:append-inner="isPasswordVisible = !isPasswordVisible" />
            </VCol> </VForm>
          </VWindowItem>
       
        </VWindow>

        <!-- navigation buttons -->
        <div class="d-flex flex-wrap gap-4 justify-sm-space-between justify-center mt-2">
          <VBtn v-if="currentTab === 'tab-1'" color="secondary" variant="outlined" disabled style="cursor: not-allowed;">
            <VIcon icon="ri-arrow-left-line" start class="flip-in-rtl" />
            Previous
          </VBtn>
          <VBtn v-else @click="currentTab = 'tab-1'">
            <VIcon icon="ri-arrow-left-line" start class="flip-in-rtl" />
            Previous
          </VBtn>

          <VBtn v-if="currentTab === 'tab-1'" @click="$refs.refForm.validate().then((isValid) => { if (isValid) checkValidations(); })">
            Next
            <VIcon icon="ri-arrow-right-line" end class="flip-in-rtl" />
          </VBtn>
          <VBtn v-else @click="$refs.refForm.validate().then((isValid) => { if (isValid) register(); })">
            Register
          </VBtn>
        </div>
      </VCard>
    </div>
  </div>

  <VSnackbar
      v-model="hasErrorAlert"
      location="top end"
      transition="scale-transition"
      color="error"
    >
    <VIcon size="20" class="me-2">ri-error-warning-line</VIcon>
    <span>{{ errorMessages }}</span>
  </VSnackbar>

  <VSnackbar
      v-model="hasSuccessAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
    <span>{{ successMessages }}</span>
  </VSnackbar>
</template>

<style scoped>
.auth-wrapper {
  display: flex;
  align-items: center;
  justify-content: flex-start; /* Align items to the left */
  background: url('/resources/images/background/background4.jpg') no-repeat center center;
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
  margin-inline-start: 50px;
  max-inline-size: 448px;
}

.auth-card {
  padding: 24px;
  border-radius: 8px;
  background: rgba(255, 255, 255, 90%);
  box-shadow: 0 4px 6px rgba(0, 0, 0, 10%);
}
</style>
