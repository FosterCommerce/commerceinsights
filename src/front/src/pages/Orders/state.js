import Vue from 'vue'
import rowCellLink from './cells/RowCellLink'
import DateCell from './cells/DateCell'
import capitalize from '../../util/capitalize'

const OrderNumberCell = rowCellLink({
  urlF: (column, data) => `/admin/commerce/orders/${data.id}`,
  tooltipF: (column, data) => data.number,
  labelF: (column, data) => data.number.slice(0, 8),
})

export default new Vue({
  data: () => ({
    formatter: 'revenue',
    range: null,
    tableColumns: [
      {
        key: 'dateOrdered',
        label: 'Date',
        sortable: true,
        sortKey: 'dateOrdered',
        component: DateCell,
      },
      {
        key: 'shortNumber',
        label: 'Number',
        sortable: false,
        component: OrderNumberCell,
      },
      { key: 'orderStatus', label: 'Status', format: capitalize },
      {
        key: 'totalPrice',
        label: 'Base Price',
        sortable: true,
        sortKey: 'totalPrice',
      },
      { key: 'totalTax', label: 'Tax', sortable: true, sortKey: 'totalTax' },
      {
        key: 'totalDiscount',
        label: 'Discount',
        sortable: true,
        sortKey: 'totalDiscount',
      },
      {
        key: 'totalPaid',
        label: 'Total Paid',
        sortable: true,
        sortKey: 'totalPaid',
      },
      {
        key: 'paidStatus',
        label: 'Paid Status',
        sortable: true,
        sortKey: 'paidStatus',
        format: capitalize,
      },
      { key: 'email', label: 'Email' },
    ],
    tableSortBy: null,
    tableSortOrder: 'desc',
    chartData: [],
    totals: null,
    startDate: null,
    endDate: null,
    results: [],
    statuses: [],
    status: null,
    search: {},
  }),
  computed: {
    query: vm => {
      const query = {
        sort: vm.tableSortBy ? vm.tableSortBy.sortKey : null,
        dir: vm.tableSortOrder,
        status: vm.status,
        search: vm.search,
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
