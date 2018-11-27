import Modal from './Modal'

export const mixin = {
  data: () => ({
    saveModalVisible: false,
  }),
  methods: {
    toggleModal () {
      this.saveModalVisible = !this.saveModalVisible
    },
  },
}

export default Modal
