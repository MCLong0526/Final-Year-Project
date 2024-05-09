<script setup>
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from 'axios';
import { watch } from 'vue';
import { VForm } from 'vuetify/components/VForm';

const props = defineProps({
  allServices: {
    type: Object,
    required: true,
  },
  allServicesLoad:{
    type:Function,
    required:true
  }

});

const placesInUnimas = [
  'Faculty of Cognitive Sciences and Human Development',
  'Faculty of Computer Science and Information Technology',
  'Faculty of Economics and Business',
  'Faculty of Engineering',
  'Faculty of Applied and Creative Arts',
  'Faculty of Medicine and Health Sciences',
  'Faculty of Resource Science and Technology',
  'Faculty of Social Sciences and Humanities',
  'Faculty of Built Environment',
  'Faculty of Education, Language and Communication',
];

const openServiceDialog = ref(false);
const clickedService = ref(null);
const checkStatusDialog = ref(false);
const serviceDate = ref(null);
const serviceStartTime = ref(null);
const serviceEndTime = ref(null);
const placeToService = ref(null);
const remark_buyer = ref('');
const remark_buyer_dateTime = ref('');
const startTimeEnabled = ref(true);
const endTimeEnabled = ref(true);
const estimatedDuration = ref('');
const estimatedPrice = ref(0);
const hasAddAlert = ref(false);
const hasErrorAlert = ref(false);
const errorMessages = ref('');

// minTime and maxTime is an Object in format { hours: 11, minutes: 30 }
const minTime = ref({ hours: 0, minutes: 0 });
const maxTime = ref({ hours: 23, minutes: 59 });

const seeService = (service) => {
  openServiceDialog.value = true;
  clickedService.value = service;
};


// Date and time validation starts here
watch(serviceDate, (newValue) => {
  //reset the serviceStartTime and serviceEndTime when the date is changed

  if (newValue) {
    const dateObj = new Date(newValue);
    //arrange the formattedDate to yyyy-mm-dd
    const formattedDate = `${dateObj.getFullYear()}/${String(dateObj.getMonth() + 1).padStart(2, '0')}/${String(dateObj.getDate()).padStart(2, '0')}`;


    // if the serviceDate is changed, then reset the serviceStartTime and serviceEndTime
    if(remark_buyer_dateTime.value.includes('Preferable Service Date and Time')){
      //remove all the time
      remark_buyer_dateTime.value = `${remark_buyer_dateTime.value.split(':')[0]}: • ${formattedDate},`;
    } else {
      remark_buyer_dateTime.value = `Preferable Service Date and Time: • ${formattedDate},`;
      
    }
    startTimeEnabled.value = false
  }
});

//watch serviceStartTime, then set the minTime, make sure the serviceEndTime is after serviceStartTime
watch(serviceStartTime, (newValue) => {
  if (newValue) {
    minTime.value = {
      hours: newValue.hours,
      minutes: newValue.minutes,
    };
    endTimeEnabled.value = false;

    const formattedStartTime = formatTime(newValue.hours, newValue.minutes);
    const remarkWithoutTime = remark_buyer_dateTime.value.split(',')[0];
    remark_buyer_dateTime.value = `${remarkWithoutTime}, ${formattedStartTime}-`;
  } else {
    minTime.value = {
      hours: 0,
      minutes: 0,
    };
  }
});


watch(serviceEndTime, (newValue) => {
  if (newValue) {
    maxTime.value = {
      hours: newValue.hours,
      minutes: newValue.minutes,
    };

    const formattedEndTime = formatTime(newValue.hours, newValue.minutes);
    const remarkWithoutTime = remark_buyer_dateTime.value.split('-')[0];
    remark_buyer_dateTime.value = `${remarkWithoutTime}-${formattedEndTime}`;
  } else {
    maxTime.value = {
      hours: 23,
      minutes: 59,
    };
  }
});


function formatTime(hours, minutes) {
  const ampm = hours >= 12 ? 'PM' : 'AM';
  const formattedHours = hours % 12 || 12; // Convert to 12-hour format
  const formattedMinutes = String(minutes).padStart(2, '0');
  return `${formattedHours}:${formattedMinutes} ${ampm}`;
}

watch([serviceStartTime, serviceEndTime], ([startTime, endTime]) => {
  if (startTime && endTime) {
    const startHours = startTime.hours;
    const startMinutes = startTime.minutes;
    const endHours = endTime.hours;
    const endMinutes = endTime.minutes;

    const startInMinutes = startHours * 60 + startMinutes;
    const endInMinutes = endHours * 60 + endMinutes;

    const durationInMinutes = endInMinutes - startInMinutes;
    const hours = Math.floor(durationInMinutes / 60);
    const minutes = durationInMinutes % 60;

    estimatedDuration.value = `${hours} hours ${minutes} minutes`;
    estimatedPrice.value = (hours * clickedService.value.price_per_hour).toFixed(2);
    //set the estimated price 
  }
});

// Date and time validation ends here


const submitOrder = async () => {

  if (!serviceDate.value || !serviceStartTime.value || !serviceEndTime.value || !placeToService.value) {
    errorMessages.value = 'Please fill in Service Date, Service Start Time, Service End Time and Place To Meet';
    hasErrorAlert.value = true;
    return;
  }

  const formData = {
    place_to_service: placeToService.value,
    remark_customer: remark_buyer_dateTime.value + '\n' + remark_buyer.value,
    service_id: clickedService.value.service_id,
    status: 'Pending',
    approximated_price: estimatedPrice.value,

  };

  try {
    await axios.post('/api/order-services/store', formData);
    checkStatusDialog.value = false;
    hasAddAlert.value = true;
    openServiceDialog.value = false;
    props.allServicesLoad();
    remark_buyer.value = '';
    remark_buyer_dateTime.value = '';
    serviceDate.value = null;
    serviceStartTime.value = null;
    serviceEndTime.value = null;
    placeToService.value = null;
    estimatedDuration.value = '';
    estimatedPrice.value = 0;
    
    
  } catch (error) {
    errorMessages.value = error.response.data.message;
    hasErrorAlert.value = true;
    console.error(error);
  }
};


</script>

<template>
  <VRow
    v-if="allServices.length > 0" 
    class="mt-1 ml-1 mr-1">
    <VCol
      v-for="(service) in allServices"
      :key="service.service_id"
      cols="12"
      md="3"
      sm="6"
    >
      <VCard class="mb-4" style="block-size:360px" @click="seeService(service)">
        <VCarousel delimiter-icon="ri-circle-fill" height="250px" hide-delimiter-background show-arrows="hover">
          <VCarouselItem v-for="(picture, index) in service.pictures" :key="index" >
            <VImg :src="picture.picture_path" height="250px" object-fit="cover" class="mt-2 ml-2 mr-2" />
          </VCarouselItem>
        </VCarousel>
        <VCardTitle>
          <VChip
            color="primary"
            class="mr-2"
            size="small"
          >
          <VIcon icon="ri-price-tag-3-line" class="mr-2"/>
          {{ service.type }}</VChip>
        </VCardTitle>
        <VRow class="ml-3 mt-2">
          <VIcon icon="ri-money-dollar-circle-line" class="mr-2"/>

          RM {{service.price_per_hour}}
        </VRow>
        <VRow class="ml-3 mt-4">
          <VIcon icon="ri-shopping-bag-4-line" class="mr-2"/>
            {{ service.name }}
        </VRow>

      </VCard>
    </VCol>
  </VRow>
  <VRow v-else>
    <VCol cols="12">
      <VCard>
        <VAlert  
          variant="tonal"
          type="warning"
          class="mt-2 text-center"
          color="primary"
          dense
          closable
        >
          No service available at the moment.
        </VAlert>
      </VCard>
    </VCol>
  </VRow>

  <!--Clicked Service Dialog-->
  <VDialog
    v-model="openServiceDialog"
    scrollable
    max-width="500"
  >

    <!-- Dialog Content -->
    <VCard>
      <VCardItem class="pb-3">
        <VCardTitle>Service Details</VCardTitle>
      </VCardItem>
      <VCardText style="block-size: 420px;">
        <VCarousel delimiter-icon="ri-circle-fill" height="250px" hide-delimiter-background show-arrows="hover">
          <VCarouselItem v-for="(picture, index) in clickedService.pictures" :key="index" >
            <VImg :src="picture.picture_path" height="250px" object-fit="cover" class="mt-2 ml-2 mr-2" />
          </VCarouselItem>
        </VCarousel>
       
        <VCardTitle>
          <VIcon icon="ri-shopping-bag-4-line" />
            {{ clickedService.name }}
        </VCardTitle>
        <VCardTitle>
          <VIcon icon="ri-money-dollar-circle-line" />

          RM {{clickedService.price_per_hour}} per hour
        </VCardTitle>

        <h3 class="mt-2">Service Provider Information</h3>
        <VCardText class="font-weight-medium text-high-emphasis text-truncate" style="display: flex; align-items: center;">
      
        <VAvatar size="40"
            :color="clickedService.user.avatar ? '' : 'primary'"
            :class="`${!clickedService.user.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
            :variant="!clickedService.user.avatar ? 'tonal' : undefined"
            style="margin-inline-end: 8px;">
            <VImg
            :src="clickedService.user.avatar"/>
        </VAvatar>
        <div style="display: inline-block; vertical-align: top;">
          <div>{{ clickedService.user.username }}</div>
          <div style="color: #6c757d; font-size: 12px;">{{ clickedService.user.email }}</div>
        </div>
        

        </VCardText>  

        <h3 class="mt-2">Details</h3>

        <VCardText>

          <div class="detail-row">
            <strong>Type</strong>
            <span>: {{ clickedService.type }}</span>
          </div>
          <div class="detail-row">
            <strong>Available</strong>
            <span v-if="clickedService.availability=='Available'">:<VChip prepend-icon="ri-checkbox-circle-line" color="success">{{ clickedService.availability }}</VChip></span>
            <span v-if="clickedService.availability=='Unavailable'">:<VChip prepend-icon="ri-close-circle-line" color="error">{{ clickedService.availability }}</VChip></span>
          </div>
          <div class="detail-row">
            <strong>Created At</strong>
            <span>: {{ clickedService.created_at }}</span>
            
          </div>

        </VCardText>

        <h3 class="mt-2">Description</h3>
        <VCardText style="white-space: pre-line;">
          {{ clickedService.description }}
          </VCardText>

      </VCardText>

      <VCardText class="pt-5 text-end">
        <VSpacer />
        <VBtn
          variant="outlined"
          color="secondary"
          class="me-4"
          @click="openServiceDialog = false"
        >
          Close
        </VBtn>
        <VBtn 
          v-if="clickedService.isOwn === false"
          @click="checkStatusDialog = true"
          
        >
          <VTooltip
            location="top"
            activator="parent"
            transition="scroll-y-transition"
          >
            <span>Interested? </span>
          </VTooltip>
          Check Status
        </VBtn>

      </VCardText>
    </VCard>
  </VDialog>


  <VDialog
    v-model="checkStatusDialog"
    scrollable
    max-width="900"

    class="dialog"
  >
    <!-- Dialog Content -->
    <VCard title="Order Service Information">

      <VCardText>
        <VForm 
        ref="refForm" 
        @submit.prevent>

        <VRow>
          <!-- Date -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Date To Service</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VueDatePicker
                  teleport-center
                  :clearable="true"
                  :min-date="new Date()"
                  v-model="serviceDate"
                  placeholder="Select Date"
                  :enable-time-picker="false"
                  required
                />
              </VCol>
            </VRow>
          </VCol>

          <!-- Start Time -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Service Start Time</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VueDatePicker
                  teleport-center
                  :clearable="true"
                  time-picker
                  :max-time="{ hours: maxTime.hours, minutes: maxTime.minutes }"
                  v-model="serviceStartTime"
                  :disabled="startTimeEnabled"
                  placeholder="Select Time"
                  
                  required
                />
              </VCol>
            </VRow>
          </VCol>

          <!-- End Time -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Service End Time</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VueDatePicker
                  teleport-center
                  :clearable="true"
                  time-picker
                  :min-time="{ hours: minTime.hours, minutes: minTime.minutes}"
                  v-model="serviceEndTime"
                  placeholder="Select Time"
                  
                  :disabled="endTimeEnabled"
                  required
                />
              </VCol>
            </VRow>
          </VCol>

          <!-- End Time -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Estimated Service Duration</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VChip
                  color="primary"
                  class="mr-2"
                  size="small"
                >
                  <VIcon icon="ri-time-fill" class="mr-2"/>
                  {{ estimatedDuration }}
                </VChip>
              </VCol>
            </VRow>
          </VCol>

          <!--Estimated Price-->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Approximated Price</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VChip
                  color="primary"
                  class="mr-2"
                  size="small"
                >
                  <VIcon icon="ri-money-dollar-circle-line" class="mr-2"/>
                  ~ RM {{ estimatedPrice }}
                </VChip>
              </VCol>
            </VRow>
          </VCol>


          <!--Place to meet-->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="placeHorizontalIcons">Preferred Place to Meet</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
              <VTooltip
                  location="top"
                  open-delay="500"
                  activator="parent"
                  transition="scroll-y-transition"
                >
                <span> You can create your own place to meet by typing in the input field.</span>
                </VTooltip>
              <VCombobox
                v-model="placeToService"
                :items="placesInUnimas"
                prepend-inner-icon="ri-map-pin-line"
                hide-selected
                :hide-no-data="false"
                placeholder="Select Place"
                persistent-hint
              >
                <template #no-data>
                  <VListItem>
                    <VListItemTitle>
                      No results matching. Press <kbd>enter</kbd> to use it.
                      <small>You can create your own place to meet by typing in the input field.</small>
                    </VListItemTitle>
                  </VListItem>
                </template>
              </VCombobox>
                
              </VCol>
            </VRow>
          </VCol>

          <!-- Remark -->
          <VCol cols="12">
            <VRow no-gutters style="cursor: not-allowed;">
              <VCol
                cols="12"
                md="3"
              >
                <label for="remarkHorizontalIcons">Remark for Service Date and Time</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
              <VTooltip
                location="top"
                open-delay="500"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>To remove the date and time, click on the reset button.</span>
              </VTooltip>
                <VTextField
                  v-model="remark_buyer_dateTime"
                  disabled
                  prepend-inner-icon="ri-chat-4-line"
                  placeholder="Enter Remark"
      
                />
              </VCol>
            </VRow>
          </VCol>

          <!-- Remark -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="remarkHorizontalIcons">Remark</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextarea
                  v-model="remark_buyer"
                  prepend-inner-icon="ri-chat-4-line"
                  placeholder="Enter Remark"
                />
              </VCol>
            </VRow>
          </VCol>


          <!-- 👉 submit and reset button -->
          <VCol
            offset-md="8"
            cols="12"
            md="9"
            class="d-flex gap-4"
          ><VBtn
              color="secondary"
              type="reset"
              variant="tonal"
            >
              Reset
            </VBtn>
          <VBtn
              type="submit"
              @click="$refs.refForm.validate().then(() => submitOrder())"
            >
              Send Request
            </VBtn>
            
          </VCol>
        </VRow>
      </VForm>
      </VCardText>
    </VCard>
  </VDialog>

   <!-- Add Alert -->
  <VSnackbar
      v-model="hasAddAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      Order has been successfully placed.
  </VSnackbar>

  <VSnackbar
      v-model="hasErrorAlert"
      location="top end"
      transition="scale-transition"
      color="error"
    >
    <VIcon size="20" class="me-2">ri-alert-line</VIcon>
      {{ errorMessages }}
  </VSnackbar>
 


</template>

<style scoped>
.item-details {
  font-size: larger;
}

.detail-row {
  display: flex;
  align-items: center;
  margin-block-end: 8px;
}

.detail-row strong {
  display: inline-block;
  inline-size: 100px; /* Adjust the width as needed */
}

.detail-row span {
  margin-inline-start: 8px;
}
</style>
