<template>
  <a class="icon"
    :class="{[icon]: true}"
    :title="title"
    role="button"
    :href="href"
    @click="$emit('click')">
  </a>
</template>

<script>
export default {
  name: 'ActionIcon',
  props: {
    icon: String,
    title: String,
    href: String,
  },
}
</script>

<style scoped>
.icon.user:before {
  content: 'user';
}
</style>
