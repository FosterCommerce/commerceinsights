<template>
  <Layout title="Products">
    <template slot="actionButton">
      <RangeSelect v-model="state.range" />
      <Date @input="startDateChanged" :value="state.startDate" />
      <Date @input="endDateChanged" :value="state.endDate" />
      <ActionButton label="Save Report" icon="add" @click="toggleModal" />
    </template>

    <SaveReportModal
      v-if="saveModalVisible"
      report-type="products"
      :query="this.state.query"
      @hide="toggleModal"
    />

    <div class="ci-spacing">
      <div class="ci-actions">
        <div class="ci-action-search">
          <QueryInput name="q" v-model="state.search" />
        </div>
      </div>

      <ProductsTable />
    </div>
  </Layout>
</template>

<script>
import Vue from 'vue'
import format from 'date-fns/format'
import parse from 'date-fns/parse'
import subDays from 'date-fns/sub_days'
import state from './state'
import ProductsTable from './Table'
import Layout from '../../components/craft/Layout'
import Date from '../../components/craft/forms/Date'
import RangeSelect from '../../components/RangeSelect'
import ActionButton from '../../components/craft/ActionButton'
import SaveReportModal, {mixin as saveReportMixin} from '../../components/SaveReportModal'
import QueryInput from '../../components/QueryInput'
import savedReport from '../../mixins/savedReport'

export default {
  name: 'Products',
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
        .get('products', { params: this.state.query })
        .then(({ data }) => {
          this.shouldUpdate = false
          this.state.startDate = data.min
          this.state.endDate = format(subDays(parse(data.max), 1), 'YYYY-MM-DD')
          this.state.results = data.tableData
          this.state.range = data.range
          this.state.search = data.search

          Vue.nextTick().then(() => (this.shouldUpdate = true))
        })
    },
  },
  components: {
    ProductsTable,
    Layout,
    Date,
    RangeSelect,
    QueryInput,
    ActionButton,
    SaveReportModal,
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
