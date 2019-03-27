<template>
  <div class="btngroup submit" @click="$emit('click')">
    <div class="btn submit" :class="{icon: !!icon, [icon]: !!icon}">
      {{label}}
    </div>
  </div>
</template>

<script>
export default {
  name: 'ActionButton',
  props: {
    label: String,
    icon: String,
  },
}
</script>