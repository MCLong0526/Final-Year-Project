<script setup>
import TableConfirmedOrders from "@/components/Order/OrderConfirmedTable.vue";
import TablePendingOrders from "@/components/Order/OrderPendingTable.vue";
import axios from "axios";

const allOrders = ref([]);
const pendingOrders = ref([]);
const confirmedOrders = ref([]);


const getAllOrders = () => {
  axios.get('/api/order-items/get-all-sell-orders')
    .then(response => {
      allOrders.value = response.data.data

      // change the order_dateTime format to yyyy-mm-dd hh:mm am/pm, remove the seconds
      allOrders.value.forEach((item) => {
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
      allOrders.value.forEach((item) => {
        item.item.pictures.forEach((picture) => {
          picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path
        })
      })

      //separate status = 'Pending' from allOrders, and assign to pendingOrders
      allOrders.value.forEach((item) => {
        if (item.status == 'Pending') {
          pendingOrders.value.push(item)
        }else
        {
          confirmedOrders.value.push(item)
        }

      })



    })
    .catch(error => {
      console.log(error)
    })
}

// const getBuyOrders = () => {
//   axios.get('/api/order-items/get-buy-orders')
//     .then(response => {
//       buyOrders.value = response.data.data

//       // change the order_dateTime format to yyyy-mm-dd hh:mm am/pm
//       buyOrders.value.forEach((item) => {
//         item.order_dateTime = new Date(item.order_dateTime).toLocaleString()
//       })

//       // change the item picture path to http://
//       buyOrders.value.forEach((item) => {
//         item.item.pictures.forEach((picture) => {
//           picture.picture_path = 'http://127.0.0.1:8000/storage/' + picture.picture_path
//         })
//       })
//     })
//     .catch(error => {
//       console.log(error)
//     })
//   }

getAllOrders()
//getBuyOrders()
</script>
<template>
  <VCardTitle class="box-style">Orders You Are Selling</VCardTitle>
  <div class="table-style">
    
    <TablePendingOrders
      :pendingOrders="pendingOrders"
      :getAllOrders="getAllOrders"
    />
  </div>
  <VDivider class="mt-2 mb-2"/>
  <div class="table-style">
    
    <TableConfirmedOrders
      :confirmedOrders="confirmedOrders"
      :getAllOrders="getAllOrders"
    />
  </div>
</template>

<style scoped>
.table-style{
  padding: 0.5px; /* Padding around the table */
  background-color: #848383; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
}

.box-style {
  padding: 10px; /* Padding around the text */
  border: 1.5px solid #d3d3d3;
  border-radius: 5px; /* Rounded corners */
  background-color: rgba(255, 255, 255, 53.7%); /* Light gray background color */
}
</style>
