<template>
  <div class="elements">
    <CraftTable
      :columns="state.tableColumns"
      :data="state.results"
      :sort-by="state.tableSortBy"
      :sort-order="state.tableSortOrder"
      @sort-changed="sortChanged"
    />
  </div>
</template>

<script>
import state from './state'
import CraftTable from '../../components/craft/Table'

export default {
  name: 'RevenueTable',
  data: () => ({ state }),
  computed: {
    sortOrder: ({ state }) => ({
      sort: state.query.sort,
      dir: state.query.dir,
    }),
  },
  methods: {
    sortChanged(column) {
      const state = this.state
      if (state.tableSortBy === column) {
        state.tableSortOrder = state.tableSortOrder === 'desc' ? 'asc' : 'desc'
      } else {
        state.tableSortBy = column
        state.tableSortOrder = 'desc'
      }
    },
  },
  components: {
    CraftTable,
  },
}
</script>
