<?php

namespace fostercommerce\commerceinsights\services;

use Craft;
use yii\base\Component;
use fostercommerce\commerceinsights\helpers\DateTimeHelper;
use DateTime;

class ParamParser extends Component
{
    private $start;
    private $end;
    private $range;
    private $step;
    private $search;

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

            $dateRange = DateTimeHelper::getDateRange($range);
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
                $dateRange = DateTimeHelper::getDateRange('this-week');
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

        $search = $request->getParam('search');

        try {
            $this->search = json_decode($search, true) ?: [];
        } catch (\Exception $e) {
            $this->search = [];
        }
    }

    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
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
