import Vue from 'vue'
import rowCellLink from './cells/RowCellLink'

const ProductCell = rowCellLink({
  urlF: (column, data) => `/admin/commerce/products/${data.productTypeHandle}/${data.productId}`,
  labelF: (column, data) => data.productTitle,
})

export default new Vue({
  data: () => ({
    formatter: 'revenue',
    range: null,
    tableColumns: [
      {
        key: 'productTitle',
        label: 'Name',
        sortable: false,
        component: ProductCell,
      },
      {
        key: 'sku',
        label: 'SKU',
        sortable: false,
      },
      {
        key: 'stock',
        label: 'Stock Left',
        sortable: true,
        sortKey: 'stock',
      },
      // {
      //   key: 'qtyOrders',
      //   label: 'Total Orders',
      //   sortable: true,
      //   sortKey: 'qtyOrders',
      // },
      // {
      //   key: 'qtySold',
      //   label: 'Total Sold',
      //   sortable: true,
      //   sortKey: 'qtySold',
      // },
      // {
      //   key: 'revenue',
      //   label: 'Revenue',
      //   sortable: true,
      //   sortKey: 'revenue',
      // },
    ],
    tableSortBy: null,
    tableSortOrder: 'desc',
    startDate: null,
    endDate: null,
    results: [],
    search: {},
  }),
  mounted() {
    this.tableSortBy = this.tableColumns.find(col => col.key === 'revenue')
  },
  computed: {
    query: vm => {
      const query = {
        sort: vm.tableSortBy ? vm.tableSortBy.sortKey : null,
        dir: vm.tableSortOrder,
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
