<script setup>
import axios from 'axios';

const props = defineProps({
  pendingPurchasesOrders: {
    type: Object,
    required: true
  },

})

const cancelOrderDialog = ref(false)
const clickedPendingOrder = ref(null)
const isAddAlert = ref(false)
const hasErrorAlert = ref(false)
const errorMessages = ref('')

const cancelOrder = (order) => {
  cancelOrderDialog.value = true
  clickedPendingOrder.value = order
}

const cancelPendingOrder = () => {
  axios.put(`/api/order-items/cancel-pending-order/${clickedPendingOrder.value.id}`)
    .then(response => {
      cancelOrderDialog.value = false
      isAddAlert.value = true

      // watch 1.5 second then window reload
      setTimeout(() => {
        window.location.reload()
      }, 1500)


    })
    .catch(error => {
      errorMessages.value = error.response.data.message
      hasErrorAlert.value = true
      console.log(error)
    })
}
</script>

<template>
  <VTable v-if="pendingPurchasesOrders.length > 0" height="250" fixed-header>
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
          <VIcon icon="ri-verified-badge-line" />
          Status
        </th>
        <th class="text-uppercase text-center">
          <VIcon icon="ri-close-circle-line" />
          Cancel
        </th>
      </tr>
    </thead>

    <tbody>
      <tr v-for="item in pendingPurchasesOrders" :key="item.id">
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
            @click="cancelOrder(item)"
          >
            <VIcon
              icon="ri-close-circle-line"
            />
            <VTooltip
                open-delay="500"
                location="top"
                activator="parent"
                transition="scroll-y-transition"
              >
                <span>Cancel Order</span>
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

    <!--Delete Dialog-->
  <VDialog
    v-model="cancelOrderDialog"
    persistent
    class="v-dialog-sm"
  >
    <!-- Dialog Content -->
    <VCard title="Cancel Order?">    
      <VCardText>
        <VAlert
          color="error"
          icon="ri-alert-line"
          variant="tonal"
        >
          Are you sure you want to cancel the order for the item "<strong>{{ clickedPendingOrder.item.name }}</strong>"
        </VAlert>
      </VCardText>

      <VCardActions>
        <VSpacer />
        <VBtn
          color="secondary"
          @click="cancelOrderDialog = false"
        >
          Close
        </VBtn>
        <VBtn
          color="error"
          @click="cancelPendingOrder"
        >
          Cancel Order
        
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <!--Snackbar-->
  <VSnackbar
      v-model="isAddAlert"
      location="top end"
      transition="scale-transition"
      color="success"
    >
    <VIcon size="20" class="me-2">ri-checkbox-circle-line</VIcon>
      Order has been successfully cancelled.
    </VSnackbar>

    <!--Snackbar-->
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
