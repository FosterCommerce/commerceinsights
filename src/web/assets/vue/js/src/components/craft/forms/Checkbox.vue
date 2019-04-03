<template>
  <div class="input ltr" :class="{ errors, success }">
    <p-check
      :id="id"
      :checked="value"
      :name="name"
      @change="onChange"
      class="p-switch p-fill"
      color="success"
    >
      {{label}}
    </p-check>
  </div>
</template>

<script>
import PrettyCheck from 'pretty-checkbox-vue/check'

export default {
  name: 'CraftCheckbox',
  props: {
    id: String,
    value: Boolean,
    errors: Boolean,
    success: Boolean,
    name: String,
    label: String,
  },
  methods: {
    onChange(checked) {
      this.$emit('input', checked)
    },
  },
  components: {
    'p-check': PrettyCheck,
  }
}
</script>
