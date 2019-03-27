<template>
  <div class="chart">
    <canvas></canvas>
  </div>
</template>

<script>
import Chart from 'chart.js'
import parseDate from 'date-fns/parse'
import startOfHour from 'date-fns/start_of_hour'
import startOfDay from 'date-fns/start_of_day'
import startOfWeek from 'date-fns/start_of_week'
import startOfMonth from 'date-fns/start_of_month'
import differenceInDays from 'date-fns/difference_in_days'
import differenceInMonths from 'date-fns/difference_in_months'
import format from 'date-fns/format'
import addDays from 'date-fns/add_days'
import groupBy from 'lodash/groupBy'

const calculateInterval = (min, max, iso = false) => {
  const days = differenceInDays(addDays(max, 1), min)
  const months = differenceInMonths(addDays(max, 1), min)

  if (days <= 2) {
    return iso ? 'isoHour' : 'hour'
  } else if (days > 2 && months <= 2) {
    return iso ? 'isoDay' : 'day'
  } else if (months > 2 && months <= 6) {
    return iso ? 'isoWeek' : 'week'
  }

  return iso ? 'isoMonth' : 'month'
}

const startOf = (interval, value) => {
  switch (interval) {
    case 'hour':
      return startOfHour(value)
    case 'day':
      return startOfDay(value)
    case 'week':
      return startOfWeek(value)
    default:
      return startOfMonth(value)
  }
}

const formatDay = (min, max, day) => {
  const interval = calculateInterval(min, max)
  return format(startOf(interval, parseDate(day)), 'YYYY-MM-DDTHH:mm:ss.SSSZ')
}

const groupData = (min, max, data) => {
  const groupedResults = groupBy(data, day => formatDay(min, max, day.x))

  const result = Object.keys(groupedResults).map(key => ({
    x: key,
    y: groupedResults[key].reduce((acc, result) => acc + result.y, 0),
  }))

  return result
}

const normalizeData = (primary, secondary) =>
  primary
    .filter((o, index) => secondary[index])
    .map((o, index) => {
      const hasSecondary = secondary[index]
      return {
        x: o.x,
        y: secondary[index].y,
        secondary: secondary[index].x,
      }
    })

const options = vm => ({
  legend: {
    display: false,
  },
  tooltips: {
    mode: 'index',
    position: 'nearest',
    intersect: false,
    callbacks: {
      label: (item, data) => {
        const {datasets} = data

        const originalItem = datasets[item.datasetIndex].data[item.index]

        let xLabel = item.xLabel
        if (originalItem.secondary) {
          xLabel = originalItem.secondary
        }

        xLabel = format(xLabel, 'MM/YYYY')
        return `${xLabel}: ${item.yLabel}`;
      },
      title: () => '',
    },
  },
  hover: {
    mode: 'index',
    intersect: true
  },
  scales: {
    yAxes: [
      {
        type: 'linear',
        position: 'left',
        id: 'primary',
        ticks: {
          beginAtZero: true,
        },
      },
    ],
    xAxes: [
      {
        type: 'time',
        time: {
          min: format(vm.min, 'YYYY-MM-DDTHH:mm:ss.SSSZ'),
          max: format(vm.max, 'YYYY-MM-DDTHH:mm:ss.SSSZ'),
          unit: calculateInterval(vm.min, vm.max),
        },
      },
    ],
  },
})

export default {
  name: 'Chart',
  data: () => ({ chart: null }),
  props: [
    'start',
    'end',
    'chartData',
    'secondaryChartData',
  ],
  computed: {
    transformedData: ({ chartData, secondaryChartData, min, max }) => {

      const groupedData = groupData(min, max, chartData)
      const datasets = {
        datasets: [
          {
            label: 'Current',
            // lineTension: 0,
            data: groupedData,
            backgroundColor: 'rgb(54, 162, 235)',
            borderColor: 'rgb(54, 162, 235)',
            fill: false,
          },
        ],
      }

      if (secondaryChartData) {
        datasets.datasets.push({
          label: 'Previous',
          display: true,
          data: normalizeData(groupedData, groupData(min, max, secondaryChartData)),
          backgroundColor: 'rgba(75, 192, 192, 0.5)',
          borderColor: 'rgba(75, 192, 192, 0.5)',
          fill: false,
        })
      }

      return datasets
    },
    min: ({ start }) => parseDate(start),
    max: ({ end }) => parseDate(end),
  },
  watch: {
    transformedData () {
      this.chart.data.datasets[0].data = this.transformedData.datasets[0].data

      if (this.secondaryChartData) {
        this.chart.data.datasets[1].data = this.transformedData.datasets[1].data
      }

      this.chart.options = options(this)
      this.chart.update()
    },
  },
  mounted () {
    this.chart = new Chart(this.$el.querySelector('canvas'), {
      type: 'line',
      data: this.transformedData,
      options: options(this),
    })
  },
}
</script>