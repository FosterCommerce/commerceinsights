<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use Tightenco\Collect\Support\Collection;

class Revenue extends BaseFormatter {

    protected $showsCurrency = true;
    public static $key = 'revenue';

    function totals() {
        $groups = $this->empty->merge($this->groupedData)->map(function ($group) { return $group->sum('totalPaid'); });
        $ordersPerDay = $groups->sum() / $groups->count();

        return collect([
            'Total' => Craft::$app->getFormatter()->asCurrency($this->data->sum('totalPaid')),
            'Average' => Craft::$app->getFormatter()->asCurrency($ordersPerDay),
        ]);
    }

    function format() {
        return $this->empty->merge($this->groupedData)
            ->map(function (Collection $group, $key) {
                return ['x' => $key, 'y' => $group->sum(function($row) {
                    return $row->totalPrice;
                })];
            })
            ->values()
            ->toArray();
    }

}