import Vue from 'vue'

const ProductCell = {
  name: 'ProductCell',
  render() {
    return (
      <abbr>
        <span class={{status: true, disabled: !this.data.enabled, live: this.data.enabled}}></span>
        <a href={`/admin/commerce/products/${this.data.productTypeHandle}/${this.data.productId}`}>
          {this.data.productTitle}
        </a>
      </abbr>
    )
  },
  props: {
    data: Object,
  },
}


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
    productFilter: null,
  }),
  computed: {
    query: vm => {
      const query = {
        sort: vm.tableSortBy ? vm.tableSortBy.sortKey : null,
        dir: vm.tableSortOrder,
        search: vm.search,
        productFilter: vm.productFilter,
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
    dynamicColumns: ({tableColumns, productFilter}) => {
      return tableColumns.filter(col => {
        if (col.key === 'sku' && productFilter === 'all') {
          return false
        }

        return true
      })
    }
  },
})
