<template>
  <ul>
    <li v-for="(value, key) in totals" :key="key">
      <div class="ci-total-label">{{ key }}</div>
      <strong class="ci-total-value">{{ value }}</strong>
    </li>
  </ul>
</template>

<script>
export default {
  name: 'OrdersTotals',
  props: {
    totals: {
      type: Object,
      default: () => ({}),
    },
  },
}
</script>

<style scoped>
ul {
  background: #fbfcfd;
  border: 1px solid #e3e5e8;
  padding: 10px;
  display: flex;
  margin-right: 2%;
  display: block;
}

ul > * {
  flex-grow: 1;
  width: 100%;
  text-align: center;
}

ul li {
  padding: 16px 0;
}

.ci-total-label {
  font-size: 10px;
  text-transform: uppercase;
}

.ci-total-value {
  font-size: 20px;
}
</style>
