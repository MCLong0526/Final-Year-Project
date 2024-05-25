<script setup>
import TableConfirmedOrders from "@/components/Order/OrderItemConfirmedTable.vue";
import TablePendingOrders from "@/components/Order/OrderItemPendingTable.vue";
import ExportSalesItems from "@/pages/export-item.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from "axios";
import { debounce } from 'lodash';
import { watch } from "vue";

const pendingOrders = ref([]);
const confirmedOrders = ref([]);
const currentTab = ref('tab-1');
const searchCustomer = ref('');
const orderStatus = ref([]);
const orderDateRange = ref(null);
const meetDateRange = ref(null);
const passStatus = ref(null);
const passOrderDateRange = ref(null);
const passMeetDateRange = ref(null);
const passSearch = ref(null);
const orderStatusSelect = [
  { name: 'Approved', value: 'Approved' },
  { name: 'Rejected', value: 'Rejected' },
  { name: 'Cancelled', value: 'Cancelled' },
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

// Function to format the date in the format "dd-mm-yyyy"
const formatDate = (date)=> {
    const day = date.getDate().toString().padStart(2, '0');
    const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Months are zero-based
    const year = date.getFullYear();
    return `${day}-${month}-${year}`; // Using "-" instead of "/"
}

const getConfirmedOrders = debounce(() => {
  let requestURL = '/api/order-items/get-confirmed-sell-orders';

  const appendQueryParam = (param, value) => {
    if (!value) return;
    requestURL += requestURL.includes('?') ? `&${param}=${value}` : `?${param}=${value}`;
  };

  appendQueryParam('search', searchCustomer.value && searchCustomer.value.length > 2 ? searchCustomer.value : null);
  appendQueryParam('status', orderStatus.value.value || null);

  const appendDateRangeQueryParam = (param, dateRange) => {
    if (!dateRange) return;
    const formattedStartDate = formatDate(dateRange[0]);
    const formattedEndDate = formatDate(dateRange[1]);
    appendQueryParam(param, `${formattedStartDate} - ${formattedEndDate}`);
  };

  appendDateRangeQueryParam('order_date', orderDateRange.value);
  appendDateRangeQueryParam('meet_date', meetDateRange.value);


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

watch([searchCustomer, orderStatus, orderDateRange, meetDateRange, currentTab], () => {
  getConfirmedOrders();
  passStatus.value = orderStatus.value;
  passOrderDateRange.value = orderDateRange.value;
  passMeetDateRange.value = meetDateRange.value;
  passSearch.value = searchCustomer.value;
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
        This page displays pending and confirmed orders. Click ‘View’ to see order details. <strong>Grey color Order ID indicates orders past their meet_dateTime for Confirmed Orders.</strong>
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
    <VCardTitle class="ml-2 mr-2 mt-1"> Filter Confirmed Orders</VCardTitle>
    <VCardSubtitle class="ml-2 mr-2 mb-2" > Filter confirmed orders by order status, order date, meet date and search for buyer and item.</VCardSubtitle>

    <VRow class="ml-2 mr-2">
      
      <VCol cols="12" md="4">
        <VCombobox
          v-model="orderStatus"
          chips
          return-object
          item-title="name"
          :items="orderStatusSelect"
          prepend-inner-icon="ri-filter-3-line"
          placeholder="Select Order Status"
          clearable
          density="compact"
          @click:clear="orderStatus = []"
        />
        </VCol>
        <VCol cols="12" md="4">
          <VueDatePicker
            teleport-center
            :clearable="true"
            v-model="orderDateRange"
            placeholder="Select Date Range (Order Date)"
            :enable-time-picker="false"
            format="dd/MM/yyyy"
            range
            class="mt-2"
        />
        </VCol>

        <VCol cols="12" md="4">
          <VueDatePicker
            teleport-center
            :clearable="true"
            v-model="meetDateRange"
            placeholder="Select Date Range (Meet Date)"
            :enable-time-picker="false"
            format="dd/MM/yyyy"
            range
            class="mt-2"
        />
        </VCol>        
      
      </VRow>
      <VRow class="ml-2 mr-2">
        <VCol cols="12" md="4">
          <ExportSalesItems
            :passStatus="passStatus"
            :passOrderDateRange="passOrderDateRange"
            :passMeetDateRange="passMeetDateRange"
            :passSearch="passSearch"
            />
          </VCol> 
          <VCol cols="12" md="4" />
          <VCol cols="12" md="4">
            <VTextField
              class="mb-4"
              v-model="searchCustomer"
              label="Search Buyer and Item"
              placeholder="Search for customer and item"
              dense
              density="compact"
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
