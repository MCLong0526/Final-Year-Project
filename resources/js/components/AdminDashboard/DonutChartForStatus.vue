<script setup>
import { hexToRgb } from '@layouts/utils';
import { defineProps, ref } from 'vue';
import VueApexCharts from 'vue3-apexcharts';
import { useTheme } from 'vuetify';

const props = defineProps({

  isActiveUsers: {
    type: Number,
    required: true,
  },
  isInactiveUsers: {
    type: Number,
    required: true,
  },
  activeUserPercentage: {
    type: String,
    required: true,
  },
})

const colorVariables = themeColors => {
  const themeSecondaryTextColor = `rgba(${hexToRgb(themeColors.colors['on-surface'])},${themeColors.variables['medium-emphasis-opacity']})`
  const themeDisabledTextColor = `rgba(${hexToRgb(themeColors.colors['on-surface'])},${themeColors.variables['disabled-opacity']})`
  const themeBorderColor = `rgba(${hexToRgb(String(themeColors.variables['border-color']))},${themeColors.variables['border-opacity']})`
  const themePrimaryTextColor = `rgba(${hexToRgb(themeColors.colors['on-surface'])},${themeColors.variables['high-emphasis-opacity']})`
  
  return { themeSecondaryTextColor, themeDisabledTextColor, themeBorderColor, themePrimaryTextColor }
}

const vuetifyTheme = useTheme()

const series = ref([])

const checkSeries = () => {
  if (props.isActiveUsers === 0 && props.isInactiveUsers === 0) {
    series.value = ([])
  } else {
    series.value = ([props.isActiveUsers, props.isInactiveUsers])
  }
}

checkSeries()


const getDonutChartConfig = themeColors => {

  const donutColors = {
    series1: '#557cf2',
    series2: '#334a91',
  }

  const { themeSecondaryTextColor, themePrimaryTextColor } = colorVariables(themeColors)
  
  return {
    stroke: { width: 0 },
    labels: ['Active Users', 'Inactive Users'],
    colors: [donutColors.series1, donutColors.series2],
    dataLabels: {
      enabled: true,
      formatter: val => `${parseInt(val, 10)}%`,
    },
    legend: {
      position: 'bottom',
      markers: { offsetX: -3 },
      labels: { colors: themeSecondaryTextColor },
      itemMargin: {
        vertical: 3,
        horizontal: 10,
      },
    },
    noData:{
      text: 'No Data',
      align: 'center',
      verticalAlign: 'middle',
      style: {
        color: themeSecondaryTextColor,
        fontSize: '14px',
        fontFamily: 'Roboto',
      },
  
      
    },
    
    plotOptions: {
      pie: {
        donut: {
          labels: {
            show: true,
            name: {
              fontSize: '1.5rem',
            },
            value: {
              fontSize: '1.5rem',
              color: themeSecondaryTextColor,
              formatter: val => `${parseInt(val, 10)}`,
            },
            total: {
              show: true,
              fontSize: '1.0rem',
              label: 'Active Users',
              formatter: () => props.activeUserPercentage,
              color: themePrimaryTextColor,
            },
          },
        },
      },
    },
    responsive: [
      {
        breakpoint: 992,
        options: {
          chart: {
            height: 380,
          },
          legend: {
            position: 'bottom',
          },
        },
      },
      {
        breakpoint: 576,
        options: {
          chart: {
            height: 320,
          },
          plotOptions: {
            pie: {
              donut: {
                labels: {
                  show: true,
                  name: {
                    fontSize: '1rem',
                  },
                  value: {
                    fontSize: '1rem',
                  },
                  total: {
                    fontSize: '1rem',
                  },
                },
              },
            },
          },
        },
      },
    ],
  }
}

const expenseRationChartConfig = computed(() => getDonutChartConfig(vuetifyTheme.current.value))
</script>

<template>
  <VueApexCharts

    type="donut"
    height="300"
    :options="expenseRationChartConfig"
    :series="series"
  />
</template>
