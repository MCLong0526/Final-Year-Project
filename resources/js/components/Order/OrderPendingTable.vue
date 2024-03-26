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
const openApproveDialog = ref(false)
const meet_dateTime = ref(null)
const remark_seller = ref('')
const preferredDates = ref([])
const isApprovedAlert = ref(false)


const viewPendingOrder = (item) => {
  openPendingDialog.value = true;
  clickedItem.value = item;
  extractPreferredDates([item]);
  console.log(clickedItem.value);
  
}

const extractPreferredDates = (pendingOrders) => {
  preferredDates.value = [];
  const pattern = /(?:^|\n)• (\d{2}\/\d{2}\/\d{4}, \d{2}:\d{2} [AP]M)/g;

  pendingOrders.forEach((item) => {
    let match;
    while ((match = pattern.exec(item.remark_buyer)) !== null) {
      preferredDates.value.push(match[1]);
    }
  });

  return preferredDates;
};

const approveOrder = () => {
  //change the date format to yyyy-mm-dd hh:mm:ss
  meet_dateTime.value = new Date(meet_dateTime.value).toISOString().slice(0, 19).replace('T', ' ');
  const data = {
    meet_dateTime: meet_dateTime.value,
    remark_seller: remark_seller.value
  }

  axios.put('/api/order-items/approve-order/' + clickedItem.value.id, data)
    .then(response => {
      openApproveDialog.value = false;
      openPendingDialog.value = false;
      isApprovedAlert.value = true;
      props.getPendingOrders();
    })
    .catch(error => {
      console.log(error)
    })
}



</script>
<template>

  <VTable height="250" fixed-header>
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
          Customer
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
          <VBtn color="primary" @click="viewPendingOrder(item)">View</VBtn>
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
          @click="openPendingDialog = false"
        >
          Reject
        </VBtn>
        <VBtn
          color="success"
          @click="openApproveDialog = true"
        >
          Approve
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>


  <!--Approve Dialog-->
  <VDialog
    v-model="openApproveDialog"
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
          <VCol cols="12">
            <VRow no-gutters>
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Selected Date</label>
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
                  placeholder="Enter Remark"
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
              @click="openApproveDialog = false"
              variant="tonal"
            >
              Close
            </VBtn>
            <VBtn
              type="submit"
              @click="$refs.refForm.validate().then(() => approveOrder())"
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
