<script setup>
import TableConfirmedOrders from "@/components/Order/OrderServiceConfirmedTable.vue";
import TablePendingOrders from "@/components/Order/OrderServicePendingTable.vue";
import ExportSalesServices from "@/pages/export-service.vue";
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import axios from "axios";
import { debounce } from 'lodash';

const currentTab = ref('tab-1');
const pendingOrders = ref([]);
const confirmedOrders = ref([]);
const search = ref('');
const orderStatus = ref([]);
const orderDateRange = ref(null);
const serviceDateRange = ref(null);
const passStatus = ref(null);
const passOrderDateRange = ref(null);
const passServiceDateRange = ref(null);
const passSearch = ref(null);

const orderStatusSelect = [
  { name: 'Approved', value: 'Approved' },
  { name: 'Rejected', value: 'Rejected' },
  { name: 'Cancelled', value: 'Cancelled' },
];

const getPendingOrders = () => {
  axios.get('/api/order-services/get-pending-sell-orders')
    .then(response => {
      pendingOrders.value = response.data.data

      // change the order_dateTime format to yyyy-mm-dd hh:mm am/pm, remove the seconds
      pendingOrders.value.forEach((service) => {
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

        service.order_dateTime = formatDate(service.order_dateTime);
        service.service_dateTime = formatDate(service.service_dateTime);
      });

      // change the service picture path to http://127.0.0.1:8000/storage/
      pendingOrders.value.forEach((service) => {
        service.service.pictures.forEach((picture) => {
          picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path
        })
      })

      // calculate the duration of the pendingOrders by referring the start and end time in remark_customer,
      // the remark_customer format is "Preferable Service Date and Time: • 04/11/2024, 7:48 AM-10:48 AM\nwwwwweeee"
      // the duration format is hours:minutes
      pendingOrders.value.forEach((service) => {
        const duration = (start, end) => {
          // change the am/pm to 24 hours format before calculating the duration
          const startHour = start.split(' ')[1] === 'AM' ? parseInt(start.split(':')[0]) : parseInt(start.split(':')[0]) + 12;
          const endHour = end.split(' ')[1] === 'AM' ? parseInt(end.split(':')[0]) : parseInt(end.split(':')[0]) + 12;
          const startMinute = parseInt(start.split(':')[1]);
          const endMinute = parseInt(end.split(':')[1]);

          //make hours and minutes to be in 2 digits
          const hours = String(endHour - startHour).padStart(2, '0');
          const minutes = String(endMinute - startMinute).padStart(2, '0');

          // if the minutes is negative, add 60 minutes to the minutes and subtract 1 hour from the hours
          if (minutes < 0) {
            return `${String(parseInt(hours) - 1).padStart(2, '0')}:${String(parseInt(minutes) + 60).padStart(2, '0')}`;
          }

          return `${hours}:${minutes}`;
          
        };

        const remark = service.remark_customer;
        const start = remark.split('• ')[1].split(',')[1].split('-')[0].trim();
        const end = remark.split('• ')[1].split(',')[1].split('-')[1].trim().split('\n')[0];
        //remove the things after \n in the end
        service.duration = duration(start, end);
      });


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
  let requestURL = '/api/order-services/get-confirmed-sell-orders';

  const appendQueryParam = (param, value) => {
    if (!value) return;
    requestURL += requestURL.includes('?') ? `&${param}=${value}` : `?${param}=${value}`;
  };

  appendQueryParam('search', search.value && search.value.length > 2 ? search.value : null);
  appendQueryParam('status', orderStatus.value.value || null);

  const appendDateRangeQueryParam = (param, dateRange) => {
    if (!dateRange) return;
    const formattedStartDate = formatDate(dateRange[0]);
    const formattedEndDate = formatDate(dateRange[1]);
    appendQueryParam(param, `${formattedStartDate} - ${formattedEndDate}`);
  };

  appendDateRangeQueryParam('order_date', orderDateRange.value);
  appendDateRangeQueryParam('service_date', serviceDateRange.value);

  axios.get(requestURL)
    .then(({data}) => {

      confirmedOrders.value = data.data;

      // change the order_dateTime format to yyyy-mm-dd hh:mm am/pm, remove the seconds
      confirmedOrders.value.forEach((service) => {
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

        service.order_dateTime = formatDate(service.order_dateTime);
      });

      // remove the second in service_dateTime in confirmedOrders, but it is string, like change "2024-11-04 07:00:00-08:00:00" to "2024-11-04 07:00-08:00"
      // remove the seconds in service_dateTime in confirmedOrders
      confirmedOrders.value.forEach((service) => {
        if (service.service_dateTime) {
            const dateTimeParts = service.service_dateTime.split(' ');
            const startTime = dateTimeParts[1].split('-')[0];
            const endTime = dateTimeParts[1].split('-')[1];
            const startTimeParts = startTime.split(':');
            const endTimeParts = endTime.split(':');
            let startHour = parseInt(startTimeParts[0]);
            let endHour = parseInt(endTimeParts[0]);
            let startSuffix = 'AM';
            let endSuffix = 'AM';

            if (startHour >= 12) {
                startHour -= 12;
                startSuffix = 'PM';
            }
            if (endHour >= 12) {
                endHour -= 12;
                endSuffix = 'PM';
            }

            const formattedDateTime = `${dateTimeParts[0]} ${startHour}:${startTimeParts[1]} ${startSuffix}-${endHour}:${endTimeParts[1]} ${endSuffix}`;
            service.service_dateTime = formattedDateTime;
        }
      });



      // change the item picture path to http://127.0.0.1:8000/storage/
      confirmedOrders.value.forEach((service) => {
        service.service.pictures.forEach((picture) => {
          picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path
        })
      })

      // calculate the duration of the confirmedOrders by referring the start and end time in remark_customer,
      // the remark_customer format is "Preferable Service Date and Time: • 2024/04/11, 10:16 AM-12:20 PM\nwqewqeq"
      // the duration format is hours:minutes
      confirmedOrders.value.forEach((service) => {
        const duration = (start, end) => {
          // change the am/pm to 24 hours format before calculating the duration
          const startHour = start.split(' ')[1] === 'AM' ? parseInt(start.split(':')[0]) : parseInt(start.split(':')[0]) + 12;
          const endHour = end.split(' ')[1] === 'AM' ? parseInt(end.split(':')[0]) : parseInt(end.split(':')[0]) + 12;
          const startMinute = parseInt(start.split(':')[1]);
          const endMinute = parseInt(end.split(':')[1]);

          //make hours and minutes to be in 2 digits
          const hours = String(endHour - startHour).padStart(2, '0');
          const minutes = String(endMinute - startMinute).padStart(2, '0');

          // if the minutes is negative, add 60 minutes to the minutes and subtract 1 hour from the hours
          if (minutes < 0) {
            return `${String(parseInt(hours) - 1).padStart(2, '0')}:${String(parseInt(minutes) + 60).padStart(2, '0')}`;
          }

          return `${hours}:${minutes}`;
          
        };

        const remark = service.remark_customer;

        const start = remark.split('• ')[1].split(',')[1].split('-')[0].trim();
        const end = remark.split('• ')[1].split(',')[1].split('-')[1].trim().split('\n')[0];
        
        //remove the things after \n in the end
        service.duration = duration(start, end);
      });

    })
    .catch(error => {
      console.log(error)
    })
}, 800);

watch([search, orderStatus, orderDateRange, serviceDateRange, currentTab], () => {
  getConfirmedOrders();
  passStatus.value = orderStatus.value;
  passOrderDateRange.value = orderDateRange.value;
  passServiceDateRange.value = serviceDateRange.value;
  passSearch.value = search.value;
});
getConfirmedOrders()
getPendingOrders()

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
          <span >Order of Services</span>
        </template>
        <p>
          This page displays pending and confirmed orders. Click ‘View’ to see order details. <strong>Grey color Order ID indicates orders past their service_dateTime for Confirmed Orders.</strong>
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
    <VCardSubtitle class="ml-2 mr-2 mb-2" > Filter confirmed orders by order status, order date, service date and search for customer and service.</VCardSubtitle>

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
            v-model="serviceDateRange"
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
    
            <ExportSalesServices
              :passStatus="passStatus"
              :passOrderDateRange="passOrderDateRange"
              :passServiceDateRange="passServiceDateRange"
              :passSearch="passSearch"
              />
          </VCol> 
          <VCol cols="12" md="4" />
          <VCol cols="12" md="4">
            <VTextField
              class="mb-4"
              v-model="search"
              label="Search Customer and Service"
              density="compact"
              placeholder="Search for customer and service"
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

