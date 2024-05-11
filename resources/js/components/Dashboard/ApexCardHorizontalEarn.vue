
<script setup>
import { hexToRgb } from '@layouts/utils';
import axios from 'axios';
import VueApexCharts from 'vue3-apexcharts';
import { useTheme } from 'vuetify';


const vuetifyTheme = useTheme()

const options = computed(() => {
  const currentTheme = ref(vuetifyTheme.current.value.colors)
  const variableTheme = ref(vuetifyTheme.current.value.variables)
  const disabledColor = `rgba(${ hexToRgb(currentTheme.value['on-surface']) },${ variableTheme.value['disabled-opacity'] })`
  const borderColor = `rgba(${ hexToRgb(String(variableTheme.value['border-color'])) },${ variableTheme.value['border-opacity'] })`
  
  return {
    chart: {
      offsetY: -10,
      offsetX: -15,
      parentHeightOffset: 0,
      toolbar: { show: false },
    },
    plotOptions: {
      bar: {
        borderRadius: 6,
        distributed: true,
        columnWidth: '30%',
      },
    },
    stroke: {
      width: 2,
      colors: [currentTheme.value.surface],
    },
    legend: { show: false },
    grid: {
      borderColor,
      strokeDashArray: 7,
      xaxis: { lines: { show: false } },
    },
    dataLabels: { enabled: false },
    colors: [
      'rgba(var(--v-theme-primary),1)',
      'rgba(var(--v-theme-primary),1)',
      'rgba(var(--v-theme-primary),1)',
      'rgba(var(--v-theme-primary),1)',
      'rgba(var(--v-theme-primary),1)',
      'rgba(var(--v-theme-primary),1)'
    ],
    states: {
      hover: { filter: { type: 'none' } },
      active: { filter: { type: 'none' } },
    },
    xaxis: {
      categories: category.value,
      tickPlacement: 'on',
      labels: { show: true },
      crosshairs: { opacity: 0 },
      axisTicks: { show: false },
      axisBorder: { show: false },
    },
    yaxis: {
      show: true,
      tickAmount:4,
      labels: {
        style: {
          colors: disabledColor,
          fontSize: '13px',
        },

        formatter: value => `RM ${ value > 999 ? `${ (value / 1000).toFixed(0) }` : value }`,
      },
    },
    responsive: [
      {
        breakpoint: 1560,
        options: { plotOptions: { bar: { columnWidth: '35%' } } },
      },
      {
        breakpoint: 1380,
        options: { plotOptions: { bar: { columnWidth: '45%' } } },
      },
    ],
  }
})

const series = ref([
  {
    name: 'Total Earned',
    data: [],
  },
])
const totalEarned = ref([]);
const totalEarnedYear = ref(null);
const totalEarnedWeek = ref(null);
const monthlyEarned = ref([]);
const isMonthly = ref(false);
const category = ref([]);
const getWeeklyEarned = async () => {
  isMonthly.value = false;
  try {
    const response = await axios.get('/api/dashboard/get-weekly-earned');
    totalEarned.value = response.data.data.total_earned;

    series.value[0].data = [
      totalEarned.value.Monday,
      totalEarned.value.Tuesday,
      totalEarned.value.Wednesday,
      totalEarned.value.Thursday,
      totalEarned.value.Friday,
      totalEarned.value.Saturday,
      totalEarned.value.Sunday,
    ]

    totalEarnedWeek.value = response.data.data.total_earned_week;

  } catch (error) {
    console.log(error)
  }
}

const getMonthlySales = async () =>{
  try {
    const response = await axios.get('/api/dashboard/get-monthly-earned');
    monthlyEarned.value = response.data.data.total_earned;

    totalEarnedYear.value = response.data.data.total_earned_year;
    isMonthly.value = true;
  } catch (error) {
    console.log(error)
  }


}

watch(isMonthly, (value) => {
  if (value) {
    series.value[0].data = [
      monthlyEarned.value.January,
      monthlyEarned.value.February,
      monthlyEarned.value.March,
      monthlyEarned.value.April,
      monthlyEarned.value.May,
      monthlyEarned.value.June,
      monthlyEarned.value.July,
      monthlyEarned.value.August,
      monthlyEarned.value.September,
      monthlyEarned.value.October,
      monthlyEarned.value.November,
      monthlyEarned.value.December,
    ]
    category.value = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
  } else {
    series.value[0].data = [
      totalEarned.value.Monday,
      totalEarned.value.Tuesday,
      totalEarned.value.Wednesday,
      totalEarned.value.Thursday,
      totalEarned.value.Friday,
      totalEarned.value.Saturday,
      totalEarned.value.Sunday,
    ]
    category.value = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']
  }
})

onMounted(() => {
  getWeeklyEarned()
})
</script>

<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Earnings Overview</VCardTitle>
      <VCardSubtitle>Through Provided Services</VCardSubtitle>
    </VCardItem>

    <VCardText>
      <VueApexCharts
        type="bar"
        :options="options"
        :series="series"
        :height="250"
      />

      <div class="align-center mb-5 gap-x-4">
        <h4 class="text-h4">
          {{ isMonthly ? 'This Year' : 'This Week' }}'s Earnings
        </h4>
        <p v-if="isMonthly" class="mb-0">
          Total Earnings in this year is <b>RM{{ totalEarnedYear }}</b>
        </p>
        <p v-else class="mb-0">
          Total Earnings in this week is <b>RM{{ totalEarnedWeek }}</b>
        </p>
      </div>

      <VBtn
       v-if="!isMonthly"
        color="primary"
        text
        block
        @click="getMonthlySales"
      >
        View Every Month's Earnings
      </VBtn>
      <VBtn
        v-else
        color="primary"
        text
        block
        @click="getWeeklyEarned"
      >
        View This Week's Earnings
      </VBtn>
    </VCardText>
  </VCard>
</template>
