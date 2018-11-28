/* globals Craft */

const CraftPlugin = (Vue, options) => {
  Vue.prototype.$cp = Craft.cp
}

export default CraftPlugin
