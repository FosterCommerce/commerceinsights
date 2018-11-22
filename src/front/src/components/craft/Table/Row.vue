<template>
  <tr>
    <Cell
      v-for="(column, index) in columns"
      :key="index"
      :data="data"
      :column="column"
    />
  </tr>
</template>

<script>
import Cell from './Cell'

export default {
  name: 'TableRow',
  props: {
    data: {
      type: [Array, Object],
      default: () => [],
    },
    columns: {
      type: Array,
      required: true,
    },
  },
  computed: {
    rowData: ({ data }) =>
      Array.isArray(data)
        ? data
            .map(cell => ({ label: cell }))
            .reduce((acc, cell, index) => ({ [index]: cell }), {})
        : data,
  },
  methods: {},
  components: {
    Cell,
  },
}
</script>
