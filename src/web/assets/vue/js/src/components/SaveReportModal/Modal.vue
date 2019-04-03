<template>
  <Modal className="save-report-modal" @hide="$emit('hide')" :width="200" :height="169">
    <template v-slot:header><h1>Save Report</h1></template>
    <template v-slot:main>
      <div>
        <div class="flex">
          <TextInput class="flex-grow" :class="{errors: !!saveReportError}"
            placeholder="Report name"
            v-model="saveReportName" />
          <ActionButton label="Save" @click="saveReport" />
        </div>
      </div>
    </template>
  </Modal>
</template>

<script>
import TextInput from '../craft/forms/TextInput'
import ActionButton from '../craft/ActionButton'
import Modal from '../craft/Modal'

export default {
  name: 'SaveReportModal',
  data: () => ({
    saveReportError: false,
    saveReportName: '',
  }),
  props: {
    reportType: {
      type: String,
      required: true,
    },
    query: {
      type: Object,
      required: true,
    },
  },
  methods: {
    saveReport () {
      this.$axios
        .post('saved/save', {
          type: this.reportType,
          name: this.saveReportName,
          query: this.query,
        })
        .then(({data}) => {
          if (data.error) {
            this.saveReportError = true
          } else {
            this.$emit('hide')
            this.$cp.displayNotice('Report saved')
          }
        })
        .catch(err => {
          this.saveReportError = true
          this.$cp.displayError('Unknown error occurred')
        })
    },
  },
  components: {
    Modal,
    ActionButton,
    TextInput,
  },
}
</script>

<style>
.save-report-modal {
  width: 500px;
  min-width: 500px;
  height: 169px;
  min-height: 169px;
}
</style>

<style scoped>
.save-report-body {
  display: flex;
  flex-direction: row;
}
</style>
