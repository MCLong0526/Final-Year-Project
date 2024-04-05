<script setup>
// get user data from store
import { useAuthStore } from '@/plugins/store/AuthStore'; // Adjust the path based on your project structure
const store = useAuthStore();
const props = defineProps({
  confirmedPurchasesOrders: {
    type: Object,
    required: true
  },
})

const clickedService = ref({})
const openConfirmedDialog = ref(false)

const viewOrder = (service) => {
  openConfirmedDialog.value = true;
  clickedService.value = service;
}

const openWhatsApp = (clickedUser) => {

const message = `Hello! I am ${store.user.username} from UNIMAS Web App. I would like to discuss the order here. Thank you.`;
const apilink = `https://web.whatsapp.com/send?phone=+60${clickedUser.phone_number}&text=${encodeURIComponent(message)}`;

// Open the WhatsApp web link
window.open(apilink, '_blank');
};

</script>

<template>
  <VTable v-if="confirmedPurchasesOrders.length>0" height="250" fixed-header>
    
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
      <tr v-for="service in confirmedPurchasesOrders" :key="service.id">
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
    max-width="500"
  >

    <!-- Dialog Content -->
    <VCard>
      <VCardTitle class="mt-2 mb-1">Order Details</VCardTitle>
      <VDivider class="mb-2"/>
        <VCardText>
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
          <div class="detail-row">
            <strong>Customer Remark</strong>
            <span>: {{ clickedService.remark_customer }}</span>
          </div>
          <div class="detail-row">
            <strong>Provider Remark</strong>
            <span>: {{ clickedService.remark_provider }}</span>
          </div>

        </VCardText>
          
      <VCardText class="pt-5">
        <VRow>
          <VCol cols="12" md="3">
            <VBtn
              color="secondary"
              @click="openConfirmedDialog = false"
            >
              <VIcon icon="ri-close-line" class="mr-1"/>
              Close
            </VBtn>
          </VCol>
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3">
            <VBtn color="success" 
              v-if="clickedService.status == 'Approved'"
              @click="openWhatsApp(clickedService.service.user)"
            >
              <VIcon icon="ri-whatsapp-line" class="mr-1"/>
              <VTooltip
                location="top"
                activator="parent"
                transition="scroll-x-transition"
              >
                Contact Provider
              </VTooltip>
              Contact
            </VBtn>
          </VCol>
            
          </VRow>
        
      </VCardText>
    </VCard>
  </VDialog>
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
