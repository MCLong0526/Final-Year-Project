<script setup>
import { ref } from 'vue';
import UserProfileDialog from '../Profile/UserProfileDialog.vue';


const props = defineProps({
  confirmedOrders: {
    type: Object,
    required: true
  },
  getConfirmedOrders: {
    type: Function,
    required: true
  }

})

const clickedService = ref({})
const openApprovedDialog = ref(false)
const isApprovedAlert = ref(false)
const isContactDialog = ref(false)
const clickedContactUser = ref({})


const viewApprovedOrder = (service) => {
  openApprovedDialog.value = true;
  clickedService.value = service;
}


const openContactDialog = (user) => {
  clickedContactUser.value = user;
  isContactDialog.value = true;
}

// Check if service_dateTime is over today
const isServiceDateTimeOverToday = (serviceDateTime) => {
  const now = new Date();
  if (!serviceDateTime) return true;
  const serviceDate = new Date(serviceDateTime.split(' ')[0]);

  // Not including today date
  if (serviceDate.getFullYear() === now.getFullYear() &&
      serviceDate.getMonth() === now.getMonth() &&
      serviceDate.getDate() === now.getDate()) return false;

  return serviceDate < now;
}


</script>
<template>
  <VTable v-if="confirmedOrders.length>0" height="250" fixed-header>
    
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
          <VIcon icon="ri-calendar-schedule-line" />
          Service Date Time
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
      <tr v-for="service in confirmedOrders" :key="service.id">
        <td class="text-center">
          <VChip 
            v-if="isServiceDateTimeOverToday(service.service_dateTime) == true" 
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
            @click="viewApprovedOrder(service)"
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
          Customer
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-calendar-schedule-line" />
          Order Date
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-calendar-schedule-line" />
          Meet Date
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



  <!--Approved Service Dialog-->
  <VDialog
    v-model="openApprovedDialog"
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
            <span>: {{ clickedService.user.username }}</span>
          </div>
          <div class="detail-row">
            <strong>Status</strong>
            <span v-if="clickedService.status=='Approved'">: <VChip color="success" size="small"><VIcon icon="ri-check-fill" class="mr-1"/>{{ clickedService.status }}</VChip></span>
            <span v-else>: <VChip color="error" size="small"><VIcon icon="ri-close-line" class="mr-1"/>{{ clickedService.status }}</VChip></span>
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

        </VCardText>
          
      <VCardText class="pt-5 mt-4">
        <VRow>
          <VCol cols="12" md="4">
            <VBtn
              color="secondary"
              @click="openApprovedDialog = false"
            >
              <VIcon icon="ri-close-line" class="mr-1"/>
              Close
            </VBtn>
          </VCol>
          <VCol cols="12" md="4" />
          <VCol cols="12" md="4">
            <VBtn color="success" 
              v-if="clickedService.status=='Approved'"
              @click="openContactDialog(clickedService.user)"
              
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
</style>
