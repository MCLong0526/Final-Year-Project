<script setup>
import { hexToRgb } from '@layouts/utils';
import VueApexCharts from 'vue3-apexcharts';
import { useTheme } from 'vuetify';


const props = defineProps({
  itemData: {
    type: Object,
    required: true,
  },
})

const highestPercentage = ref(0)

const vuetifyTheme = useTheme()

const colorVariables = themeColors => {
  const themeSecondaryTextColor = `rgba(${hexToRgb(themeColors.colors['on-surface'])},${themeColors.variables['medium-emphasis-opacity']})`
  const themeDisabledTextColor = `rgba(${hexToRgb(themeColors.colors['on-surface'])},${themeColors.variables['disabled-opacity']})`
  const themeBorderColor = `rgba(${hexToRgb(String(themeColors.variables['border-color']))},${themeColors.variables['border-opacity']})`
  const themePrimaryTextColor = `rgba(${hexToRgb(themeColors.colors['on-surface'])},${themeColors.variables['high-emphasis-opacity']})`
  
  return { themeSecondaryTextColor, themeDisabledTextColor, themeBorderColor, themePrimaryTextColor }
}


const getRadialBarChartConfig = themeColors => {
  const radialBarColors = {
    series1: '#fdd835',
    series2: '#32baff',
    series3: '#00d4bd',
    series4: '#7367f0',
    series5: '#FFA1A1',
  }

  const { themeSecondaryTextColor, themePrimaryTextColor } = colorVariables(themeColors)
  
  return {
    stroke: { lineCap: 'round' },
    labels: Array.isArray(props.itemData) ? props.itemData.map(item => item.name) : [],
    legend: {
      show: true,
      position: 'bottom',
      labels: {
        colors: themeSecondaryTextColor,
      },
      markers: {
        offsetX: -3,
      },
      itemMargin: {
        vertical: 3,
        horizontal: 10,
      },
    },
    colors: [radialBarColors.series1, radialBarColors.series2, radialBarColors.series4, radialBarColors.series5],
    plotOptions: {
      radialBar: {
        hollow: { size: '12%' },
        track: {
          margin: 10,
          background: themeColors.colors['grey-100'],
        },
        dataLabels: {
          name: {
            fontSize: '0.5rem',
          },
          value: {
            fontSize: '0.8rem',
            color: themeSecondaryTextColor,
          },
          total: {
            show: true,
            fontWeight: 400,
          
            label: 'Highest',
            fontSize: '0.8rem',
            color: themePrimaryTextColor,
            formatter() {
              //assigned the highest value in the series to totalValue
              return highestPercentage.value + '%'

            },
          },
        },
      },
    },
    grid: {
      padding: {
        top: -30,
        bottom: -30,
      },
    },
  }
}

const statisticsChartConfig = computed(() => getRadialBarChartConfig(vuetifyTheme.current.value))

//assign the props.itemData percentage values to series
const series = computed(() => {
  const series = Array.isArray(props.itemData) ? props.itemData.map(item => item.percentage) : []
  highestPercentage.value = Math.max(...series)
  return series
})

</script>

<template>
  <VueApexCharts
    type="radialBar"
    height="330"
    :options="statisticsChartConfig"
    :series="series"
  />
</template>
