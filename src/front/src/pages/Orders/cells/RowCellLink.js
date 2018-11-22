export default ({ urlF, tooltipF, labelF }) => {
  return {
    name: 'RowCellLink',
    render() {
      return (
        <abbr title={tooltipF(this.column, this.data)}>
          <a href={urlF(this.column, this.data)}>
            {labelF(this.column, this.data)}
          </a>
        </abbr>
      )
    },
    props: {
      column: Object,
      data: Object,
    },
  }
}
