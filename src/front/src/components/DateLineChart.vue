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

const groupData = (min, max, data) => {
  const interval = calculateInterval(min, max)

  const formatDay = day =>
    format(startOf(interval, parseDate(day.x)), 'YYYY-MM-DDTHH:mm:ss.SSSZ')
  const groupedResults = groupBy(data, formatDay)

  const result = Object.keys(groupedResults).map(key => ({
    x: key,
    y: groupedResults[key].reduce((acc, result) => acc + result.y, 0),
  }))

  return result
}

const options = vm => ({
  legend: {
    display: false,
  },
  scales: {
    yAxes: [
      {
        ticks: {
          beginAtZero: true,
          callback: value => {
            return value
          }, //this.yAxesCallback.bind(this),
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
    'chartData'
  ],
  // render() {
  //   return (
  //     <Chart
  //       chartData={this.transformedData}
  //       options={this.options}
  //       min={this.min}
  //       max={this.max}
  //       width={900}
  //     />
  //   )
  // },
  computed: {
    transformedData: ({ chartData, min, max }) => ({
      datasets: [
        {
          label: '',
          lineTension: 0,
          data: groupData(min, max, chartData),
          backgroundColor: '#36a2eb',
        },
      ],
    }),
    min: ({ start }) => parseDate(start),
    max: ({ end }) => parseDate(end),
  },
  watch: {
    transformedData () {
      this.chart.data.datasets[0].data = this.transformedData.datasets[0].data
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