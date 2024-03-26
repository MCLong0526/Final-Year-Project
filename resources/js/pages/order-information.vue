<script setup>
import TablePendingOrders from "@/components/Order/OrderPendingTable.vue";
import axios from "axios";

const pendingOrders = ref([]);


const getPendingOrders = () => {
  axios.get('/api/order-items/get-pending-orders')
    .then(response => {
      pendingOrders.value = response.data.data

      // change the order_dateTime format to yyyy-mm-dd hh:mm am/pm
      pendingOrders.value.forEach((item) => {
        item.order_dateTime = new Date(item.order_dateTime).toLocaleString()
      })

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

getPendingOrders()
</script>
<template>
  <div class="table-style">
    
    <TablePendingOrders
      :pendingOrders="pendingOrders"
      :getPendingOrders="getPendingOrders"
    />
  </div>
</template>

<style scoped>
.table-style{
  padding: 0.5px; /* Padding around the table */
  background-color: #848383; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
}
</style>
