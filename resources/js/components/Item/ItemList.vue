<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import UserProfileDialog from '@/components/Profile/UserProfileDialog.vue';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from 'axios';
import { VForm } from 'vuetify/components/VForm';
  
const props = defineProps({
  allItems: {
    type: Object,
    required: true,
  },
  allItemLoad: {
    type: Function,
    required: true,
  },
});

const openItemDialog = ref(false);
const clickedItem = ref({});
const openDialog = ref(false);
const clickedUser = ref({});
const checkStatusDialog = ref(false);
const meetDateTime = ref('');
const quantity = ref('');
const remark_buyer = ref('');
const remark_buyer_dateTime = ref('');
const isAddAlert = ref(false);  
const placeToMeet = ref(null);
const hasErrorAlert = ref(false);
const errorMessages = ref('');
const approximated_price = computed(() => {
  if (clickedItem.value.price && quantity.value) {
    return clickedItem.value.price * quantity.value;
  }
  return 0;
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

const seeItem = (item) => {
  openItemDialog.value = true;
  clickedItem.value = item;
};

const submitOrder = async () => {

  if (!meetDateTime.value || !placeToMeet.value || !quantity.value) {
    errorMessages.value = 'Please fill in Meet Up Date and Time, Place to Meet, and Quantity.';
    hasErrorAlert.value = true;
    return;
  }

  const formData = {
    place_to_meet: placeToMeet.value,
    quantity: quantity.value,
    remark_buyer: remark_buyer_dateTime.value + '\n' + remark_buyer.value,
    item_id: clickedItem.value.item_id,
    approximated_price: approximated_price.value,
    status: 'Pending',
  };

  try {
    await axios.post('/api/order-items/store', formData);
    checkStatusDialog.value = false;
    openItemDialog.value = false;
    props.allItemLoad();
    meetDateTime.value = '';
    placeToMeet.value = null;
    quantity.value = '';
    remark_buyer.value = '';
    remark_buyer_dateTime.value = '';
    isAddAlert.value = true;
    
  } catch (error) {
    errorMessages.value = error.response.data.message;
    hasErrorAlert.value = true;
    console.error(error);
  }
};

watch(meetDateTime, (newValue) => {
  if (newValue) {
    const dateObj = new Date(newValue);
    // Change the hour and minutes to 12-hour format, and arrange the formattedDate to yyyy/mm/dd hh:mm AM/PM
    const formattedDate = `${dateObj.getFullYear()}/${dateObj.getMonth() + 1}/${dateObj.getDate()} ${dateObj.getHours() % 12 || 12}:${String(dateObj.getMinutes()).padStart(2, '0')} ${dateObj.getHours() >= 12 ? 'PM' : 'AM'}`;
    if (remark_buyer_dateTime.value.includes('Preferred meet up date and time:')) {
      remark_buyer_dateTime.value = `${remark_buyer_dateTime.value}\n• ${formattedDate}`;
    } else {
      remark_buyer_dateTime.value = `Preferred meet up date and time:\n• ${formattedDate}`;
    }
  }
});

const openProfileDialog = (user) => {
  openDialog.value = true;
  clickedUser.value = user;
};

watch(openItemDialog, (newValue) => {
  if (!newValue) {
    meetDateTime.value = '';
    placeToMeet.value = null;
    quantity.value = '';
    remark_buyer.value = '';
    remark_buyer_dateTime.value = '';
  } 
});


</script>

<template>
  <VRow 
    v-if="allItems.length > 0"
    class="mt-1 ml-1 mr-1">
    <VCol
      v-for="(item) in allItems"
      :key="item.item_id"
      cols="12"
      md="3"
      sm="6"

    >
      <VCard class="mb-2 box-style" style="block-size:430px" @click="seeItem(item)">
        <VCarousel delimiter-icon="ri-circle-fill" height="250px" hide-delimiter-background show-arrows="hover">
          <VCarouselItem v-for="(image, index) in item.pictures" :key="index" >
            <VImg :src="image.picture_path" height="250px" object-fit="cover" class="mt-2 ml-2 mr-2" />
          </VCarouselItem>
        </VCarousel>
        <VCardTitle>
          <VChip
            color="primary"
            class="mr-2"
            size="small"
          >
          <VIcon icon="ri-price-tag-3-line" class="mr-2"/>
          {{ item.type }}</VChip>
        </VCardTitle>
        <VRow class="ml-3 mt-2">
          <VIcon icon="ri-shopping-bag-4-line" class="mr-2"/>
            {{ item.name }}
        </VRow>
        <VRow class="ml-3 mt-4">
          
            <VIcon icon="ri-money-dollar-circle-line" class="mr-2"/>

          RM {{item.price}}
        </VRow>
        <VRow class="ml-3 mt-4 mb-4">
          <VIcon icon="ri-thumb-up-line" class="mr-2"/>
              {{ item.condition }}
            
        </VRow>
        <VRow class="ml-3 mt-4">
          <VChip
              v-if="item.rating !== null"
              color="secondary"
              size="small"
              variant="outlined"
            ><VIcon icon="ri-star-fill" class="mr-1" style="color: #fbb400;"/> 
              {{ item.rating }} / 5
            </VChip>
            
            <VChip
              v-else
              color="secondary"
              variant="outlined"
           
              size="small"
            >
              No ratings yet
            </VChip>  
            <span class="small-rating mt-1 ml-1">({{ item.rate_by }} rates)</span>
            
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
        No selling items available at the moment.
        </VAlert>
      </VCard>
    </VCol>
  </VRow>

  <!--Clicked Item Dialog-->
  <VDialog
    v-model="openItemDialog"
    scrollable
    max-width="500"
    style="overflow-y: hidden;"
  >

    <!-- Dialog Content -->
    <VCard>
      <VCardItem class="pb-3 text-center">
        <h3>Item Details</h3>
      </VCardItem>
      <VCardText style="block-size: 420px;">
        <VCarousel delimiter-icon="ri-circle-fill" height="250px" hide-delimiter-background show-arrows="hover">
          <VCarouselItem v-for="(image, index) in clickedItem.pictures" :key="index" >
            <VImg :src="image.picture_path" height="250px" object-fit="cover" class="mt-2 ml-2 mr-2" />
          </VCarouselItem>
        </VCarousel>
   
      <VRow class="mt-2">
        <VCol cols="12" md="7" class="mt-2">
        <VCardTitle>
          <VIcon icon="ri-shopping-bag-4-line" />
            {{ clickedItem.name }}
        </VCardTitle>
        <VCardTitle>
          <VIcon icon="ri-money-dollar-circle-line" />

          RM {{clickedItem.price}} 
        </VCardTitle>
      </VCol>
      <VCol class="mt-4 text-center" cols="12" md="5" style="cursor:not-allowed">
        <div v-if="clickedItem.rating!==null" class="box-style">
          <h2 class="ml-4 mt-4">{{ clickedItem.rating }}<span style="font-size:0.5em; font-weight: normal; ">({{ clickedItem.rate_by }} rates)</span></h2>
          <VRating
            v-if="clickedItem.rating !== null"
            v-model="clickedItem.rating"
            half-increments
            color="warning"
            size="x-small"
            class="ml-2"
          />
        </div>
        <div v-else class="box-style">
          <h4 class="text-center mt-4 ml-2">No ratings yet</h4>
          <VRating
            v-if="clickedItem.rating === null"
            v-model="clickedItem.rating"
            half-increments
            color="warning"
            size="x-small"
            class="ml-2 mt-2"
          />
        </div>

        </VCol>
      </VRow>
  

        <h3 class="mt-3">Seller Information</h3>
        <VCardText v-if="clickedItem.isOwn === false" class="font-weight-medium text-high-emphasis text-truncate" style="display: flex; align-items: center;cursor: pointer;" @click="openProfileDialog(clickedItem.user)">
      
        <VAvatar size="40"
            :color="clickedItem.user.avatar ? '' : 'primary'"
            :class="`${!clickedItem.user.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
            :variant="!clickedItem.user.avatar ? 'tonal' : undefined"
            style="margin-inline-end: 8px;">
            <VImg
            :src="clickedItem.user.avatar"/>
        </VAvatar>
        <div style="display: inline-block; vertical-align: top;">
          <VTooltip
            location="end"
            open-delay="500"
            activator="parent"
            transition="scroll-y-transition"
          >
            <span>Click to view profile</span>
          </VTooltip>
          <div>{{ clickedItem.user.username }}</div>
          <div style="color: #6c757d; font-size: 12px;">{{ clickedItem.user.email }}</div>
        </div>

        </VCardText>  
        <VCardText  v-else class="font-weight-medium text-high-emphasis text-truncate" style="display: flex; align-items: center;">
      
          <VAvatar size="40"
              :color="clickedItem.user.avatar ? '' : 'primary'"
              :class="`${!clickedItem.user.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
              :variant="!clickedItem.user.avatar ? 'tonal' : undefined"
              style="margin-inline-end: 8px;">
              <VImg
              :src="clickedItem.user.avatar"/>
          </VAvatar>
          <div style="display: inline-block; vertical-align: top;">
            <VTooltip
            location="end"
            open-delay="500"
            activator="parent"
            transition="scroll-y-transition"
          >
            <span>You are the seller</span>
          </VTooltip>
            <div>{{ clickedItem.user.username }}</div>
            <div style="color: #6c757d; font-size: 12px;">{{ clickedItem.user.email }}</div>
          </div>
          

          </VCardText>  
        <h3 class="mt-3">Details</h3>

        <VCardText>
          <div class="detail-row">
            <strong>Condition</strong>
            <span>: {{ clickedItem.condition }}</span>
          </div>
          <div class="detail-row">
            <strong>Type</strong>
            <span>: {{ clickedItem.type }}</span>
          </div>
          <div class="detail-row">
            <strong>Quantity</strong>
            <span>: {{ clickedItem.quantity }}</span>
          </div>
          <div class="detail-row">
            <strong>Created At</strong>
            <span>: {{ clickedItem.created_at }}</span>
            
          </div>
        </VCardText>

        <h3 class="mt-2">Description</h3>
        <VCardText style="white-space: pre-line;">
          {{ clickedItem.description }}
        </VCardText>


      </VCardText>

      <VCardText class="pt-5 text-end">
        <VSpacer />
        <VBtn
          variant="outlined"
          color="secondary"
          class="me-4"
          @click="openItemDialog = false"
        >
          Close
        </VBtn>
        <VBtn 
          v-if="clickedItem.isOwn === false"
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
    max-width="650"
    class="dialog"
    style="overflow-x: hidden;"
    
  >
    <!-- Dialog Content -->
    <VCard title="Order Item Information">

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
                <label for="firstNameHorizontalIcons">Preferred Meet Up Date and Time</label>
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
                  <span> You can select more than one date and time for seller to choose.</span>
                </VTooltip>
                <VueDatePicker
                  teleport-center
                  :clearable="true"
                  :min-date="new Date()"
                  v-model="meetDateTime"
                  placeholder="Select Date and Time"
                  required
                  
                />
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
                v-model="placeToMeet"
                :items="placesInUnimas"
                prepend-inner-icon="ri-map-pin-line"
                hide-selected
                :hide-no-data="false"
                placeholder="Select Place"
                persistent-hint
                :rules="[requiredValidator]"
              >
              
                <template #no-data>
                  <VListItem>
                    <VListItemTitle>
                      No results matching. Press <kbd>enter</kbd> to use it.<br>
                      <small>You can create your own place to meet by typing in the input field.</small>
                    </VListItemTitle>
                  </VListItem>
                </template>
              </VCombobox>
                
              </VCol>
            </VRow>
          </VCol>

        
          <!-- 👉 Mobile -->
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="mobileHorizontalIcons">Quantity</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
                <VTextField
                  v-model="quantity"
                  prepend-inner-icon="ri-survey-line"
                  placeholder="Enter Quantity"
                  type="number"
                  :rules="[requiredValidator, (v) => v > 0 || 'Quantity must be greater than 0']"
                />
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
                  ~ RM {{ approximated_price }}
                </VChip>
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
                <label for="remarkHorizontalIcons">Remark for Meet Up</label>
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
                <VTextarea
                  v-model="remark_buyer_dateTime"
                  disabled
                  scrollable
                  prepend-inner-icon="ri-chat-4-line"
                  placeholder="Enter Remark"
                  :rules="[requiredValidator]"
                  
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
            offset-md="7"
            cols="12"
            md="9"
            class="d-flex gap-4"
          ><VBtn
              color="secondary"
    
              variant="tonal"
              @click="meetDateTime = ''; placeToMeet = null; quantity = null; remark_buyer = ''; remark_buyer_dateTime = '';"
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

  <VDialog
    v-model="openDialog"
    max-width="450"
  >
    <UserProfileDialog :clickedUser="clickedUser" />
  </VDialog>

  <!-- Add Alert -->
  <VSnackbar
      v-model="isAddAlert"
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
.box-style {
  padding: 1.5px; /* Padding around the table */
  border: 0.4px solid #282828;
  border-radius:10px;
  background-color: #fff; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
  margin-block-end: 15px
}

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

.small-rating {
  font-size: 0.8em; /* Adjust the font size as needed */
}
</style>

