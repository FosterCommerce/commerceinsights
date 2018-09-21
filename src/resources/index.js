import axios from 'axios'
import qs from 'qs'
import moment from 'moment'
import Chart from './Chart'
import getDateRange from './getDateRange'
import { select, selectAll } from './utils'

const el = document.getElementById('chart')
const chartData = JSON.parse(el.dataset.chartData)
const chartStart = el.dataset.chartStart
const chartEnd = el.dataset.chartEnd
const chartShowsCurrency = el.dataset.chartShowsCurrency

const chart = new Chart(el)
chart.update(chartStart, chartEnd, chartData)
chart.currency(chartShowsCurrency)
chart.draw()

const setDates = value => {
  const range = getDateRange(value)
  const startDate = $('[name="start[date]"]')
  const endDate = $('[name="end[date]"]')
  startDate.datepicker('setDate', range[0])
  endDate.datepicker('setDate', range[1])

  loadData()
}

selectAll('#sidebar-ranges a').forEach(el =>
  el.addEventListener('click', event => {
    const current = select('#sidebar-ranges a.sel')
    if (current) {
      current.classList.toggle('sel')
    }
    event.target.classList.toggle('sel')

    setDates(event.target.dataset.range)
  }),
)

select('#range').addEventListener('change', () => {
  const value = select('#range option:checked').value
  setDates(value)
})

const loadData = () => {
  const formatter = document.querySelector('*[name="formatter"]').value
  const startDate = moment($('[name="start[date]"]').datepicker('getDate'))
    .startOf('day')
    .toISOString(true)
  const endDate = moment($('[name="end[date]"]').datepicker('getDate'))
    .endOf('day')
    .toISOString(true)

  const params = {
    start: startDate,
    end: endDate,
    q: select('*[name="q"]').value,
  }

  const queryString = qs.stringify(params)

  const requestUrl = `/admin/commerceinsights/${formatter}?${queryString}`
  const headers = { 'X-Requested-With': 'XMLHttpRequest' }
  axios
    .get(`${requestUrl}&format=json`, { headers })
    .then(function(res) {
      chart.update(res.data.min, res.data.max, res.data.chartData)
      chart.currency(res.data.chartShowsCurrency)
      chart.draw()

      document.querySelector('.elements').innerHTML = res.data.chartTable

      let totalsHTML = ''
      for (const key in res.data.totals) {
        const value = res.data.totals[key]
        totalsHTML += `<li>
    <div class="ci-total-label">${key}</div> <strong class="ci-total-value">${value}</strong>
</li>`
      }
      document.querySelector('.ci-totals').innerHTML = totalsHTML
    })
    .catch(function(err) {
      // TODO: Handle this
      console.log('ERR', err)
    })
    .finally(function() {
      history.replaceState({}, '', requestUrl)

      const links = document.querySelectorAll('[data-dynamic-link]')
      links.forEach(function(link) {
        let query, path
        const config = JSON.parse(link.dataset.dynamicLink)
        if (config && config.query) {
          query = qs.stringify(Object.assign({}, params, config.query))
        } else {
          query = qs.stringify(params)
        }
        if (config && config.path) {
          path = config.path
        } else {
          path = ''
        }
        link.setAttribute('href', path + '?' + query)
      })
    })
}

select('.ci-actions').addEventListener('submit', function(event) {
  event.preventDefault()
  loadData()
})
