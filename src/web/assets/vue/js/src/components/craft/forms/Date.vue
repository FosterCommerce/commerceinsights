<template>
  <TextInput class="datewrapper" :size="true" />
</template>

<script>
/* global $ */
import parse from 'date-fns/parse'
import format from 'date-fns/format'
import TextInput from './TextInput'

export default {
  name: 'CraftDate',
  template: '<input />',
  data: () => ({
    picker: null,
    defaultDate: new Date().toISOString(),
  }),
  props: {
    value: String,
  },
  computed: {
    date: ({ value }) => format(parse(value), 'MM/DD/YYYY'),
  },
  watch: {
    date() {
      this.picker.datepicker('setDate', this.date)
    },
  },
  mounted() {
    this.defaultDate = this.date
    const input = $(this.$el).children('input')
    this.picker = input.datepicker({
      defaultDate: this.date,
      onSelect: () => {
        this.onInput()
      },
    })

    this.picker.datepicker('setDate', this.date)
  },
  methods: {
    onInput() {
      this.$emit('input', this.picker.datepicker('getDate'))
    },
  },
  components: {
    TextInput,
  },
}
</script>
