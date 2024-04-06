<script setup>
import { requiredValidator } from '@/@core/utils/validators';
import axios from 'axios';

const props = defineProps({
  pendingOrders: {
    type: Object,
    required: true
  },
  getPendingOrders: {
    type: Function,
    required: true
  }
})

const clickedItem = ref({})
const openPendingDialog = ref(false)
const openDecisionDialog = ref(false)
const meet_dateTime = ref(null)
const remark_seller = ref('')
const preferredDates = ref([])
const decision= ref('')
const isApprovedAlert = ref(false)
const isRejectedAlert = ref(false)


const viewPendingOrder = (item) => {
  openPendingDialog.value = true;
  clickedItem.value = item;
  extractPreferredDates([item]);
  
}

const openApproveDialog = () => {
  openDecisionDialog.value = true;
  decision.value = 'approve';
}

const openRejectDialog = () => {
  openDecisionDialog.value = true;
  decision.value = 'reject';
}

const extractPreferredDates = (pendingOrders) => {
  preferredDates.value = [];

  // extract the dates from the remark_buyer, the format of remark_buyer is "Preferred meet up date and time:\n• 2024/4/11 7:32 PM\ndewf", 
  //only extract the date and time part 2024/4/11 7:32 PM, there might be multiple dates in the remark_buyer separated by new line, make sure the AM/PM is correct
  pendingOrders.forEach((item) => {
    const remark = item.remark_buyer;
    const dates = remark.match(/(\d{4}\/\d{1,2}\/\d{1,2} \d{1,2}:\d{2} [AP]M)/g);
    if(dates) {
      dates.forEach((date) => {
        preferredDates.value.push(date);
      })
    }
  })
  return preferredDates;
};

// meet_dateTime is in format 11/4/2024, 02:29 PM, we need to convert it to yyyy-mm-dd hh:mm:ss, make sure follow the AM/PM 
const convertDateTime = (dateTime) => {
    const date = new Date(dateTime);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    let hours = date.getHours();
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = '00';
    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
  };


//confirm order
const confirmedOrder = () => {

  meet_dateTime.value = convertDateTime(meet_dateTime.value);

  if(decision.value === 'reject') {
    meet_dateTime.value = null;
  }
  const data = {
    meet_dateTime: meet_dateTime.value,
    remark_seller: remark_seller.value
  }

  axios.put('/api/order-items/confirmed-order/' + clickedItem.value.id, data)
    .then(response => {
      openDecisionDialog.value = false;
      openPendingDialog.value = false;
      meet_dateTime.value = null;
      remark_seller.value = '';
      if(decision.value === 'approve') {
        isApprovedAlert.value = true;
      } else {
        isRejectedAlert.value = true;
      }
      decision.value = '';

      // refresh the pending orders
      props.getPendingOrders();

      // refresh the page 1.5 seconds after the order is confirmed
      setTimeout(() => {
        location.reload();
      }, 1500);
      
    })
    .catch(error => {
      console.log(error)
    })
}





</script>
<template>
  <VTable v-if="pendingOrders.length > 0" height="250" fixed-header>
    <thead>
      <tr>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-info-i" />
          Order ID
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-product-hunt-line" />
          Product
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-id-card-line" />
          Buyer
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-calendar-schedule-line" />
          Order Date
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-verified-badge-line" />
          Status
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-eye-line" />
          View
        </th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="item in pendingOrders" :key="item.id">
        <td class="text-center">#{{ item.id }}</td>
        <td>
          <div class="d-flex align-items-center">
            <VAvatar
              v-if="item.item.pictures.length > 0 && item.item.pictures"
              size="32"
              rounded="sm"
              style="margin-inline-end: 8px;"
            >
              <VImg :src="item.item.pictures[0].picture_path" />
            </VAvatar>
            <div>
              <div>{{ item.item.name }}</div>
              <div style="color: #6c757d; font-size: 12px;">{{ item.item.type }}</div>
            </div>
          </div>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <VAvatar
              v-if="item.user.avatar"
              size="32"
              :color="item.user.avatar ? '' : 'primary'"
              class="mr-1"
            >
              <VImg :src="item.user.avatar" />
            </VAvatar>
            <div>
              <div>{{ item.user.username }}</div>
              <div style="color: #6c757d; font-size: 12px;">{{ item.user.email }}</div>
            </div>
          </div>
        </td>
        <td class="text-center">{{ item.order_dateTime }}</td>
        <td class="text-center">
          <div class="status-dot">
            <VChip color="warning">{{ item.status }}</VChip>
          </div>
        </td>

        <td class="text-center">
          <VBtn
            color="warning"
            style="margin-inline: 15px 3px"
            size="small"
            @click="viewPendingOrder(item)"
          >
            <VIcon
              icon="ri-eye-line"
            />
            <VTooltip
                open-delay="500"
                location="top"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>Confirm Order</span>
              </VTooltip>
          </VBtn>
        </td>
      </tr>
      
    </tbody>
  </VTable>

  <VTable v-else height="140" fixed-header>
    <thead>
      <tr>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-info-i" />
          Order ID
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-product-hunt-line" />
          Product
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-id-card-line" />
          Buyer
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-calendar-schedule-line" />
          Order Date
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-verified-badge-line" />
          Status
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-eye-line" />
          View
        </th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td colspan="6" class="text-center">
          <VAlert  
            variant="tonal"
            type="warning"
            class="mt-2"
            color="primary"
            closable
            dense
          >
            No pending orders found.
          </VAlert>
        </td>
      </tr>
    </tbody>
    
    </VTable>



  <!--Pending Item Dialog-->
  <VDialog
    v-model="openPendingDialog"
    scrollable
    max-width="800"
  >

    <!-- Dialog Content -->
    <VCard>
      <VCardItem class="pb-3">
        <h3 class="mt-2">Order Information</h3>
      </VCardItem>
        <VCardText>
          <div class="detail-row">
            <strong>Order at</strong>
            <span>: {{ clickedItem.order_dateTime }}</span>
          </div>
          <div class="detail-row">
            <strong>Place to meet</strong>
            <span>: {{ clickedItem.place_to_meet }}</span>
          </div>
          <div class="detail-row">
            <strong>Quantity</strong>
            <span>: {{ clickedItem.quantity }}</span>
          </div>
          <div class="detail-row">
            <strong>Remark</strong>
            <span>: {{ clickedItem.remark_buyer }}</span>
          </div>
          <h3 class="mt-6">Customer Information</h3>
        

        <VCardText class="font-weight-medium text-high-emphasis text-center text-truncate" style="display: flex; align-items: center;">
      
        <VAvatar size="40"
  
            :color="clickedItem.user.avatar ? '' : 'primary'"
            :class="`${!clickedItem.user.avatar ? 'v-avatar-light-bg primary--text' : ''}`"
            :variant="!clickedItem.user.avatar ? 'tonal' : undefined"
            style="margin-inline-end: 8px;">
            <VImg
            :src="clickedItem.user.avatar"/>
        </VAvatar>
        {{ clickedItem.user.username }}

        </VCardText>  
  
          <h3>Product Information</h3>
          <VCarousel delimiter-icon="ri-circle-fill" height="250px" hide-delimiter-background show-arrows="hover">
            <VCarouselItem v-for="(image, index) in clickedItem.item.pictures" :key="index" >
              <VImg :src="image.picture_path" height="250px" object-fit="cover" class="mt-2 ml-2 mr-2" />
            </VCarouselItem>
          </VCarousel>
        
          <VCardTitle>
            <VIcon icon="ri-shopping-bag-4-line" />
              {{ clickedItem.item.name }}
          </VCardTitle>
          <VCardTitle>
          <VIcon icon="ri-money-dollar-circle-line" />

          RM {{clickedItem.item.price}}</VCardTitle>

        </VCardText>

        

      <VCardText class="pt-5 d-flex justify-content-center" style="margin-inline-start:300px">
        <VBtn
          color="error"
          class="me-4"
          @click="openRejectDialog"
        >
          Reject
        </VBtn>
        <VBtn
          color="success"
          @click="openApproveDialog"
        >
          Approve
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>


  <!--Approve Dialog-->
  <VDialog
    v-model="openDecisionDialog"
    max-width="600"
  >
    <!-- Dialog Content -->
    <VCard title="Confirm Order Information">

      <VCardText>
        <VForm 
        ref="refForm" 
        @submit.prevent>
        <VRow>
          <!-- 👉 Select date -->
          <VCol cols="12" v-if="decision === 'approve'">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Selected Date and Time</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
              <VCombobox  
                v-model="meet_dateTime"
                :items="preferredDates"
                placeholder="Select Date from remarks buyer"
                label="Select Date"
                prepend-inner-icon="ri-calendar-2-line"
                scrollable  
                chips

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
                  v-model="remark_seller"
                  prepend-inner-icon="ri-chat-4-line"
                  placeholder="Enter Remark for Buyer"
                  :rules="[requiredValidator]"
                />
              </VCol>
            </VRow>
          </VCol>

          <!-- 👉 submit and reset button -->
          <VCol
            offset-md="3"
            cols="12"
            md="9"
            class="d-flex gap-4"
          >
          
            <VBtn
            class="ml-8"
              color="secondary"
              @click="openDecisionDialog = false"
              variant="tonal"
            >
              Close
            </VBtn>
            <VBtn
              type="submit"
              @click="$refs.refForm.validate().then(() => confirmedOrder())"
            >
              Send
            </VBtn>
          </VCol>
        </VRow>
      </VForm>
      </VCardText>
    </VCard>
  </VDialog>

  <!-- Approved Successfully Order -->
  <VSnackbar
      v-model="isApprovedAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      Order has been successfully approved.
  </VSnackbar>

  <VSnackbar
      v-model="isRejectedAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      Order has been successfully rejected.
  </VSnackbar>


  
</template>

<style scoped>
.status-dot::before {
  color: #ffc107; /* Color of the dot */
  content: '\2022'; /* Unicode character for a bullet point */
  font-size: 20px; /* Size of the dot */
  margin-inline-end: 8px; /* Spacing between the dot and the text */
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
  flex-shrink: 0; /* Prevent shrinking */
  inline-size: 120px; /* Adjust the width as needed */
}

.detail-row span {
  flex-grow: 1; /* Allow growing to fill the remaining space */
}
</style>
