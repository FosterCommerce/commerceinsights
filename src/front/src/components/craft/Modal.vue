<template>
  <div>
    <div class="modal" :class="{[className]: !!className}">
      <template v-if="!$slots['main']">
        <div class="body">
          <slot name="body"></slot>
        </div>
      </template>
      <template v-else>
        <header v-if="!!$slots['header']" class="header">
          <slot name="header"></slot>
        </header>
        <div class="main">
          <slot name="main"></slot>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
/* global Garnish */

export default {
  name: 'Modal',
  data: () => ({
    modal: null,
  }),
  props: {
    resizable: {
      type: Boolean,
      default: false,
    },
    width: Number,
    height: Number,
    dark: {
      type: Boolean,
      default: true,
    },
    className: String,
  },
  mounted () {
    const element = this.$el.children[0]
    this.modal = new Garnish.Modal(element, {
      autoShow: true,
      resizable: this.resizable,
      shadeClass: this.shadeClass,
      onFadeOut: () => {
        this.$emit('hide')
      },
    })
  },
  computed: {
    shadeClass: ({ dark }) => ['modal-shade', dark && 'dark'].filter(s => !!s).join(' '),
  },
}
</script>

<style scoped>
.main {
  padding: 24px;
}
</style>