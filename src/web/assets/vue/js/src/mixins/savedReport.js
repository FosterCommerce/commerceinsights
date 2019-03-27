import Vue from 'vue'

export default {
  props: {
    query: Object,
  },
  mounted() {
    const fetchReport = () => {
      if (this.query.report) {
        return this.$axios
          .get(`saved/${this.query.report}`)
          .then(({ data }) => data)
      }

      return Promise.resolve({})
    }

    this.shouldUpdate = false
    fetchReport().then(data => {
      Object.keys(data).forEach(key => {
        this.state[key] = data[key]
      })

      Vue.nextTick().then(() => {
        this.shouldUpdate = true
        this.update()
      })
    })
  },
}
