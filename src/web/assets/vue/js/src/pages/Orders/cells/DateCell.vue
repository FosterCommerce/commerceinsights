<template>
  <span>{{ date }}</span>
</template>

<script>
import format from 'date-fns/format'

export default {
  name: 'RowCellLink',
  props: {
    column: Object,
    data: Object,
  },
  computed: {
    date: ({ column, data }) => format(data[column.key], 'MM/DD/YYYY'),
  },
}
</script>
