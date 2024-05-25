<script setup>
import logo from '@images/logos/logo.png';
import axios from 'axios';
import jspdf from 'jspdf';
import 'jspdf-autotable';
import debounce from 'lodash/debounce';
import Papa from 'papaparse';
import { defineProps, ref } from 'vue';
import * as XLSX from 'xlsx';

const props = defineProps({
  passStatus:{
    type: Object,
    default: null
  },
  passOrderDateRange:{
    type:Array,
    default: null
  },
  passServiceDateRange:{
    type:Array,
    default: null
  },
  passSearch:{
    type:String,
    default: null
  },
})

const { passOrderDateRange:orderDateRange } = toRefs(props)
const { passServiceDateRange:serviceDateRange } = toRefs(props)

const confirmedOrders = ref([])

const items = ref([
  { id: 1, title: 'PDF', value: 'PDF', icon: 'ri-file-pdf-line' },
  { id: 2, title: 'Excel', value: 'Excel', icon: 'ri-file-excel-2-line' },
  { id: 3, title: 'CSV', value: 'CSV', icon: 'ri-file-text-line' },
  { id: 4, title: 'Print', value: 'Print', icon: 'ri-printer-line' },
])

const itemSelected = ref(null)
const base64Image = logo

//Export as PDF
const exportPDF = () => {
  const doc = new jspdf()

  const tableData = confirmedOrders.value.map(row => [
    row.id,
    row.order_dateTime,
    row.user.username,
    row.service.name,
    row.service_dateTime,
    row.place_to_service,
    row.duration,
    row.approximated_price,
    row.rating,
    row.status,
  ])


  const date = new Date();
  const dateString = `${date.getFullYear()}/${date.getMonth()+1}/${date.getDate()} ${date.getHours()}:${("0" + date.getMinutes()).slice(-2)}:${("0" + date.getSeconds()).slice(-2)}`;

  // Get page width
  let pageWidth = doc.internal.pageSize.getWidth();

  // Assume logo width is 50, adjust this value as per your requirement
  let logoWidth = 30;

  // Calculate x-coordinate for center alignment
  let xCoord = (pageWidth / 2) - (logoWidth / 2);

  // Add the image to the PDF with adjusted x-coordinate and size
  doc.addImage(base64Image, 'PNG', xCoord, 12, logoWidth, logoWidth);

  doc.setFontSize(18);
  doc.text('Report of Sales Services', 14, 50);
  doc.setFontSize(10);
  doc.setTextColor(128);
  doc.text(`Export Date: ${dateString}`, 14, 58);

  doc.autoTable({
    theme: 'grid',
    head: [['Order ID', 'Order Date', 'Customer', 'Service', 'Service Date', 'Place to Service', 'Duration', 'Approximated Price', 'Rating', 'Status']],
    body: tableData,
    styles: { fillColor: [255, 255, 255], textColor: 20, fontSize: 10 },
    headStyles: { fillColor: [93, 123, 234], textColor: 255, fontSize: 10 },
    margin: { top: 65 },
    startY: 65,
  })

  doc.save('SalesServices.pdf')
}


//Export as Excel
const exportExcel = () => {

  const tableData = confirmedOrders.value.map(row => ({
    'Order ID': row.id,
    'Order Date': row.order_dateTime,
    'Customer': row.user.username,
    'Service': row.service.name,
    'Service Date': row.service_dateTime,
    'Place to Service': row.place_to_service,
    'Duration': row.duration,
    'Approximated Price': row.approximated_price,
    'Rating': row.rating,
    'Status': row.status,
  }))

  const ws = XLSX.utils.json_to_sheet(tableData)
  const wb = XLSX.utils.book_new()

  XLSX.utils.book_append_sheet(wb, ws, "Sheet1")
  XLSX.writeFile(wb, "SalesServices.xlsx")
}

//Export as CSV
const exportCsv = () => {

  const data = confirmedOrders.value.map(row => ({
    'Order ID': row.id,
    'Order Date': row.order_dateTime,
    'Customer': row.user.username,
    'Service': row.service.name,
    'Service Date': row.service_dateTime,
    'Place to Service': row.place_to_service,
    'Duration': row.duration,
    'Approximated Price': row.approximated_price,
    'Rating': row.rating,
    'Status': row.status,
  }))

  const csv = Papa.unparse(data)

  const blob = new Blob([csv], { type: "text/csv;charset=utf-8" })
  const url = URL.createObjectURL(blob)
  const link = document.createElement("a")

  link.href = url
  link.download = "SalesServices.csv"
  document.body.appendChild(link)
  link.click()
  document.body.removeChild(link)
}

//Print table
const tableHtml = computed(()=> {
  
  const data = confirmedOrders.value.map(row => ({
    'Order ID': row.id,
    'Order Date': row.order_dateTime,
    'Customer': row.user.username,
    'Service': row.service.name,
    'Service Date': row.service_dateTime,
    'Place to Service': row.place_to_service,
    'Duration': row.duration,
    'Approximated Price': row.approximated_price,
    'Rating': row.rating,
    'Status': row.status,
  }))

  const headerHtml = `<tr>${Object.keys(data[0]).map(key => `<th style="text-align: left">${key}</th>`).join('')}</tr>`
  const rowsHtml = data.map(row => `<tr>${Object.values(row).map(cell => `<td>${cell}</td>`).join('')}</tr>`).join('')
  
  return `<table><thead>${headerHtml}</thead><tbody>${rowsHtml}</tbody></table>`

})

const printTable = () => {

  const win = window.open('','', 'height=400,width=800')

  win.document.write(`<html><head><style>table {border-collapse: collapse; widh: 100%} td,th {border: 1px solid #dddddd; text align:left; padding: 8px;} th {background-color: #f2f2f2;}</style></head><body>${tableHtml.value}</body></html>`)
  win.document.close()
  win.focus()
  win.print()
  win.close()
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

  appendQueryParam('search', props.passSearch && props.passSearch.length > 2 ? props.passSearch : null);
  appendQueryParam('status', props.passStatus.value || null);

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

      // if the status is rejected or cancelled, set the service_dateTime as null
      confirmedOrders.value.forEach((service) => {
        if(service.status === 'Rejected' || service.status === 'Cancelled'){
          service.service_dateTime = 'null'
        }
      })

      //if rating is null, set it as 'null'
      confirmedOrders.value.forEach((service) => {
        if(service.rating === null){
          service.rating = 'null'
        }
      })

      if(itemSelected.value === 'Print'){
        printTable()
      } else if(itemSelected.value === 'PDF'){
        exportPDF()
      } else if(itemSelected.value === 'Excel'){
        exportExcel()
      } else if(itemSelected.value === 'CSV'){
        exportCsv()
      }

    })
    .catch(error => {
      console.log(error)
    })
}, 800);

const handleClick = itemName => {
  itemSelected.value = itemName
  getConfirmedOrders()
}
</script>

<template>
  <VMenu open-on-hover>
    <template #activator="{ props: menuProps }">
      <VBtn 
        v-bind="menuProps"
        prepend-icon="ri-download-2-line"
      >
        Export
      </VBtn>
    </template>

    <VList>
      <template
        v-for="item in items"
        :key="item.value"
      >
        <VListItem     
          @click="handleClick(item.value)"
        >
          <div style="display: flex; align-items: center;">
            <VIcon
              :icon="item.icon"
              size="16px"
              style="margin-inline-end: 5px;"
            />
            {{ item.title }}
          </div>
        </VListItem>
      </template>
    </VList>
  </VMenu>
</template>
