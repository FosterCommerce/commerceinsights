<template>
  <span>
    <a title="Filter by this user" @click="filterByUser">
      {{data['email']}}
    </a>
    <ActionIcon title="Show user" icon="user" :href="url" />
  </span>
</template>

<script>
import Vue from 'vue'
import state from '../state'
import ActionIcon from '../../../components/craft/ActionIcon'

export default {
  name: 'EmailCell',
  props: {
    column: Object,
    data: Object,
  },
  computed: {
    url: ({data}) => `/admin/users/${data.userId}`,
  },
  methods: {
    filterByUser () {
      Vue.set(state.search, 'orders.email', this.data['email'])
    }
  },
  components: {
    ActionIcon,
  },
}
</script>

<style scoped>
span > a:first-child {
  padding-right: 16px;
}

a {
  cursor: pointer;
}
</style>
