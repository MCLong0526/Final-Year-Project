<script setup>
import dayGridPlugin from '@fullcalendar/daygrid';
import listPlugin from '@fullcalendar/list';
import FullCalendar from '@fullcalendar/vue3';

import axios from 'axios';
import { ref, watch } from 'vue';

const allEvents = ref({
  item_orders: [],
  service_orders: []
});

const calendarEvents = ref([]);
const forFilter = ref([]);
const openItemDetails = ref(false);
const openServiceDetails = ref(false);  
const clickedItem = ref({});
const clickedService = ref({});
const eventSwitch = ref(false);

// calendar options
const calendarOptions = ref({
  plugins: [dayGridPlugin, listPlugin],
  initialView: 'listMonth',
  events: [],
  headerToolbar: {
    start: "dayGridMonth,listMonth listWeek",
    center: "title",
    end: "today,prevYear,prev,next,nextYear",
  },
  height: 700,
  aspectRatio: 1,
  dayMaxEvents: 1, // Limit the number of events displayed per day
  eventContent: function(arg) {
    const startTime = arg.event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    let endTime = '';
    if (arg.event.end) {
      endTime = arg.event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true });
    }
    
    const timeText = endTime ? `${startTime} - ${endTime}` : startTime;
    return { html: `
      <div style="font-size: 10px; white-space: nowrap; overflow: hidden; width: 200px;">
        <span style="color: #9370DB;"><b>${timeText}</b></span><br>
        <span style="color: #000;">Type: ${arg.event.extendedProps.type === 'item' ? 'Selling Item' : 'Provide Service'}</span><br>
        <span style="color: #000;">Name: ${arg.event.title}</span>
      </div>
    ` };
      
  },
  
  // can click the event to open VDialog
  eventClick: function(arg) {
    //pass the event to the dialog
    openEventDialog(arg.event);
  },

  //change the headerToolbar button text
  buttonText: {
    month: 'Calendar',
    listWeek: 'Weekly List',
    listMonth: 'Lists',

  },
  

});

// open the dialog when the event is clicked, and pass the event to the dialog after filtering the event by type
const openEventDialog = (event) => {

  //clear the clickedItem and clickedService
  clickedItem.value = {};
  clickedService.value = {};

  if(event._def.extendedProps.type === 'item') {
    clickedItem.value = allEvents.value[event._def.extendedProps.type + '_orders'].find(item => item.id === event._def.extendedProps.event_id);

    // remove the second in meet_dateTime in clickedItem, format like "2024-05-12 12:54:00" to "2024-05-12 12:54 PM"
    const dateTimeParts = clickedItem.value.meet_dateTime.split(" ");
    const timeParts = dateTimeParts[1].split(":");
    const hour = parseInt(timeParts[0]);
    const minute = timeParts[1];
    const amPm = hour >= 12 ? "PM" : "AM";
    const hourStr = hour > 12 ? (hour - 12).toString() : hour.toString();
    clickedItem.value.meet_dateTime = dateTimeParts[0] + " " + hourStr + ":" + minute + " " + amPm;


    openItemDetails.value = true;
  } else {
    clickedService.value = allEvents.value[event._def.extendedProps.type + '_orders'].find(service => service.id === event._def.extendedProps.event_id);

    // remove the second in service_dateTime in clickedService, but it is string, like change "2024-11-04 07:00:00-08:00:00" to "2024-11-04 07:00AM-08:00AM"
    const dateTimeParts = clickedService.value.service_dateTime.split(" ");
    const timeRange = dateTimeParts[1].split("-");
    const startTime = timeRange[0];
    const endTime = timeRange.length > 1 ? timeRange[1] : startTime; // Use startTime if endTime is not provided  
    const startHour = startTime.split(":")[0];
    const startMinute = startTime.split(":")[1];
    const endHour = endTime.split(":")[0];
    const endMinute = endTime.split(":")[1];
    const startHourInt = parseInt(startHour);
    const endHourInt = parseInt(endHour);
    const startHourStr = startHourInt > 12 ? (startHourInt - 12).toString() : startHour;
    const endHourStr = endHourInt > 12 ? (endHourInt - 12).toString() : endHour;
    const startAmPm = startHourInt >= 12 ? "PM" : "AM";
    const endAmPm = endHourInt >= 12 ? "PM" : "AM";
    clickedService.value.service_dateTime = dateTimeParts[0] + " " + startHourStr + ":" + startMinute + " " + startAmPm + "-" + endHourStr + ":" + endMinute + " " + endAmPm;

    // calculate the duration of the clickedService by referring the start and end time in remark_customer,
    // the remark_customer format is "Preferable Service Date and Time: • 2024/04/11, 10:16 AM-12:20 PM\nwqewqeq"
    // the duration format is hours:minutes
    const dateTimeParts2 = clickedService.value.remark_customer.split("•")[1].split(",")[1].split("-");
    const startTime2 = dateTimeParts2[0].trim();
    const endTime2 = dateTimeParts2[1].trim();
    const start = new Date("2024/04/11 " + startTime2);
    const end = new Date("2024/04/11 " + endTime2);
    const duration = (end - start) / 1000 / 60 / 60;
    clickedService.value.duration = duration.toFixed(2);


    openServiceDetails.value = true;
  }
};


// get the upcoming meetups from the backend
const getUpComingMeetupSeller = () => {
  axios.get('/api/calendar/upcoming-meetup-seller')
    .then(response => {
      const twoEvents = response.data.data;
      allEvents.value = twoEvents;
      const events = [];

      twoEvents.item_orders.forEach(item => {
        events.push({
          title: item.item.name,
          start: item.meet_dateTime.replace(" ", "T"),
          end: item.meet_dateTime.replace(" ", "T"),
          color: '#68b5e8',
          
          event_id: item.id,
          type: 'item', // Set the type to 'item' for item orders
        });
      });

      twoEvents.service_orders.forEach(service => {
        const dateTimeParts = service.service_dateTime.split(" ");
        const startDate = dateTimeParts[0];
        const startTime = dateTimeParts[1].split("-")[0];
        const endTime = dateTimeParts[1].split("-")[1];

        const start = startDate + "T" + startTime + ":00";
        const end = startDate + "T" + endTime + ":00";

        events.push({
          title: service.service.name,
          start: start,
          end: end,
          color: '#7ee879',
          event_id: service.id,
          type: 'service', // Set the type to 'service' for service orders
        });
      });

      calendarEvents.value = events;
      filterEvents();

    })
    .catch(error => {
      console.log(error);
    });
};

const getUpComingMeetupBuyer = () => {
  axios.get('/api/calendar/upcoming-meetup-buyer')
    .then(response => {
      const twoEvents = response.data.data;
      allEvents.value = twoEvents;
      const events = [];

      twoEvents.item_orders.forEach(item => {
        events.push({
          title: item.item.name,
          start: item.meet_dateTime.replace(" ", "T"),
          end: item.meet_dateTime.replace(" ", "T"),
          color: '#68b5e8',
          
          event_id: item.id,
          type: 'item', // Set the type to 'item' for item orders
        });
      });

      twoEvents.service_orders.forEach(service => {
        const dateTimeParts = service.service_dateTime.split(" ");
        const startDate = dateTimeParts[0];
        const startTime = dateTimeParts[1].split("-")[0];
        const endTime = dateTimeParts[1].split("-")[1];

        const start = startDate + "T" + startTime + ":00";
        const end = startDate + "T" + endTime + ":00";

        events.push({
          title: service.service.name,
          start: start,
          end: end,
          color: '#7ee879',
          event_id: service.id,
          type: 'service', // Set the type to 'service' for service orders
        });
      });

      calendarEvents.value = events;
      

    })
    .catch(error => {
      console.log(error);
    });
};

// checkbox for filtering the events
const eventCheckbox = ref([
  { title: 'View All', color: 'primary' },
  { title: 'Selling Item', color: 'info' },
  { title: 'Provide Service', color: 'success' },
]);

// select all checkbox by default
const selectedCheckbox = ref(eventCheckbox.value.map(item => item.title));

function filterEvents() {
  if (selectedCheckbox.value.includes('View All')) {
    forFilter.value = calendarEvents.value;
    if (selectedCheckbox.value.includes('Selling Item') && selectedCheckbox.value.includes('Provide Service')) {
      forFilter.value = calendarEvents.value;
    } else if (selectedCheckbox.value.includes('Selling Item')) {
      forFilter.value = calendarEvents.value.filter(event => event.type === 'item');
    } else if (selectedCheckbox.value.includes('Provide Service')) {
      forFilter.value = calendarEvents.value.filter(event => event.type === 'service');
    }
  } else if (selectedCheckbox.value.includes('Selling Item') && selectedCheckbox.value.includes('Provide Service')) {
    forFilter.value = [];
  }

  // Update the calendarOptions
  calendarOptions.value = {
    ...calendarOptions.value,
    events: forFilter.value,
  };

}

// watch for changes in selectedCheckbox and filter the events accordingly, default is select all checkbox, so all events will be displayed, 
// if user unselect the checkbox, the events will be filtered accordingly
watch(selectedCheckbox, (newVal) => {
  if (newVal.includes('View All')) {
    forFilter.value = calendarEvents.value;
    if (newVal.includes('Selling Item') && newVal.includes('Provide Service')) {
      forFilter.value = calendarEvents.value;
    } else if (newVal.includes('Selling Item')) {
      forFilter.value = calendarEvents.value.filter(event => event.type === 'item');
    } else if (newVal.includes('Provide Service')) {
      forFilter.value = calendarEvents.value.filter(event => event.type === 'service');
  }
  //else if there is no View All in the selectedCheckbox, then the forFilter will be empty
  } else if (newVal.includes('Selling Item') && newVal.includes('Provide Service')) {
    forFilter.value = [];
  }
  calendarOptions.value = {
    ...calendarOptions.value,
    events: forFilter.value,
  };

} 
);

// watch for changes in calendarEvents and update the events in the calendarOptions
watch(calendarEvents, () => {
  calendarOptions.value.events = calendarEvents.value;
});

watch(eventSwitch, (newVal) => {
  if (newVal) {
    calendarEvents.value = [];
    selectedCheckbox.value = ['View All', 'Selling Item', 'Provide Service'];

    //don assign the value to the calendarEvents, because the calendarEvents is watched, and it will update the calendarOptions
    getUpComingMeetupSeller();

    filterEvents();

  } else {
    calendarEvents.value = [];
    selectedCheckbox.value = ['View All', 'Selling Item', 'Provide Service'];
    getUpComingMeetupBuyer();

    filterEvents();
  }
});

// get the upcoming meetups
getUpComingMeetupBuyer();

</script>


<template>
  <div class="box-style">
    <div class="mt-4 ml-4 mr-4 mb-4">
      <VRow>
        <VCol cols="12" md="3" class="border-right">
          <VRow>
            <VCardTitle class="text-overline mt-4 ml-4 mr-4 mb-2" style="font-size: 20px !important;"><b>Meetup (Upcoming)</b></VCardTitle>
            <VCardSubtitle class="text-overline ml-4 mr-4 mb-4" style="font-size: 13px !important;"><b>Date and Time for {{ eventSwitch ? 'Sales' : 'Purchases' }}</b></VCardSubtitle>
          </VRow>
          <VDivider class="mt-4 mb-4" />
          <VRow>
            <VCol cols="12">
              <VCardText>
                <span class="text-overline" style="font-size: 15px !important;">Event Type:</span>
                <br>
                <VRadioGroup v-model="eventSwitch">
                <VRadio
                  label="Purchases"
                  color="warning"
                  :value="false"
                />
                <VRadio
                  label="Sales"
                  color="error"
                  :value="true"
                />
              </VRadioGroup>
                  
            </VCardText>
            </VCol>
          </VRow>
          <VDivider class="mt-4 mb-4" />
          <VRow>
            <VCol cols="12">
              <VCardText>
                <span class="text-overline" style="font-size: 15px !important;">Filter by:</span>
             

              <VCheckbox
                v-for="item in eventCheckbox"
                :key="item.title"
                v-model="selectedCheckbox"
                :label="item.title"
                :color="item.color.toLowerCase()"
                :value="item.title"
                class="mt-2"
              />
            </VCardText>
            </VCol>
          </VRow>
          
        </VCol>
        <VCol cols="12" md="9">
          <FullCalendar 
            :options='calendarOptions'
            
          />
        </VCol>
      </VRow>

      
    </div>
  </div>


  <!--Approved Item Dialog-->
  <VDialog
    v-model="openItemDetails"
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
            <strong>Buyer</strong>
            <span>: {{ clickedItem.user.username }}</span>
          </div>
          <div class="detail-row">
            <strong>Status</strong>
            <span v-if="clickedItem.status=='Approved'">: <VChip color="success" size="small"><VIcon icon="ri-check-fill" class="mr-1"/>{{ clickedItem.status }}</VChip></span>
            <span v-else>: <VChip color="error" size="small"><VIcon icon="ri-close-line" class="mr-1"/>{{ clickedItem.status }}</VChip></span>
          </div>
          <VDivider class="mt-2 mb-2"/>
          <div class="detail-row">
            <strong>Item Name</strong>
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
          
      <VCardText class="pt-5">
        <VRow>
          <VCol cols="12" md="3">
            <VBtn
              color="secondary"
              @click="openItemDetails = false"
            >
              <VIcon icon="ri-close-line" class="mr-1"/>
              Close
            </VBtn>
          </VCol>
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3" />
            
          </VRow>
        
      </VCardText>
    </VCard>
  </VDialog>


  <!--Approved Service Dialog-->
  <VDialog
    v-model="openServiceDetails"
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
          <VCol cols="12" md="3">
            <VBtn
              color="secondary"
              @click="openServiceDetails = false"
            >
              <VIcon icon="ri-close-line" class="mr-1"/>
              Close
            </VBtn>
          </VCol>
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3" />
          <VCol cols="12" md="3" />
            
            
          </VRow>
        
      </VCardText>
    </VCard>
  </VDialog>


</template>


<style scoped>
.border-right {
  border-inline-end: 1px solid #ccc; /* Adjust the color and thickness as needed */
}

.box-style {
  padding: 3px; /* Padding around the table */
  border: 0.4px solid #282828;
  border-radius: 20px;

  /* Add margin to create space around the div */
  margin: 10px;
  background-color: #fff; /* White background color */
  box-shadow: 0 0 10px rgba(0, 0, 0, 15%); /* Drop shadow */
}



/* Example CSS for styling the buttons */
.fc button {
  border: none;
  border-radius: 5px;
  background-color: #007bff;
  color: #fff;
  cursor: pointer;
  margin-block: 0;
  margin-inline: 5px;
  padding-block: 5px;
  padding-inline: 10px;
}

.fc button:hover {
  background-color: #0056b3;
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
