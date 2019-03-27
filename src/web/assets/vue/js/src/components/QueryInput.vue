<template>
  <TextInput :value="formatted" @input="onInput" />
</template>

<script>
import TextInput from './craft/forms/TextInput'

export default {
  name: 'QueryInput',
  props: {
    value: {
      type: Object,
      default: () => ({}),
    },
  },
  computed: {
    formatted: ({ value }) => JSON.stringify(value),
  },
  methods: {
    onInput(value) {
      try {
        if (value.trim().length === 0) {
          this.$emit('input', {})
        } else {
          console.log('weeee')
          this.$emit('input', JSON.parse(value))
        }
      } catch (e) {
        /* noOp */
      }
    },
  },
  components: {
    TextInput,
  },
}
</script>
