/* globals Craft */

import ActionIcon from '../../components/craft/ActionIcon'
import state from './state'

const DeleteButton = () => ({
  name: 'DeleteButton',
  render () {
    return (
      <ActionIcon
        icon='delete'
        title='Delete'
        onClick={this.deleteItem}
      />
    )
  },
  data: () => ({
    state,
  }),
  props: {
    column: Object,
    data: Object,
  },
  methods: {
    deleteItem () {
      this.$axios
        .get(`saved/delete/${this.data.id}`)
        .then(({ data }) => {
          if (!data.success) {
            Craft.cp.displayError('Unable to delete saved report')
            return false
          }

          this.state.reports = this.state.reports.filter(item => item.id !== this.data.id)
        })

    }
  },
  components: {
    ActionIcon,
  },
})

export default DeleteButton
