<script setup>
import ConfirmedPurchasesTable from "@/components/Purchases/ConfirmedServicePurchasesTable.vue";
import PendingPurchasesTable from "@/components/Purchases/PendingServicePurchasesTable.vue";
import axios from "axios";

const currentTab = ref('tab-1');
const pendingPurchasesOrders = ref([]);
const approvedPurchasesOrders = ref([]);
const rejectedPurchasesOrders = ref([]);
const noPendingOrders = ref(0);
const noApprovedOrders = ref(0);
const noRejectedOrders = ref(0);
const noCancelledOrders = ref(0);  
const allOrders = ref([]);

const getPurchasesOrder = () => {
  axios.get('/api/order-services/get-purchases-orders')
    .then(response => {
      allOrders.value = response.data.data

      // change the order_dateTime format to yyyy-mm-dd hh:mm am/pm, remove the seconds
      allOrders.value.forEach((service) => {
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

      // remove the second in service_dateTime in allOrders, but it is string, like change "2024-11-04 07:00:00-08:00:00" to "2024-11-04 07:00-08:00"
      // remove the seconds in service_dateTime in allOrders
      allOrders.value.forEach((service) => {
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


      // change the service picture path to http://
      allOrders.value.forEach((service) => {
        service.service.pictures.forEach((picture) => {
          picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path
        })
      })

      //separate status = 'Pending' from allOrders, and assign to pendingOrders
      allOrders.value.forEach((service) => {
        if (service.status == 'Pending') {
          pendingPurchasesOrders.value.push(service)
        }else if(service.status == 'Approved'){
          approvedPurchasesOrders.value.push(service)
        }else{
          rejectedPurchasesOrders.value.push(service)
        }
      

      })

      // calculate the duration of the allOrders by referring the start and end time in remark_customer,
      // the remark_customer format is "Preferable Service Date and Time: • 2024/04/11, 10:16 AM-12:20 PM\nwqewqeq"
      // the duration format is hours:minutes
      allOrders.value.forEach((service) => {
        if (service.status !== 'Cancelled') {
          const remark = service.remark_customer;
          const dateTime = remark.split('• ')[1].split(',')[1].split('-');
          const start = dateTime[0].trim();
          const end = dateTime[1].trim().split('\n')[0];

          const startTime = new Date(`2000/01/01 ${start}`);
          const endTime = new Date(`2000/01/01 ${end}`);

          const duration = (endTime - startTime) / (1000 * 60); // Duration in minutes

          const hours = Math.floor(duration / 60);
          const minutes = duration % 60;

          service.duration = `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}`;
        }
      });

      // sort for the approvedPurchasesOrders, make sure the  Sort the filtered results to ensure upcoming orders are first, make sure the meet_dateTime that over today will be on bottom
      // now the format of meet_dateTime is "2024-11-04 07:00AM-08:00AM"
      approvedPurchasesOrders.value.sort((a, b) => {
        const now = new Date();
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate()); // Clear time part

        const parseDate = (dateTime) => {
          return new Date(dateTime.split(' ')[0]); // Only parse the date part
        };

        const dateA = parseDate(a.service_dateTime);
        const dateB = parseDate(b.service_dateTime);

        const isToday = (date) => {
          return date.getFullYear() === today.getFullYear() &&
                 date.getMonth() === today.getMonth() &&
                 date.getDate() === today.getDate();
        };

        if (isToday(dateA) && !isToday(dateB)) return -1;
        if (!isToday(dateA) && isToday(dateB)) return 1;
        if (isToday(dateA) && isToday(dateB)) return 0;

        if (dateA > now && dateB > now) return dateA - dateB;
        if (dateA > now) return -1;
        if (dateB > now) return 1;

        return dateB - dateA;
      });
      



    })
    .catch(error => {
      console.log(error)
    })
}

const countAuthPurchases = () => {
  axios.get('/api/order-services/count-auth-purchases')
    .then(response => {
      
      noPendingOrders.value = response.data.data.noPendingOrders
      noApprovedOrders.value = response.data.data.noApprovedOrders
      noRejectedOrders.value = response.data.data.noRejectedOrders
      noCancelledOrders.value = response.data.data.noCancelledOrders

    })
    .catch(error => {
      console.log(error)
    })
}

countAuthPurchases()
getPurchasesOrder()

</script>

<template>

  <div class="box-style">
    <VRow class="no-gutters">
      <VCol cols="12" md="3" >
        <CardStatisticsHorizontal
          title="Pending Orders"
          color="warning"
          icon="ri-emotion-happy-line"
          :stats="noPendingOrders"
          :change="0"
        />
        
      </VCol>
      <VCol cols="12" md="3">
        <CardStatisticsHorizontal
          title="Approved Orders"
          color="success"
          icon="ri-emotion-laugh-line"
          :stats="noApprovedOrders"
          :change="0"
        />
      </VCol>
      <VCol cols="12" md="3">
        <CardStatisticsHorizontal
          title="Rejected Orders"
          color="error"
          icon="ri-emotion-unhappy-line"
          :stats="noRejectedOrders"
          :change="0"
        />
      </VCol>
      <VCol cols="12" md="3">
        <CardStatisticsHorizontal
          title="Cancelled Orders"
          color="secondary"
          icon="ri-emotion-normal-line"
          :stats="noCancelledOrders"
          :change="0"
        />
      </VCol>

    </VRow>
  </div>


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
          <span >Order of Purchasing Services</span>
        </template>
        <p>
          This page displays pending and confirmed orders. Click ‘View’ to see order details. <strong>Grey color Order ID indicates orders past their service_dateTime.</strong>
        
        </p>
      </VAlert>
    <VTabs
      v-model="currentTab"
      grow
      stacked
    >
      <VTab value="tab-1">
        <VIcon
          icon="ri-calendar-schedule-fill"
          class="mb-2"
        />
        <span>Pending Order</span>
      </VTab>

      <VTab value="tab-2">
        <VIcon
          icon="ri-calendar-check-fill"
          class="mb-2"
        />
        <span>Approved Order</span>
      </VTab>

      <VTab value="tab-3">
        <VIcon
          icon="ri-calendar-close-fill"
          class="mb-2"
        />
        <span>Rejected/Cancelled Order</span>
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
          <PendingPurchasesTable
            :pendingPurchasesOrders="pendingPurchasesOrders"
          />
          
        </div>
      </VWindowItem>

      <VWindowItem
        value="tab-2"
      >
        <div class="table-style">
          <ConfirmedPurchasesTable
            :confirmedPurchasesOrders="approvedPurchasesOrders"
          />
          
        </div>
      </VWindowItem>

      <VWindowItem
        value="tab-3"
      >
        <div class="table-style">
          <ConfirmedPurchasesTable
            :confirmedPurchasesOrders="rejectedPurchasesOrders"
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

