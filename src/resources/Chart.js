import Chart from 'chart.js'
import moment from 'moment'
import groupBy from 'lodash/groupBy'

const calculateInterval = (min, max, iso = false) => {
  const duration = moment.duration(max.diff(min))
  const days = duration.asDays()
  const months = duration.asMonths()

  if (days <= 2) {
    return iso ? 'isoHour' : 'hour'
  } else if (days > 2 && months <= 2) {
    return iso ? 'isoDay' : 'day'
  } else if (months > 2 && months <= 6) {
    return iso ? 'isoWeek' : 'week'
  }

  return iso ? 'isoMonth' : 'month'
}

const groupData = (min, max, data) => {
  const interval = calculateInterval(min, max)
  const groupedResults = groupBy(data, day =>
    moment(day.x, 'YYYY-MM-DD hh:mm:ss')
      .startOf(interval)
      .toISOString(),
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
    this._min = moment().subtract(1, 'day')
    this._max = moment()
    this._currency = false
  }

  update(min, max, data) {
    this._min = moment(min)
    this._max = moment(max)
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
      this.chartObj.options.scales.xAxes[0].time.min = this._min.toISOString()
      this.chartObj.options.scales.xAxes[0].time.max = this._max.toISOString()
      this.chartObj.options.scales.xAxes[0].time.unit = calculateInterval(
        this._min,
        this._max,
      )
      this.chartObj.update()

      return
    }

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
                min: this._min.toISOString(),
                max: this._max.toISOString(),
                unit: calculateInterval(this._min, this._max),
              },
            },
          ],
        },
      },
    })

    return this
  }
}
