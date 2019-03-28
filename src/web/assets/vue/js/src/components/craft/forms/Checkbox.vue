<template>
  <div class="input ltr" :class="{ errors, success }">
    <label>
      {{label}}
      <input
        type="checkbox"
        class="checkbox"
        :id="id"
        :value="value"
        :name="name"
        @input="onInput"
      />
    </label>
  </div>
</template>

<script>
export default {
  name: 'CraftCheckbox',
  props: {
    id: String,
    value: String,
    errors: Boolean,
    success: Boolean,
    name: String,
    label: String,
  },
  methods: {
    onInput(e) {
      this.$emit('input', e.target.value)
    },
  },
}
</script>

<style scoped>
label {
  display: inline-block;
  clear: none;
  position: relative;
  cursor: pointer;
  padding-left: 20px;
}

label::before {

  display: block;
  position: absolute;
  width: 12px !important;
  height: 12px;
  content: '.';
  font-size: 0;
  border-radius: 1px;
  background: #fff;
  box-shadow: 0 0 0 1px rgba(0, 0, 20, 0.2);
}
</style>
