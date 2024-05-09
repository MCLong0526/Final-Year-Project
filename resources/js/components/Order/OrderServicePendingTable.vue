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

const emit = defineEmits(['update:pendingOrders'])
const clickedService = ref({})
const openPendingDialog = ref(false)
const openDecisionDialog = ref(false)
const service_dateTime = ref('')
const remark_provider = ref('')
const decision= ref('')
const isApprovedAlert = ref(false)
const isRejectedAlert = ref(false)

const viewPendingOrder = (service) => {
  openPendingDialog.value = true;
  clickedService.value = service;
  extractServiceDateTime([service]);
  console.log(clickedService.value);
  
}

const openApproveDialog = () => {
  decision.value = 'approve';
  openDecisionDialog.value = true;
}

const openRejectDialog = () => {
  decision.value = 'reject';
  openDecisionDialog.value = true;
}

// change the order_dateTime format to yyyy-mm-dd hh:mm am/pm, remove the seconds
const extractServiceDateTime = (pendingOrders) => {
  // set the service_dateTime from the remark_customer
  // the format of the remark_customer is "Preferable Service Date and Time: • 04/11/2024, 7:48 AM-10:48 AM\nwwwwweeee"
  // get the 04/11/2024, 7:48 AM-10:48 AM only
  const remark = pendingOrders[0].remark_customer;
  const dateTime = remark.split('• ')[1].split(',')[0].trim();
  const startTime = remark.split('• ')[1].split(',')[1].split('-')[0].trim();
  const endTime = remark.split('• ')[1].split(',')[1].split('-')[1].trim().split('\n')[0];
  service_dateTime.value = `${dateTime}, ${startTime}-${endTime}`;

};

// convert 2024/04/10, 7:32 AM-8:32 AM to yyyy-mm-dd hh:mm:ss-hh:mm:ss
const convertDateTime = (dateTime) => {
  const date = dateTime.split(',')[0].trim();
  const startTime = dateTime.split(',')[1].split('-')[0].trim();
  const endTime = dateTime.split(',')[1].split('-')[1].trim();
  const year = date.split('/')[0];
  const month = date.split('/')[1];
  const day = date.split('/')[2];
  const startHour = startTime.split(' ')[1] === 'AM' ? parseInt(startTime.split(':')[0]) : parseInt(startTime.split(':')[0]) + 12;
  const startMinute = parseInt(startTime.split(':')[1]);
  const endHour = endTime.split(' ')[1] === 'AM' ? parseInt(endTime.split(':')[0]) : parseInt(endTime.split(':')[0]) + 12;
  const endMinute = parseInt(endTime.split(':')[1]);

  return `${year}-${month}-${day} ${String(startHour).padStart(2, '0')}:${String(startMinute).padStart(2, '0')}-${String(endHour).padStart(2, '0')}:${String(endMinute).padStart(2, '0')}`;
}

//confirm order
const confirmedOrder = () => {

  if(decision.value === 'approve'){
    service_dateTime.value = convertDateTime(service_dateTime.value);
  }else{
    service_dateTime.value = null;
    if(remark_provider.value === ''){
      return
    }
  }

  const data = {
    service_dateTime: service_dateTime.value,
    remark_provider: remark_provider.value
  }

  axios.put('/api/order-services/confirmed-order/' + clickedService.value.id, data)
    .then(response => {
      openDecisionDialog.value = false;
      openPendingDialog.value = false;
      service_dateTime.value = null;
      remark_provider.value = '';
      if(decision.value === 'approve') {
        isApprovedAlert.value = true;
      } else {
        isRejectedAlert.value = true;
      }
      decision.value = '';

      // refresh the pending orders
      props.getPendingOrders();
      
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
          Service
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-id-card-line" />
          Customer
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-hourglass-fill" />
          Service Duration
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
      <tr v-for="service in pendingOrders" :key="service.id">
        <td class="text-center">#{{ service.id }}</td>
        <td>
          <div class="d-flex align-items-center">
            <VAvatar
              v-if="service.service.pictures.length > 0 && service.service.pictures"
              size="32"
              rounded="sm"
              style="margin-inline-end: 8px;"
            >
              <VImg :src="service.service.pictures[0].picture_path" />
            </VAvatar>
            <div>
              <div>{{ service.service.name }}</div>
              <div style="color: #6c757d; font-size: 12px;">{{ service.service.type }}</div>
            </div>
          </div>
        </td>
        <td>
          <div class="d-flex align-items-center">
            <VAvatar
              v-if="service.user.avatar"
              size="32"
              :color="service.user.avatar ? '' : 'primary'"
              class="mr-1"
            >
              <VImg :src="service.user.avatar" />
            </VAvatar>
            <div>
              <div>{{ service.user.username }}</div>
              <div style="color: #6c757d; font-size: 12px;">{{ service.user.email }}</div>
            </div>
          </div>
        </td>
        <td class="text-center">{{ service.duration }}</td>
        <td class="text-center">{{ service.order_dateTime }}</td>
        <td class="text-center">
          <div class="status-dot">
            <VChip color="warning">{{ service.status }}</VChip>
          </div>
        </td>



        <td class="text-center">
          <VBtn
            color="warning"
            style="margin-inline: 15px 3px"
            size="small"
            @click="viewPendingOrder(service)"
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
          Service
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
            <span>: {{ clickedService.order_dateTime }}</span>
          </div>
          <div class="detail-row">
            <strong>Place to service</strong>
            <span>: {{ clickedService.place_to_service }}</span>
          </div>
          <div class="detail-row" style="display: flex; align-items: flex-start;">
            <strong>Remark customer</strong>
            <span style="white-space: pre-line;">: {{ clickedService.remark_customer }}</span>
          </div>
          <div class="detail-row">
            <strong>Service duration</strong>
            <span>: <VChip
                  color="primary"
                  class="mr-2"
                  size="small"
                >
                  <VIcon icon="ri-time-fill" class="mr-2"/>
                  {{ clickedService.duration }}
                </VChip></span>
          </div>
          <div class="detail-row">
            <strong>Approximated earn</strong>
            <span>: <VChip
                  color="primary"
                  class="mr-2"
                  size="small"
                >
                  <VIcon icon="ri-money-dollar-circle-line" class="mr-2"/>
                  ~ RM {{ clickedService.approximated_price }}
                </VChip></span>
          </div>

          
          <h3 class="mt-6">Customer Information</h3>
        

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
  
          <h3>Service Information</h3>
          <VCarousel delimiter-icon="ri-circle-fill" height="250px" hide-delimiter-background show-arrows="hover">
            <VCarouselItem v-for="(picture, index) in clickedService.service.pictures" :key="index" >
              <VImg :src="picture.picture_path" height="250px" object-fit="cover" class="mt-2 ml-2 mr-2" />
            </VCarouselItem>
          </VCarousel>
        
          <VCardTitle>
            <VIcon icon="ri-shopping-bag-4-line" />
              {{ clickedService.service.name }}
          </VCardTitle>
          <VCardTitle>
          <VIcon icon="ri-money-dollar-circle-line" />

          RM {{clickedService.service.price_per_hour}} /hour</VCardTitle>

        </VCardText>

        

      <VCardText class="pt-5 d-flex mt-4 justify-content-center" style="margin-inline-start:300px">
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
            <VRow no-gutters style="cursor: not-allowed;">
              <VCol
                cols="12"
                md="3"
              >
                <label for="firstNameHorizontalIcons">Service Date and Time</label>
              </VCol>

              <VCol
                cols="12"
                md="9"
              >
              <VTextField 
                v-model="service_dateTime"
                prepend-inner-icon="ri-calendar-event-line"
                disabled
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
                  v-if="decision === 'reject'"
                  v-model="remark_provider"
                  prepend-inner-icon="ri-chat-4-line"
                  placeholder="Enter remark for customer (Reason for Rejection)"
                  :rules="[requiredValidator]"
                />
                <VTextarea
                  v-else
                  v-model="remark_provider"
                  prepend-inner-icon="ri-chat-4-line"
                  placeholder="Enter remark for customer (optional)"
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
  inline-size: 150px; /* Adjust the width as needed */
}

.detail-row span {
  flex-grow: 1; /* Allow growing to fill the remaining space */
}
</style>
