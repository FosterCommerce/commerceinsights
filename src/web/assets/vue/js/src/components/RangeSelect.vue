<template>
  <Dropdown :disabled="disabled" @input="onChange" :data="ranges" :value="value">
    <option
      slot-scope="scope"
      :key="scope.item.value"
      :value="scope.item.value"
      :selected="scope.item.selected"
    >
      {{ scope.item.label }}
    </option>
  </Dropdown>
</template>

<script>
import Dropdown from './craft/forms/Dropdown'

const ranges = [
  { value: 'today', label: 'Today' },
  { value: 'this-week', label: 'This week' },
  { value: 'this-month', label: 'This month' },
  { value: 'this-quarter', label: 'This quarter' },
  { value: 'this-year', label: 'This year' },
  { value: 'yesterday', label: 'Yesterday' },
  { value: 'prev-week', label: 'Previous week' },
  { value: 'prev-month', label: 'Previous month' },
  { value: 'prev-quarter', label: 'Previous quarter' },
  { value: 'prev-year', label: 'Previous year' },
  { value: '', label: 'Custom' },
]

export default {
  name: 'RangeSelect',
  data: () => ({
    ranges,
  }),
  props: {
    disabled: Boolean,
    value: {
      type: String,
      default: 'this-week',
    },
  },
  methods: {
    isSelected(range) {
      return range.value === this.value
    },
    onChange(value) {
      this.$emit('input', value)
    },
  },
  components: {
    Dropdown,
  },
}
</script>
