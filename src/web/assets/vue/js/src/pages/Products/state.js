import Vue from 'vue'
import rowCellLink from './cells/RowCellLink'

const ProductCell = rowCellLink({
  urlF: (column, data) => `/admin/commerce/products/${data.productTypeHandle}/${data.productId}`,
  labelF: (column, data) => data.productTitle,
})

const subTotalColumn = {
  key: 'subTotal',
  label: 'Revenue',
  sortable: true,
  sortKey: 'subTotal',
}

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
      {
        key: 'orderCount',
        label: 'Total Orders',
        sortable: true,
        sortKey: 'orderCount',
      },
      {
        key: 'qty',
        label: 'Total Sold',
        sortable: true,
        sortKey: 'qty',
      },
      subTotalColumn,
    ],
    tableSortBy: subTotalColumn,
    tableSortOrder: 'desc',
    startDate: null,
    endDate: null,
    results: [],
    search: {},
  }),
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
