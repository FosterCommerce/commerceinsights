<template>
  <Layout title="Orders">
    <template slot="actionButton">
      <RangeSelect v-model="state.range" />
      <Date @input="startDateChanged" :value="state.startDate" />
      <Date @input="endDateChanged" :value="state.endDate" />
    </template>

    <div class="ci-spacing">
      <div class="ci-actions">
        <Dropdown v-model="state.status" :data="state.statuses" />
        <div class="ci-action-search">
          <QueryInput name="q" v-model="state.search" />
        </div>
        <!--
          <div class="ci-action-actions">
            <a href="/admin/commerceinsights/orders/csv" class="btn">Export</a>
          </div>
        -->
      </div>

      <div class="summary-header">
        <Totals :totals="state.totals" />

        <Chart
          :chart-data="state.chartData"
          :start="state.startDate"
          :end="state.endDate"
        />
      </div>

      <form method="get" class="ci-actions">
        <input type="hidden" name="formatter" :value="state.formatter" />

        <div class="ci-action-search"><!-- Search field --></div>

        <div class="ci-action-actions">
          <input type="hidden" name="startDate" value="" />
          <input type="hidden" name="endDate" value="" />
        </div>
      </form>

      <OrdersTable />
    </div>
  </Layout>
</template>

<script>
import Vue from 'vue'
import format from 'date-fns/format'
import parse from 'date-fns/parse'
import subDays from 'date-fns/sub_days'
import state from './state'
import OrdersTable from './Table'
import Totals from './Totals'
import Layout from '../../components/craft/Layout'
import Date from '../../components/craft/forms/Date'
import RangeSelect from '../../components/RangeSelect'
import Dropdown from '../../components/craft/forms/Dropdown'
import QueryInput from '../../components/QueryInput'
import Chart from '../../components/DateLineChart'

export default {
  name: 'Revenue',
  data: () => ({
    state,
    shouldUpdate: true,
  }),
  mounted() {
    this.update()
  },
  watch: {
    'state.query': function() {
      if (this.shouldUpdate) {
        this.update()
      }
    },
  },
  methods: {
    setRange(range) {
      this.state.range = range
    },
    startDateChanged(value) {
      this.state.range = ''
      this.state.startDate = format(value, 'YYYY-MM-DD')
    },
    endDateChanged(value) {
      this.state.range = ''
      this.state.endDate = format(value, 'YYYY-MM-DD')
    },
    update() {
      this.$axios
        .get('orders', { params: this.state.query })
        .then(({ data }) => {
          this.shouldUpdate = false
          this.state.chartData = data.chartData
          this.state.startDate = data.min
          this.state.endDate = format(subDays(parse(data.max), 1), 'YYYY-MM-DD')
          this.state.results = data.tableData
          this.state.totals = data.totals
          this.state.summary = data.summary
          this.state.range = data.range
          this.state.totals = data.totals
          this.state.statuses = data.statuses
          this.state.search = data.search

          Vue.nextTick().then(() => (this.shouldUpdate = true))
        })
    },
  },
  components: {
    OrdersTable,
    Layout,
    Date,
    RangeSelect,
    Chart,
    Totals,
    Dropdown,
    QueryInput,
  },
}
</script>

<style scoped>
.ci-actions {
  display: flex;
  align-items: baseline;
}

.ci-action-search {
  flex-grow: 1;
}

[class^='ci-action-'] {
  padding: 0 10px;
}

.summary-header {
  display: grid;
  grid-auto-flow: column;
  grid-template-columns: 1fr 2fr;
}

.chart {
  margin-right: 0;
  padding-left: 2%;
}

.ci-spacing > * + * {
  margin-top: 30px;
}
</style>
