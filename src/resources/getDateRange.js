import startOfDay from 'date-fns/start_of_day'
import endOfDay from 'date-fns/end_of_day'
import startOfWeek from 'date-fns/start_of_week'
import endOfWeek from 'date-fns/end_of_week'
import startOfMonth from 'date-fns/start_of_month'
import endOfMonth from 'date-fns/end_of_month'
import startOfQuarter from 'date-fns/start_of_quarter'
import endOfQuarter from 'date-fns/end_of_quarter'
import startOfYear from 'date-fns/start_of_year'
import endOfYear from 'date-fns/end_of_year'
import subDays from 'date-fns/sub_days'
import subWeeks from 'date-fns/sub_weeks'
import subMonths from 'date-fns/sub_months'
import subQuarters from 'date-fns/sub_quarters'
import subYears from 'date-fns/sub_years'

const mapping = {
  today: () => [startOfDay(new Date()), endOfDay(new Date())],
  'this-week': () => [startOfWeek(new Date()), endOfWeek(new Date())],
  'this-month': () => [startOfMonth(new Date()), endOfMonth(new Date())],
  'this-quarter': () => [startOfQuarter(new Date()), endOfQuarter(new Date())],
  'this-year': () => [startOfYear(new Date()), endOfYear(new Date())],
  yesterday: () => [
    subDays(startOfDay(new Date()), 1),
    subDays(endOfDay(new Date()), 1),
  ],
  'prev-week': () => [
    subWeeks(startOfWeek(new Date()), 1),
    subWeeks(endOfWeek(new Date()), 1),
  ],
  'prev-month': () => [
    subMonths(startOfMonth(new Date()), 1),
    subMonths(endOfMonth(new Date()), 1),
  ],
  'prev-quarter': () => [
    subQuarters(startOfQuarter(new Date()), 1),
    subQuarters(endOfQuarter(new Date()), 1),
  ],
  'prev-year': () => [
    subYears(startOfYear(new Date()), 1),
    subYears(endOfYear(new Date()), 1),
  ],
}

const getDateRange = val => (mapping[val] ? mapping[val]() : [])

export default getDateRange
