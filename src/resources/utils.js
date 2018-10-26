const select = (selector, scope = document) =>
  scope.querySelector(selector) || { addEventListener: () => {} }

const selectAll = (selector, scope = document) =>
  scope.querySelectorAll(selector)

export { select, selectAll }
