export default value => {
  if (!value) {
    return ''
  }

  const strValue = value.toString()
  return strValue.charAt(0).toUpperCase() + strValue.slice(1)
}
