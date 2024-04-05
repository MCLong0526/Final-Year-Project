<script setup>
import TableConfirmedOrders from "@/components/Order/OrderItemConfirmedTable.vue";
import TablePendingOrders from "@/components/Order/OrderItemPendingTable.vue";
import axios from "axios";
import { debounce } from 'lodash';
import { watch } from "vue";

const pendingOrders = ref([]);
const confirmedOrders = ref([]);
const currentTab = ref('tab-1');
const searchCustomer = ref('');
const orderStatus = ref([]);
const orderStatusSelect = [
  { name: 'Approved', value: 'Approved' },
  { name: 'Rejected', value: 'Rejected' },
];


const getPendingOrders = () => {
  axios.get('/api/order-items/get-pending-sell-orders')
    .then(response => {
      pendingOrders.value = response.data.data

      // change the order_dateTime format to yyyy-mm-dd hh:mm am/pm, remove the seconds
      pendingOrders.value.forEach((item) => {
        const formatDate = (dateTime) => {
          const date = new Date(dateTime);
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          let hours = date.getHours();
          const minutes = String(date.getMinutes()).padStart(2, '0');
          const ampm = hours >= 12 ? 'pm' : 'am';
          hours = hours % 12 || 12;
          return `${year}-${month}-${day} ${hours}:${minutes} ${ampm}`;
        };

        item.order_dateTime = formatDate(item.order_dateTime);
        item.meet_dateTime = formatDate(item.meet_dateTime);
      });



      // change the item picture path to http://127.0.0.1:8000/storage/
      pendingOrders.value.forEach((item) => {
        item.item.pictures.forEach((picture) => {
          picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path
        })
      })

    })
    .catch(error => {
      console.log(error)
    })
}

const getConfirmedOrders = debounce(() => {
  let requestURL = '/api/order-items/get-confirmed-sell-orders';
  if (searchCustomer.value && searchCustomer.value.length > 2) {
    requestURL += `?search=${searchCustomer.value}`;
  }
  if (orderStatus.value.value) {
    requestURL += `?&status=${orderStatus.value.value}`;
  }
  axios.get(requestURL)
    .then(({data}) => {
      confirmedOrders.value = data.data

      // change the order_dateTime format to yyyy-mm-dd hh:mm am/pm, remove the seconds
      confirmedOrders.value.forEach((item) => {
        const formatDate = (dateTime) => {
          const date = new Date(dateTime);
          const year = date.getFullYear();
          const month = String(date.getMonth() + 1).padStart(2, '0');
          const day = String(date.getDate()).padStart(2, '0');
          let hours = date.getHours();
          const minutes = String(date.getMinutes()).padStart(2, '0');
          const ampm = hours >= 12 ? 'pm' : 'am';
          hours = hours % 12 || 12;
          return `${year}-${month}-${day} ${hours}:${minutes} ${ampm}`;
        };

        item.order_dateTime = formatDate(item.order_dateTime);
        item.meet_dateTime = formatDate(item.meet_dateTime);
      });



      // change the item picture path to http://127.0.0.1:8000/storage/
      confirmedOrders.value.forEach((item) => {
        item.item.pictures.forEach((picture) => {
          picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path
        })
      })

    })
    .catch(error => {
      console.log(error)
    })
}, 800);

watch([searchCustomer, orderStatus, currentTab], () => {
  getConfirmedOrders();
});

getPendingOrders()
getConfirmedOrders()
</script>
<template>
  <div class="box-style">

  <VAlert
      variant="tonal"
      color="info"
    >
      <template #title >
        <VIcon
          icon="ri-information-line"
          class="mr-2"
        />
        <span >Order of Selling Items</span>
      </template>
      <p>
        This page shows the list of orders that are pending and confirmed. You can view the details of each order by clicking the "View" button.
      </p>
    </VAlert>
  <VTabs
    v-model="currentTab"
    grow
    stacked
  >
    <VTab value="tab-1">
      <VIcon
        icon="ri-loader-line"
        class="mb-2"
      />
      <span>Pending Order</span>
    </VTab>

    <VTab value="tab-2">
      <VIcon
        icon="ri-takeaway-line"
        class="mb-2"
      />
      <span>Confirmed Order</span>
    </VTab>
  </VTabs>

  <VWindow
    v-model="currentTab"
    class="mt-5"
  >
    <VWindowItem
      value="tab-1"
    >
    
      <div class="table-style">
        <TablePendingOrders
          :pendingOrders="pendingOrders"
          :getPendingOrders="getPendingOrders"
        />
      </div>
    </VWindowItem>

    <VWindowItem
      value="tab-2"
    >
    <VRow class="ml-2 mr-2">
      <VCol cols="12" md="3">
        <VCombobox
          v-model="orderStatus"
          class="mb-4 mt-2"
          chips
          return-object
          item-title="name"
          :items="orderStatusSelect"
          prepend-inner-icon="ri-filter-3-line"
          placeholder="Select Order Status"
          clearable
        @click:clear="orderStatus = []"
        />
        </VCol>
        <VCol cols="12" md="3" />

        <VCol cols="12" md="3" />
        
      <VCol cols="12" md="3">
        <VTextField
          class="mb-4 mt-2"
          v-model="searchCustomer"
          label="Search"
          placeholder="Search for customer and item"
          dense
          clearable
        />
        </VCol>
        
      
      </VRow>
      <div class="table-style">
        <TableConfirmedOrders
          :confirmedOrders="confirmedOrders"
          :getConfirmedOrders="getConfirmedOrders"
        />
      </div>
    </VWindowItem>
  </VWindow>
  </div>
  
</template>

<style scoped>
.table-style{
  padding: 0.5px; /* Padding around the table */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
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
