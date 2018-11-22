<template>
  <th :class="headerClass">
    <a v-if="column.sortable" @click="sortBy()" href="#">{{ column.label }}</a>
    <span v-else>{{ column.label }}</span>
  </th>
</template>

<script>
export default {
  name: 'TableHeaderColumn',
  props: {
    active: {
      type: Boolean,
      default: false,
    },
    sortOrder: {
      type: String,
      default: 'desc',
    },
    column: {
      type: Object,
      required: true,
    },
  },
  computed: {
    headerClass: ({ column, active, sortOrder }) => ({
      ordered: column.sortable && active,
      ordererable: !active && column.sortable,
      [sortOrder]: column.sortable && active,
    }),
  },
  methods: {
    sortBy() {
      this.$emit('sort-changed')
    },
  },
}
</script>
