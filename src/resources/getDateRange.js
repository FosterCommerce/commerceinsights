import moment from 'moment'

const mapping = {
  today: () => [moment().startOf('day'), moment().endOf('day')],
  'this-week': () => [moment().startOf('week'), moment().endOf('week')],
  'this-month': () => [moment().startOf('month'), moment().endOf('month')],
  'this-quarter': () => [
    moment().startOf('quarter'),
    moment().endOf('quarter'),
  ],
  'this-year': () => [moment().startOf('year'), moment().endOf('year')],
  yesterday: () => [
    moment()
      .subtract(1, 'days')
      .startOf('day'),
    moment()
      .subtract(1, 'days')
      .endOf('day'),
  ],
  'prev-week': () => [
    moment()
      .subtract(1, 'weeks')
      .startOf('week'),
    moment()
      .subtract(1, 'weeks')
      .endOf('week'),
  ],
  'prev-month': () => [
    moment()
      .subtract(1, 'months')
      .startOf('month'),
    moment()
      .subtract(1, 'months')
      .endOf('month'),
  ],
  'prev-quarter': () => [
    moment()
      .subtract(1, 'quarters')
      .startOf('quarter'),
    moment()
      .subtract(1, 'quarters')
      .endOf('quarter'),
  ],
  'prev-year': () => [
    moment()
      .subtract(1, 'years')
      .startOf('year'),
    moment()
      .subtract(1, 'years')
      .endOf('year'),
  ],
}

const getDateRange = val => {
  const rangeF = mapping[val]
  if (rangeF) {
    return rangeF().map(d => d.toDate())
  }

  return []
}

export default getDateRange
