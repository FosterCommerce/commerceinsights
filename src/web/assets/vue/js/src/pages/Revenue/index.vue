<template>
  <Layout title="Revenue">
    <template v-slot:actionButton>
      <RangeSelect v-model="state.range" />
      <Date @input="startDateChanged" :value="state.startDate" />
      <Date @input="endDateChanged" :value="state.endDate" />
      <ActionButton label="Save Report" icon="add" @click="toggleModal" />
    </template>

    <SaveReportModal
      v-if="saveModalVisible"
      report-type="revenue"
      :query="this.state.query"
      @hide="toggleModal"
    />

    <nav slot:sidebar>
      <ul id="sidebar-ranges">
        <li><a href="#" @click="setRange('today')">Today</a></li>
        <li><a href="#" @click="setRange('this-week')">This Week</a></li>
        <li><a href="#" @click="setRange('this-month')">This Month</a></li>
        <li><a href="#" @click="setRange('this-year')">This Year</a></li>
      </ul>
    </nav>

    <div class="ci-spacing">
      <h2>Entire Store</h2>

      <p class="ci-summary" v-html="state.summary"></p>

      <div class="ci-totals">
        <ul>
          <li v-for="(value, key) in state.totals" :key="key">
            <div class="ci-total-label">{{ key }}</div>
            <strong class="ci-total-value">{{ value }}</strong>
          </li>
        </ul>
      </div>

      <Chart
        :chart-data="state.chartData"
        :secondary-chart-data="state.secondaryChartData"
        :start="state.startDate"
        :end="state.endDate"
      />

      <form method="get" class="ci-actions">
        <input type="hidden" name="formatter" :value="state.formatter" />

        <div class="ci-action-search"><!-- Search field --></div>

        <div class="ci-action-actions">
          <input type="hidden" name="startDate" value="" />
          <input type="hidden" name="endDate" value="" />
        </div>
      </form>
    </div>
  </Layout>
</template>

<script>
import Vue from 'vue'
import format from 'date-fns/format'
import parse from 'date-fns/parse'
import subDays from 'date-fns/sub_days'
import state from './state'
import Layout from '../../components/craft/Layout'
import Date from '../../components/craft/forms/Date'
import RangeSelect from '../../components/RangeSelect'
import ActionButton from '../../components/craft/ActionButton'
import SaveReportModal, {mixin as saveReportMixin} from '../../components/SaveReportModal'
import Chart from '../../components/DateLineChart'
import savedReport from '../../mixins/savedReport'

export default {
  name: 'Revenue',
  mixins: [saveReportMixin, savedReport],
  data: () => ({
    state,
    shouldUpdate: true,
  }),
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
        .get('revenue', { params: this.state.query })
        .then(({ data }) => {
          this.shouldUpdate = false
          this.state.chartData = data.chartData
          this.state.secondaryChartData = data.secondaryChartData
          this.state.startDate = data.min
          this.state.endDate = format(subDays(parse(data.max), 1), 'YYYY-MM-DD')
          this.state.totals = data.totals
          this.state.summary = data.summary
          this.state.range = data.range

          Vue.nextTick().then(() => (this.shouldUpdate = true))
        })
    },
  },
  components: {
    Layout,
    Date,
    RangeSelect,
    Chart,
    ActionButton,
    SaveReportModal,
  },
}
</script>

<style scoped>
h2 {
  margin: 14px 0;
  font-size: 16px;
  font-weight: bold;
  line-height: 20px;
}

p {
  margin: 1em 0;
}

.ci-totals > ul {
  background: #fbfcfd;
  border: 1px solid #e3e5e8;
  padding: 10px;
  display: flex;
}

.ci-spacing > * + * {
  margin-top: 30px;
}

.ci-totals > ul > * {
  flex-grow: 1;
  width: 100%;
  text-align: center;
}

.ci-total-label {
  font-size: 10px;
  text-transform: uppercase;
}

.ci-total-value {
  font-size: 20px;
}
</style>
