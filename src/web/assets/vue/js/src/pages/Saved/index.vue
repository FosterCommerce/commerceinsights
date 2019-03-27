<template>
  <Layout title="Saved Reports">
    <div class="ci-spacing">
      <CraftTable
        :columns="tableColumns"
        :data="state.reports"
      />
    </div>
  </Layout>
</template>

<script>
import Vue from 'vue'
import CraftTable from '../../components/craft/Table'
import Layout from '../../components/craft/Layout'
import DeleteButton from './DeleteButton'
import state from './state'

const LinkCell = {
  name: 'LinkCell',
  render() {
    return (
      <abbr>
        <a href={`/admin/commerceinsights/view/${this.data.type}?report=${this.data.id}`}>
          {this.data.name}
        </a>
      </abbr>
    )
  },
  props: {
    column: Object,
    data: Object,
  },
}

export default {
  name: 'SavedReports',
  data () {
    return {
      state,
      tableColumns: [
        {
          key: 'name',
          label: 'Name',
          component: LinkCell,
        },
        {
          key: 'type',
          label: 'Type',
        },
        {
          key: 'deleteAction',
          label: '',
          class: 'thin',
          component: DeleteButton(),
          thin: true,
        },
      ],
    }
  },
  mounted() {
    this.$axios
      .get('saved')
      .then(({ data }) => {
        this.state.reports = data
      })
  },
  components: {
    CraftTable,
    Layout,
  },
}
</script>
