<?php

namespace fostercommerce\commerceinsights\services;

use yii\base\Component;

class ParamParser extends Component
{
    private $start;
    private $end;
    private $step;

    public function __construct()
    {
        $start = \Craft::$app->request->getParam('start');
        $end = \Craft::$app->request->getParam('end');
        $range = \Craft::$app->request->getParam('range') ?: 7 * 24 * 60 * 60 /* 7 days */;

        if ($start && $end) {
            $this->start = date('Y-m-d\TH:i:s', strtotime($start));
            $this->end = date('Y-m-d\TH:i:s', strtotime($end));
        } else {
            $this->start = date('Y-m-d\TH:i:s', strtotime("-{$range} seconds"));
            $this->end = date('Y-m-d\TH:i:s');
        }

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

    public function step()
    {
        return $this->step;
    }

    public function stepFormat()
    {
        return $this->step == 60 * 60 ? 'Y-m-d H:00:00' : 'Y-m-d 00:00:00';
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
