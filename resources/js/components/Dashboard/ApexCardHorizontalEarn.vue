<template>
  <VCard>
    <VCardItem>
      <VCardTitle>Earnings Overview</VCardTitle>
      <VCardSubtitle v-if="currentType === 'services'" class="ml-1">
        Through Provide Services
      </VCardSubtitle>
      <VCardSubtitle v-else-if="currentType === 'items'" class="ml-1">
        Through Selling Items
      </VCardSubtitle>
      <VCardSubtitle v-else class="ml-1">
        Through Provide Services and Selling Items
      </VCardSubtitle>
      <template #append>
        <MoreBtn class="me-n3 mt-n1" i/>
        <VMenu
          activator="parent"
          width="240"
          location="end"
          offset="14px"
        >
          <VList>
            <VListItem @click="filterTo('services')">
              Provide Services
            </VListItem>
            <VListItem @click="filterTo('items')">
              Selling Items
            </VListItem>
            <VListItem @click="filterTo('both')">
              Both
            </VListItem>
          </VList>
        </VMenu>
      </template>
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

<script setup>
import { hexToRgb } from '@layouts/utils';
import axios from 'axios';
import { computed, onMounted, ref, watch } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { useTheme } from 'vuetify';

const vuetifyTheme = useTheme();
const currentType = ref('both');

const options = computed(() => {
  const currentTheme = ref(vuetifyTheme.current.value.colors);
  const variableTheme = ref(vuetifyTheme.current.value.variables);
  const disabledColor = `rgba(${hexToRgb(currentTheme.value['on-surface'])},${variableTheme.value['disabled-opacity']})`;
  const borderColor = `rgba(${hexToRgb(String(variableTheme.value['border-color']))},${variableTheme.value['border-opacity']})`;

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
      'rgba(var(--v-theme-primary),1)',
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
      tickAmount: 4,
      labels: {
        style: {
          colors: disabledColor,
          fontSize: '13px',
        },
        formatter: value => `RM${value}`,
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
  };
});

const series = ref([
  {
    name: 'Total Earned',
    data: [],
  },
]);
const totalEarned = ref([]);
const totalEarnedYear = ref(null);
const totalEarnedWeek = ref(null);
const monthlyEarned = ref([]);
const isMonthly = ref(false);
const category = ref([]);

// Parse the data to number
const parseEarningsData = (data) => {
  return Object.values(data).map(value => Number(String(value).replace(/,/g, '')));
};

const getWeeklyEarned = async () => {
  isMonthly.value = false;
  try {
    const response = await axios.get(`/api/dashboard/get-weekly-earned?type=${currentType.value}`);
    totalEarned.value = response.data.data.total_earned;

    series.value[0].data = parseEarningsData({
      Monday: totalEarned.value.Monday,
      Tuesday: totalEarned.value.Tuesday,
      Wednesday: totalEarned.value.Wednesday,
      Thursday: totalEarned.value.Thursday,
      Friday: totalEarned.value.Friday,
      Saturday: totalEarned.value.Saturday,
      Sunday: totalEarned.value.Sunday,
    });

    totalEarnedWeek.value = response.data.data.total_earned_week;
    
  } catch (error) {
    console.log(error);
  }
};

const getMonthlySales = async () => {
  try {
    const response = await axios.get(`/api/dashboard/get-monthly-earned?type=${currentType.value}`);
    monthlyEarned.value = response.data.data.total_earned;

    totalEarnedYear.value = response.data.data.total_earned_year;
    isMonthly.value = true;
  } catch (error) {
    console.log(error);
  }
};

watch(isMonthly, (value) => {
  if (value) {
    series.value[0].data = parseEarningsData({
      January: monthlyEarned.value.January,
      February: monthlyEarned.value.February,
      March: monthlyEarned.value.March,
      April: monthlyEarned.value.April,
      May: monthlyEarned.value.May,
      June: monthlyEarned.value.June,
      July: monthlyEarned.value.July,
      August: monthlyEarned.value.August,
      September: monthlyEarned.value.September,
      October: monthlyEarned.value.October,
      November: monthlyEarned.value.November,
      December: monthlyEarned.value.December,
    });
    category.value = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
  } else {
    series.value[0].data = parseEarningsData({
      Monday: totalEarned.value.Monday,
      Tuesday: totalEarned.value.Tuesday,
      Wednesday: totalEarned.value.Wednesday,
      Thursday: totalEarned.value.Thursday,
      Friday: totalEarned.value.Friday,
      Saturday: totalEarned.value.Saturday,
      Sunday: totalEarned.value.Sunday,
    });
    category.value = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
  }
});

const filterTo = (type) => {
  currentType.value = type;
  if (isMonthly.value) {
    getMonthlySales();
  } else {
    getWeeklyEarned();
  }
};


watch(currentType, () => {
  isMonthly.value=false;
});

onMounted(() => {
  getWeeklyEarned();
});
</script>
