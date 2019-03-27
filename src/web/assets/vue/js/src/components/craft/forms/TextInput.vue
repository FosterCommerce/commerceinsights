<template>
  <div class="input ltr" :class="{ errors, success }">
    <input
      type="text"
      class="text"
      autocomplete="off"
      :class="{ fullwidth: fullWidth }"
      :id="id"
      :placeholder="placeholder"
      :value="value"
      :name="name"
      @input="onInput"
    />
  </div>
</template>

<script>
export default {
  name: 'CraftTextInput',
  props: {
    id: String,
    placeholder: String,
    value: String,
    errors: Boolean,
    success: Boolean,
    name: String,
    fullWidth: {
      type: Boolean,
      default: true,
    },
  },
  methods: {
    onInput(e) {
      this.$emit('input', e.target.value)
    },
  },
}
</script>
