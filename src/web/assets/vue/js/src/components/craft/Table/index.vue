<template>
  <table class="data fullwidth">
    <thead>
      <tr>
        <HeaderColumn
          v-for="(column, index) in columns"
          :key="index"
          :sortOrder="sortOrder"
          :column="column"
          :active="column === sortBy"
          @sort-changed="sortChanged(column)"
        />
      </tr>
    </thead>
    <tbody>
      <Row
        v-for="(row, index) in data"
        :key="index"
        :data="row"
        :columns="columns"
      />
    </tbody>
  </table>
</template>

<script>
import HeaderColumn from './HeaderColumn'
import Row from './Row'

export default {
  name: 'Table',
  props: {
    sortBy: Object,
    sortOrder: {
      type: String,
      default: 'desc',
    },
    columns: {
      type: Array,
      required: true,
    },
    data: {
      type: Array,
      default: () => [],
    },
  },
  methods: {
    sortChanged(column) {
      this.$emit('sort-changed', column)
    },
  },
  components: {
    HeaderColumn,
    Row,
  },
}
</script>
