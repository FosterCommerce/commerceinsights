<?php

namespace fostercommerce\commerceinsights\services;

use Craft;
use yii\base\Component;
use DateTime;

class ParamParser extends Component
{
    private $start;
    private $end;
    private $range;
    private $step;

    private function startOf($period = 'w')
    {
        switch($period) {
            case 'd':
                $date = new DateTime(date('y-m-d'));
                break;
            case 'm':
                $year = date('y');
                $month = date('m');
                $date = new DateTime("{$year}-{$month}-1");
                break;
            case 'q':
                $year = date('y');
                $month = date('m') - (date('m') % 3);
                $date = new DateTime("{$year}-{$month}-1");
                break;
            case 'y':
                $year = date('y');
                $date = new DateTime("{$year}-1-1");
                break;
            case 'w':
            default:
                $dayOfWeek = date('w');
                $date = (new DateTime(date('y-m-d')))->modify("-{$dayOfWeek} days");
        }

        return $date;
    }

    private function endOf($period = 'w')
    {
        $date = $this->startOf($period);
        switch($period) {
            case 'd':
                $date->modify('+1 days');
                break;
            case 'm':
                $date->modify('+1 months');
                break;
            case 'q':
                $date->modify('+3 months');
                break;
            case 'y':
                $date->modify('+1 years');
                break;
            case 'w':
            default:
                $date->modify('+1 weeks');
        }

        return $date;
    }

    private function getDateRange($range = 'this-week')
    {
        $mapping = [
            'today' => [$this->startOf('d'), $this->endOf('d')],
            'this-week' => [$this->startOf('w'), $this->endOf('w')],
            'this-month' => [$this->startOf('m'), $this->endOf('m')],
            'this-quarter' => [$this->startOf('q'), $this->endOf('q')],
            'this-year' => [$this->startOf('y'), $this->endOf('y')],
            'yesterday' => [
                $this->startOf('d')->modify('-1 days'),
                $this->endOf('d')->modify('-1 days'),
            ],
            'prev-week' => [
                $this->startOf('w')->modify('-1 weeks'),
                $this->endOf('w')->modify('-1 weeks'),
            ],
            'prev-month' => [
                $this->startOf('m')->modify('-1 months'),
                $this->endOf('m')->modify('-1 months'),
            ],
            'prev-quarter' => [
                $this->startOf('q')->modify('-3 months'),
                $this->endOf('q')->modify('-3 months'),
            ],
            'prev-year' => [
                $this->startOf('y')->modify('-1 years'),
                $this->endOf('y')->modify('-1 years'),
            ],
        ];

        return $mapping[$range];
    }

    public function __construct()
    {
        $session = Craft::$app->session;
        $request = Craft::$app->request;
        $start = $request->getParam('start');
        $end = $request->getParam('end');
        $range = $request->getParam('range');

        if (!$range && !$start && !$end) {
            $range = $session->get('commerceinsights_range');
        }

        if ($range) {
            $session->remove('commerceinsights_start_date');
            $session->remove('commerceinsights_end_date');
            $session->set('commerceinsights_range', $range);
            $this->range = $range;

            $dateRange = $this->getDateRange($range);
            $this->start = $dateRange[0];
            $this->end = $dateRange[1];
        } else {
            if (!$start || !$end) {
                $start = $session->get('commerceinsights_start_date');
                $end = $session->get('commerceinsights_end_date');
            }

            if ($start && $end) {
                $session->remove('commerceinsights_range');
                $session->set('commerceinsights_start_date', $start);
                $session->set('commerceinsights_end_date', $end);

                $this->start = new DateTime(date('Y-m-d', strtotime($start)));
                $this->end = (new DateTime(date('Y-m-d', strtotime($end))))->modify('+1 days');
            } else {
                // Otherwise default to the current week
                $dateRange = $this->getDateRange('this-week');
                $this->start = $dateRange[0];
                $this->end = $dateRange[1];
            }
        }

        $this->start = $this->start->format('Y-m-d');
        $this->end = $this->end->format('Y-m-d');

        $duration = strtotime($this->end) - strtotime($this->start);
        $hours = $duration / 60 / 60;
        if ($hours < 48) {
            $this->step = 60 * 60;
        } else {
            $this->step = 60 * 60 * 24;
        }
    }

    public function start()
    {
        return $this->start;
    }

    public function end()
    {
        return $this->end;
    }

    public function range()
    {
        return $this->range;
    }

    public function step()
    {
        return $this->step;
    }

    public function stepFormat($hours = true)
    {
        if ($hours) {
            return $this->step == 60 * 60 ? 'Y-m-d H:00:00' : 'Y-m-d 00:00:00';
        }

        return 'Y-m-d 00:00:00';
    }

    /**
     * An array of preset ranges
     *
     * @return array
     */
    public function presetRanges()
    {
        return [
            24 * 60 * 60 => 'Today',
            7 * 24 * 60 * 60 => 'Last 7 days',
            30 * 24 * 60 * 60 => 'Last 30 days',
            365 * 24 * 60 * 60 => 'Last 365 days',
        ];
    }
}
