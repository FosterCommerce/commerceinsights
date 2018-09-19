import Chart from 'chart.js'
import moment from 'moment'

export default class SomeChartLib {
  constructor(el) {
    this.el = el
    this.chartObj = false
    this._min = moment().subtract(1, 'day')
    this._max = moment()
    this._currency = false
  }

  data(data) {
    this._data = data
  }

  min(min) {
    this._min = moment(min)
  }

  max(max) {
    this._max = moment(max)
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
      this.chartObj.options.scales.xAxes[0].time.unit =
        moment.duration(this._max.diff(this._min)).asDays() > 2 ? 'day' : 'hour'
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
                unit:
                  moment.duration(this._max.diff(this._min)).asDays() > 2
                    ? 'day'
                    : 'hour',
              },
            },
          ],
        },
      },
    })

    return this
  }
}
