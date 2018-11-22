<template>
  <td>
    <component
      v-if="column.component"
      :is="column.component"
      :data="rowData"
      :column="column"
    />
    <span v-else>{{ formatted }}</span>
  </td>
</template>

<script>
export default {
  name: 'TableCell',
  props: {
    data: {
      type: Object,
      required: true,
    },
    column: {
      type: Object,
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
    formatted: ({ rowData, column }) =>
      column.format ? column.format(rowData[column.key]) : rowData[column.key],
  },
  methods: {},
}
</script>
