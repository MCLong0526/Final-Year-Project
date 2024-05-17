<script setup>
import UserProfileDialog from '../Profile/UserProfileDialog.vue';
const props = defineProps({
  confirmedPurchasesOrders: {
    type: Object,
    required: true
  },

})

const clickedItem = ref({})
const openConfirmedDialog = ref(false)
const isContactDialog = ref(false)
const clickedContactUser = ref({})

// View the order details
const viewOrder = (item) => {
  openConfirmedDialog.value = true;
  clickedItem.value = item;
}

// Open the contact dialog
const openContactDialog = (user) => {
  clickedContactUser.value = user;
  isContactDialog.value = true;
}



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
          Product
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-id-card-line" />
         Seller
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
      <tr v-for="item in confirmedPurchasesOrders" :key="item.id">
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
              v-if="item.item.user.avatar"
              size="32"
              :color="item.item.user.avatar ? '' : 'primary'"
              class="mr-1"
            >
              <VImg :src="item.item.user.avatar" />
            </VAvatar>
            <div>
              <div>{{ item.item.user.username }}</div>
              <div style="color: #6c757d; font-size: 12px;">{{ item.item.user.email }}</div>
            </div>
          </div>
        </td>     
        <td class="text-center">{{ item.order_dateTime }}</td>
        <td class="text-center" >
          <VChip v-if="item.status=='Approved'" color="warning" size="x-small">{{ item.meet_dateTime }}</VChip>
          <VChip v-else color="warning" size="x-small">Not Set</VChip>
        </td>
        <td class="text-center">
          <div class="status-dot" :class="{ 'status-dot-read': item.status === 'Rejected', 'status-dot-read2': item.status === 'Cancelled' }">
            <VChip v-if="item.status == 'Approved'" color="success" size="x-small" >{{ item.status }}</VChip>
            <VChip v-if="item.status=='Rejected'" color="error" size="x-small">{{ item.status }}</VChip>
            <VChip v-if="item.status=='Cancelled'" color="secondary" size="x-small">{{ item.status }}</VChip>
          
          </div>
        </td>

        <td class="text-center">
          <VBtn
            color="primary"
            style="margin-inline: 15px 3px"
            size="small"
            @click="viewOrder(item)"
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
          Product
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-id-card-line" />
         Seller
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
        <VCardText style="max-block-size: 400px; overflow-y: auto;">
          <div class="detail-row">
            <strong>Order ID</strong>
            <span>: #{{ clickedItem.id }}</span>
          </div>
          <div class="detail-row">
            <strong>Order Date Time</strong>
            <span>: {{ clickedItem.order_dateTime }}</span>
          </div>
          <div class="detail-row">
            <strong>Seller</strong>
            <span>: {{ clickedItem.item.user.username }}</span>
          </div>
          <div class="detail-row">
            <strong>Status</strong>
            <span v-if="clickedItem.status=='Approved'">: <VChip color="success" size="small"><VIcon icon="ri-check-fill" class="mr-1"/>{{ clickedItem.status }}</VChip></span>
            <span v-if="clickedItem.status=='Rejected'">: <VChip color="error" size="small"><VIcon icon="ri-close-line" class="mr-1"/>{{ clickedItem.status }}</VChip></span>
            <span v-if="clickedItem.status=='Cancelled'">: <VChip color="secondary" size="small"><VIcon icon="ri-close-circle-line" class="mr-1"/>{{ clickedItem.status }}</VChip></span>
          </div>
          <VDivider class="mt-2 mb-2"/>
          <div class="detail-row">
            <strong>Item</strong>
            <span>: {{ clickedItem.item.name }}</span>
          </div>
          <div class="detail-row" >
            <strong>Meet Date Time</strong>
            <span v-if="clickedItem.status=='Approved'">: <VChip color="warning" size="small"><VIcon icon="ri-check-double-fill" class="mr-1"/>{{ clickedItem.meet_dateTime }}</VChip></span>
            <span v-else>: <VChip color="warning" size="small">Not Set</VChip></span>
          </div>
          <div class="detail-row" style="display: flex; align-items: flex-start;">
            <strong>Place to meet</strong>
            <span style="white-space: pre-line;">: {{ clickedItem.place_to_meet }}</span>
          </div>
          <VDivider class="mt-2 mb-2"/>
          <div class="detail-row" style="display: flex; align-items: flex-start;">
            <strong>Buyer Remark</strong>
            <span style="white-space: pre-line;">: {{ clickedItem.remark_buyer }}</span>
          </div>
          <div class="detail-row" style="display: flex; align-items: flex-start;"> 
            <strong>Seller Remark</strong>
            <span style="white-space: pre-line;">: {{ clickedItem.remark_seller }}</span>
          </div>

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
              v-if="clickedItem.status=='Approved'"
              @click="openContactDialog(clickedItem.item.user)"
              
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
