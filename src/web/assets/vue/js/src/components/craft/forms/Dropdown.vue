<template>
  <div class="select">
    <select :disabled="disabled" @change="onChange">
      <slot v-for="item in transformed" :item="item">
        <option :value="item.value" :key="item.value" :selected="item.selected">
          {{ item.label }}
        </option>
      </slot>
    </select>
  </div>
</template>

<script>
export default {
  name: 'CraftDropdown',
  props: {
    disabled: Boolean,
    data: {
      type: Array,
      default: () => [],
    },
    value: {
      type: String,
    },
  },
  computed: {
    transformed({ data, value }) {
      return data.map(item => ({
        ...item,
        selected: this.isSelected(item, value),
      }))
    },
  },
  methods: {
    onChange(e) {
      this.$emit('input', e.target.value)
    },
    isSelected(item) {
      if (this.value === null) {
        return item.value === ''
      }

      return item.value === this.value
    },
  },
}
</script>

<style scoped>
select {
  transition: background-color .5s ease !important;
}
select:disabled {
  background-color: #d3d3d3;
}
</style>