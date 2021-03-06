import axios from 'axios'
import qs from 'qs'
import startOfDay from 'date-fns/start_of_day'
import endOfDay from 'date-fns/end_of_day'
import format from 'date-fns/format'
import debounce from 'lodash/debounce'
import Chart from './Chart'
import getDateRange from './getDateRange'
import { select, selectAll } from './utils'

$(function() {
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

    if (range.length > 0) {
      const startDate = $('[name="start[date]"]')
      const endDate = $('[name="end[date]"]')
      startDate.datepicker('setDate', range[0])
      endDate.datepicker('setDate', range[1])

      loadData()
    }
  }

  const addQuery = ({ name, value }) => {
    const dataEl = select('#q-data')
    const queryData = JSON.parse(dataEl.dataset.queryData)
    dataEl.dataset.queryData = JSON.stringify({
      ...queryData,
      [name]: value,
    })

    select('*[name="q"]').value = dataEl.dataset.queryData
    debounce(loadData, 2000)()
  }

  const loadData = () => {
    const formatter = document.querySelector('*[name="formatter"]').value
    const startDate = format(
      startOfDay($('[name="start[date]"]').datepicker('getDate')),
      'YYYY-MM-DD',
    )
    const endDate = format(
      endOfDay($('[name="end[date]"]').datepicker('getDate')),
      'YYYY-MM-DD',
    )

    // Get additional query params
    const extra = {}
    selectAll('.query-extra, .query-extra > *').forEach(el => {
      extra[el.name] = el.value
    })

    const selectedRange = select('#range').value

    const range =
      selectedRange === ''
        ? { start: startDate, end: endDate }
        : { range: selectedRange }

    const params = {
      ...extra,
      ...range,
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

        select('.elements').innerHTML = res.data.chartTable
        init(select('.elements'))

        if (res.data.summary) {
          const summaryEl = select('.ci-summary')
          if (summaryEl) {
            summaryEl.innerHTML = res.data.summary
          }
        }

        if (res.data.totals) {
          const totalsEl = select('.ci-totals')
          if (totalsEl) {
            totalsEl.innerHTML = res.data.totals
          }
        }
      })
      .catch(function(err) {
        // TODO: Handle this
        console.log('ERR', err)
      })
      .finally(function() {
        history.replaceState({}, '', requestUrl)

        const links = selectAll('[data-dynamic-link]')
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

  const init = scope => {
    selectAll('[name="q"], .query-extra, .query-extra > *', scope).forEach(el =>
      el.addEventListener('input', debounce(loadData, 2000)),
    )

    selectAll('[name="start[date]"],[name="end[date]"]', scope).forEach(el => {
      const debouncedLoad = debounce(loadData, 2000)
      $(el).datepicker('option', {
        onSelect: () => {
          select('#range option[value=""]').selected = true
          debouncedLoad()
        },
      })
    })

    selectAll('#sidebar-ranges a', scope).forEach(el =>
      el.addEventListener('click', event => {
        const current = select('#sidebar-ranges a.sel')
        if (current) {
          current.classList.toggle('sel')
        }
        event.target.classList.toggle('sel')

        setDates(event.target.dataset.range)
      }),
    )

    selectAll('.ci-clickable-query', scope).forEach(el =>
      el.addEventListener('click', event => {
        addQuery({
          name: el.dataset.qName,
          value: el.dataset.qValue,
        })

        event.preventDefault()
      }),
    )

    select('#range', scope).addEventListener('change', () => {
      const value = select('#range option:checked').value
      setDates(value)
    })

    select('.ci-actions', scope).addEventListener('submit', function(event) {
      event.preventDefault()
      loadData()
    })
  }

  init()
})
