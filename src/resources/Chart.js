import parseDate from 'date-fns/parse'
import startOfHour from 'date-fns/start_of_hour'
import startOfDay from 'date-fns/start_of_day'
import startOfWeek from 'date-fns/start_of_week'
import startOfMonth from 'date-fns/start_of_month'
import differenceInDays from 'date-fns/difference_in_days'
import differenceInMonths from 'date-fns/difference_in_months'
import format from 'date-fns/format'
import subDays from 'date-fns/sub_days'
import groupBy from 'lodash/groupBy'

const calculateInterval = (min, max, iso = false) => {
  const days = differenceInDays(max, min)
  const months = differenceInMonths(max, min)

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
  const groupedResults = groupBy(data, day =>
    format(startOf(interval, parseDate(day.x)), 'YYYY-MM-DDTHH:mm:ss.SSSZ'),
  )

  const result = Object.keys(groupedResults).map(key => ({
    x: key,
    y: groupedResults[key].reduce((acc, result) => acc + result.y, 0),
  }))

  return result
}

export default class SomeChartLib {
  constructor(el) {
    this.el = el
    this.chartObj = false
    this._min = subDays(new Date(), 1)
    this._max = new Date()
    this._currency = false
  }

  update(min, max, data) {
    this._min = parseDate(min)
    this._max = parseDate(max)
    this._data = groupData(this._min, this._max, data)
  }

  currency(currency) {
    this._currency = currency
  }

  yAxesCallback(value, index, values) {
    return (this._currency ? '$' : '') + value
  }

  draw() {
    if (this.chartObj) {
      this.chartObj.data.datasets[0].data = this._data
      this.chartObj.options.scales.xAxes[0].time.min = format(
        this._min,
        'YYYY-MM-DDTHH:mm:ss.SSSZ',
      )
      this.chartObj.options.scales.xAxes[0].time.max = format(
        this._max,
        'YYYY-MM-DDTHH:mm:ss.SSSZ',
      )
      this.chartObj.options.scales.xAxes[0].time.unit = calculateInterval(
        this._min,
        this._max,
      )
      this.chartObj.update()

      return
    }

    import('chart.js')
      .then(({ Chart }) => {
        this.chartObj = new Chart(this.el, {
          type: 'line',
          data: {
            datasets: [
              {
                label: '',
                lineTension: 0,
                data: this._data,
                backgroundColor: '#36a2eb',
              },
            ],
          },
          options: {
            legend: {
              display: false,
            },
            scales: {
              yAxes: [
                {
                  ticks: {
                    beginAtZero: true,
                    callback: this.yAxesCallback.bind(this),
                  },
                },
              ],
              xAxes: [
                {
                  type: 'time',
                  time: {
                    min: format(this._min, 'YYYY-MM-DDTHH:mm:ss.SSSZ'),
                    max: format(this._max, 'YYYY-MM-DDTHH:mm:ss.SSSZ'),
                    unit: calculateInterval(this._min, this._max),
                  },
                },
              ],
            },
          },
        })
      })

    return this
  }
}
