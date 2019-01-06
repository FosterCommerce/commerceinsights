import Vue from 'vue'

export default new Vue({
  data: () => ({
    summary: 'Summary',
    totals: null,
    formatter: 'revenue',
    range: null,
    tableSortBy: null,
    tableSortOrder: 'desc',
    chartData: [],
    secondaryChartData: [],
    startDate: null,
    endDate: null,
  }),
  computed: {
    query: vm => {
      const query = {
        sort: vm.tableSortBy ? vm.tableSortBy.sortKey : null,
        dir: vm.tableSortOrder,
      }

      if (vm.range !== '') {
        return {
          ...query,
          range: vm.range,
        }
      }

      return {
        ...query,
        start: vm.startDate,
        end: vm.endDate,
      }
    },
  },
})
