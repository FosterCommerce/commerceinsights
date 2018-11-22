<template>
  <main id="main" role="main">
    <header id="header">
      <h1>{{ title }}</h1>
      <div class="flex-grow"></div>
      <slot name="actionButton"></slot>
    </header>

    <div id="main-content" :class="{ 'has-sidebar': hasSidebar }">
      <!--
        class="{% if sidebar %}has-sidebar{% endif %} {% if details %}has-details{% endif %}"
      -->
      <a v-if="hasSidebar" id="sidebar-toggle" @click="toggleSidebar">
        <span id="selected-sidebar-item-label"></span>&nbsp;<span
          data-icon="downangle"
        ></span>
      </a>
      <div v-if="hasSidebar" id="sidebar" class="sidebar">
        <slot name="sidebar"></slot>
      </div>

      <div id="content-container">
        <div id="content"><slot></slot></div>
      </div>
    </div>
  </main>
</template>

<script>
/* global Garnish */

export default {
  name: 'Layout',
  props: {
    title: {
      type: String,
      default: 'Dashboard',
    },
  },
  computed: {
    hasSidebar() {
      return !!this.$slots['sidebar']
    },
  },
  methods: {
    toggleSidebar: () => Garnish.$bod.toggleClass('showing-sidebar'),
  },
}
</script>
