<script setup>
import axios from 'axios';
import UserProfileDialog from '../Profile/UserProfileDialog.vue';
const props = defineProps({
  confirmedPurchasesOrders: {
    type: Object,
    required: true
  },
  getPurchasesOrder: {
    type: Function,
    required: true
  }
})

const emits = defineEmits(['update:confirmedPurchasesOrders'])

const clickedService = ref({})
const openConfirmedDialog = ref(false)
const isContactDialog = ref(false)
const clickedContactUser = ref({})
const successRating = ref(false)
const rating = ref(0)


// View the order details
const viewOrder = (service) => {
  openConfirmedDialog.value = true;
  clickedService.value = service;
}

// Open the contact dialog
const openContactDialog = (user) => {
  clickedContactUser.value = user;
  isContactDialog.value = true;
}

// Check if service_dateTime is over today, format is "2024-11-04 07:00AM-08:00AM"
const isServiceDateTimeOverToday = (serviceDateTime) => {
  if (serviceDateTime === null){
    return true;
  }
  const now = new Date();
  const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // Clear time part
  const serviceDate = new Date(serviceDateTime.split(' ')[0]); // Only parse the date part

  return serviceDate < today;
}

// Check if the service_dateTime is past
const isPastService = (serviceDateTime) => {
  if (serviceDateTime === null) return false;
  const now = new Date();
  const serviceDate = new Date(serviceDateTime.split(' ')[0]);

  return serviceDate < now;
}

const giveRating = (service) => {
  axios.post('/api/order-services/rate-provider', {
    order_id: service.id,
    rating: rating.value
  })
    .then(response => {
      console.log('Rating submitted')
      rating.value = 0

      successRating.value = true
      openConfirmedDialog.value = false
      
      props.getPurchasesOrder()


    })
    .catch(error => {
      console.log(error)
    })
}

</script>

<template>
  <VTable v-if="confirmedPurchasesOrders.length>0" height="270" fixed-header>
    
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
         Provider
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-calendar-schedule-line" />
          Order Date
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-calendar-schedule-line" />
          Service Date
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
      <tr v-for="service in confirmedPurchasesOrders" :key="service.id" >
        <td class="text-center">
          <VChip 
            v-if="isServiceDateTimeOverToday(service.service_dateTime) === true" 
            color="secondary"
          >
            {{ service.id }}
          </VChip>
          <VChip 
            v-else
            color="primary"
          >
            {{ service.id }}
          </VChip>
        </td>
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
              v-if="service.service.user.avatar"
              size="32"
              :color="service.service.user.avatar ? '' : 'primary'"
              class="mr-1"
            >
              <VImg :src="service.service.user.avatar" />
            </VAvatar>
            <div>
              <div>{{ service.service.user.username }}</div>
              <div style="color: #6c757d; font-size: 12px;">{{ service.service.user.email }}</div>
            </div>
          </div>
        </td>     
        <td class="text-center">{{ service.order_dateTime }}</td>
        <td class="text-center" >
          <VChip v-if="service.status=='Approved'" color="warning" size="x-small">{{ service.service_dateTime }}</VChip>
          <VChip v-else color="warning" size="x-small">Not Set</VChip>
        </td>
        <td class="text-center">
          <div class="status-dot" :class="{ 'status-dot-read': service.status === 'Rejected', 'status-dot-read2': service.status === 'Cancelled' }">
            <VChip v-if="service.status == 'Approved'" color="success" size="x-small" >{{ service.status }}</VChip>
            <VChip v-if="service.status=='Rejected'" color="error" size="x-small">{{ service.status }}</VChip>
            <VChip v-if="service.status=='Cancelled'" color="secondary" size="x-small">{{ service.status }}</VChip>
          
          </div>
        </td>

        <td class="text-center">
          <VBtn
            color="primary"
            style="margin-inline: 15px 3px"
            size="small"
            @click="viewOrder(service)"
          >
            <VIcon
              icon="ri-list-view"
            />
            <VTooltip
                open-delay="500"
                location="top"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>View Details</span>
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
         Provider
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-calendar-schedule-line" />
          Order Date
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-calendar-schedule-line" />
          Service Date
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
        <td colspan="8" class="text-center">
          <VAlert  
            variant="tonal"
            type="warning"
            class="mt-2"
            color="primary"
            closable
            dense
          >
            No confirmed orders found.
          </VAlert>
        </td>
      </tr>
    </tbody>
    
    </VTable>


  <!-- Confirmed Order for purchases -->
  <VDialog
    v-model="openConfirmedDialog"
    scrollable
    max-width="550"
  >

    <!-- Dialog Content -->
    <VCard>
      <VCardTitle class="mt-2 mb-1">Order Details</VCardTitle>
      <VDivider class="mb-2"/>
        <VCardText style="max-block-size: 400px; overflow-y: auto;">
          <div class="detail-row">
            <strong>Order ID</strong>
            <span>: #{{ clickedService.id }}</span>
          </div>
          <div class="detail-row">
            <strong>Order Date Time</strong>
            <span>: {{ clickedService.order_dateTime }}</span>
          </div>
          <div class="detail-row">
            <strong>Customer</strong>
            <span>: {{ clickedService.service.user.username }}</span>
          </div>
          <div class="detail-row">
            <strong>Status</strong>
            <span v-if="clickedService.status=='Approved'">: <VChip color="success" size="small"><VIcon icon="ri-check-fill" class="mr-1"/>{{ clickedService.status }}</VChip></span>
            <span v-else>: <VChip color="error" size="small"><VIcon icon="ri-close-line" class="mr-1"/>{{ clickedService.status }}</VChip></span>
          </div>
          <VDivider class="mt-2 mb-4"/>

          <!-- Rating Part -->
          <div v-if="clickedService.status=='Approved' ">
          <div class="box-style" v-if="isPastService(clickedService.service_dateTime) && clickedService.rating===null">
          <VCard elevation="2" >

              <div class="text-center">
                <h4 class="headline mr-1 mt-4">
                  <VIcon icon="ri-user-star-linee" />
                  Rate The Provider
                  
                </h4>
                <VDivider class="mt-2 mb-2" />
                <VRating v-model="rating" half-increments hover color="secondary" size="48" />
              </div>
    
            <VCardActions class="justify-center">
              
              <VBtn color="success" @click="giveRating(clickedService)">Submit</VBtn>
            </VCardActions>
          </VCard>
          </div>
          <div class="box-style" v-else-if="isPastService(clickedService.service_dateTime) && clickedService.rating!==null">
            <VCard elevation="2" >
              <div class="text-center">
                <h4 class="headline mr-1 mt-4">
                  <VIcon icon="ri-user-star-line"/>
                  Rating Given For Provider
                </h4>
                <VDivider class="mt-2 mb-2" />
                <VRating v-model="clickedService.rating" half-increments hover color="secondary" size="48" :readonly="true" />
              </div>
            </VCard>
          </div>
          <div class="box-style" v-else style="cursor:not-allowed">
            <VCard elevation="2" >
              <div class="text-center">
                <h4 class="headline mr-1 mt-4">
                  <VIcon icon="ri-user-star-line" />
                  Rating Not Available Yet
                </h4>
                <VDivider class="mt-2 mb-2" />
                <VRating v-model="clickedService.rating" half-increments hover color="secondary" size="48" :readonly="true" />
              </div>
            </VCard>
          </div>
        </div>
        
        <!--Rejected Cannot Rating-->
          <div class="box-style" v-else style="cursor:not-allowed">
            <VCard elevation="2" >
              <div class="text-center">
                <h4 class="headline mr-1 mt-4">
                  <VIcon icon="ri-user-star-line" />
                  Rejected/Cancelled Order Cannot Be Rated
                </h4>
                <VDivider class="mt-2 mb-2" />
                <VRating v-model="clickedService.rating" half-increments hover color="secondary" size="48" :readonly="true" />
              </div>
            </VCard>
          </div>

          <VDivider class="mt-2 mb-2"/>
          <div class="detail-row">
            <strong>Service Name</strong>
            <span>: {{ clickedService.service.name }}</span>
          </div>
          <div class="detail-row" >
            <strong>Service Date Time</strong>
            <span v-if="clickedService.status=='Approved'">: <VChip color="warning" size="small"><VIcon icon="ri-check-double-fill" class="mr-1"/>{{ clickedService.service_dateTime }}</VChip></span>
            <span v-else>: <VChip color="warning" size="small"><VIcon icon="ri-close-line" class="mr-1"/>Not Set</VChip></span>
          </div>
          <div class="detail-row" >
            <strong>Service Duration</strong>
            <span v-if="clickedService.status=='Approved'">: <VChip color="warning" size="small"><VIcon icon="ri-check-double-fill" class="mr-1"/>{{ clickedService.duration }}</VChip></span>
            <span v-else>: <VChip color="warning" size="small"><VIcon icon="ri-close-line" class="mr-1"/>Not Set</VChip></span>
          </div>
          <div class="detail-row" >
            <strong>Approximated Price</strong>
            <span v-if="clickedService.status=='Approved'">: <VChip color="warning" size="small"><VIcon icon="ri-check-double-fill" class="mr-1"/> ~ RM{{ clickedService.approximated_price }}</VChip></span>
            <span v-else>: <VChip color="warning" size="small"><VIcon icon="ri-close-line" class="mr-1"/>Not Set</VChip></span>
          </div>
          <div class="detail-row">
            <strong>Place to service</strong>
            <span>: {{ clickedService.place_to_service }}</span>
          </div>
          <VDivider class="mt-2 mb-2"/>
          <div class="detail-row" style="display: flex; align-items: flex-start;">
            <strong>Customer Remark</strong>
            <span style="white-space: pre-line;">: {{ clickedService.remark_customer }}</span>
          </div>
          <div class="detail-row" style="display: flex; align-items: flex-start;">
            <strong>Provider Remark</strong>
            <span style="white-space: pre-line;">: {{ clickedService.remark_provider }}</span>
          </div>
          <VDivider class="mt-2 mb-4"/>

        </VCardText>
          
      <VCardText class="pt-5 mt-4">
        <VRow>
          <VCol cols="12" md="4">
            <VBtn
              color="secondary"
              @click="openConfirmedDialog = false"
            >
              <VIcon icon="ri-close-line" class="mr-1"/>
              Close
            </VBtn>
          </VCol>
          <VCol cols="12" md="4" />
          <VCol cols="12" md="4">
            <VBtn color="success" 
              v-if="clickedService.status=='Approved'"
              @click="openContactDialog(clickedService.service.user)"
              
            >
              <VIcon icon="ri-whatsapp-line" class="mr-1"/>
              <VTooltip
                location="top"
                activator="parent"
                transition="scroll-x-transition"
              >
                Contact Buyer
              </VTooltip>
              Contact
            </VBtn>
          </VCol>
            
          </VRow>
        
      </VCardText>
    </VCard>
  </VDialog>

  <VDialog
    v-model="isContactDialog"
    scrollable
    max-width="500"
  >
    <UserProfileDialog
      :clickedUser="clickedContactUser"
    />
  </VDialog>

  <!--Snackbar-->
   <VSnackbar
      v-model="successRating"
      location="top end"
      transition="scale-transition"
      color="success"
    >
      <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      <span>Rating submitted successfully</span>
    </VSnackbar>
</template>

<style scoped>
.status-dot::before {
  color: #6fff00;
  content: '\2022'; /* Unicode character for a bullet point */
  font-size: 20px; /* Size of the dot */
  margin-inline-end: 6px; /* Spacing between the dot and the text */
}

.status-dot-read::before {
  color: #f00; /* Grey color for read notifications */
}

.status-dot-read2::before {
  color: #565656; /* Grey color for read notifications */
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

.box-style {
  padding: 1.5px; /* Padding around the table */
  border: 0.4px solid #282828;
  border-radius:10px;
  background-color: #fff; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
  margin-block-end: 15px
}

</style>
