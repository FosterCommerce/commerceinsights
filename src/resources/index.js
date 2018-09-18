import Chart from './Chart'
import axios from 'axios'
import qs from 'qs'
import qsUtils from 'qs/lib/utils'

const el = document.getElementById("chart")
const chartData = JSON.parse(el.dataset.chartData)
const chartStart = el.dataset.chartStart
const chartEnd = el.dataset.chartEnd
const chartShowsCurrency = el.dataset.chartShowsCurrency


const chart = new Chart(el)
chart.data(chartData)
chart.min(chartStart)
chart.max(chartEnd)
chart.currency(chartShowsCurrency)
chart.draw()

// When the range changes submit the data
document.addEventListener('change', function(event) {
    if (event.target.name !== 'range') {
        return
    }

    document.querySelector('label[for="filter-toggle"]').innerHTML = event.target.querySelector('option:checked').innerHTML

    event.target.form.querySelector('*[type="submit"]').click()
})

document.addEventListener('submit', function(event) {

    // make sure it's the right form
    if (!event.target.classList.contains('ci-actions')) {
        return
    }

    // stop form submission, for ajax
    event.preventDefault()

    // bundle up the form data
    let params = {}
    params.formatter = document.querySelector('*[name="formatter"]').value
    params.range = document.querySelector('*[name="range"]').value
    params.start = document.querySelector('*[name="start[date]"]').value
    params.end = document.querySelector('*[name="end[date]"]').value
    params.q = document.querySelector('*[name="q"]').value
    const queryString = qs.stringify(params)

    // submit the data
    const headers = {'X-Requested-With': 'XMLHttpRequest'}
    axios.get('/admin/commerceinsights?'+queryString, {headers})
        .then(function(res) {
            chart.data(res.data.chartData)
            chart.min(res.data.min)
            chart.max(res.data.max)
            chart.currency(res.data.chartShowsCurrency)
            chart.draw()

            document.querySelector('.elements').innerHTML = res.data.chartTable

            let totalsHTML = ''
            for (const key in res.data.totals) {
                const value = res.data.totals[key];
                totalsHTML += `<li>
                    <div class="ci-total-label">${key}</div> <strong class="ci-total-value">${value}</strong>
                </li>`
            }
            document.querySelector('.ci-totals').innerHTML = totalsHTML;
        })
        .catch(function (err) {
            console.log('ERR', err)
        })
        .finally(function() {
            document.querySelector('#filter-toggle').checked = false
            history.replaceState({}, '', '/admin/commerceinsights?'+queryString)

            const links = document.querySelectorAll('[data-dynamic-link]')
            links.forEach(function (link) {
                let query, path
                const config = JSON.parse(link.dataset.dynamicLink)
                if (config && config.query) {
                    query = qs.stringify(Object.assign({}, params, config.query))
                }
                else {
                    query = qs.stringify(params)
                }
                if (config && config.path) {
                    path = config.path
                }
                else {
                    path = ''
                }
                link.setAttribute('href', path + '?' + query)
            })
        })
})