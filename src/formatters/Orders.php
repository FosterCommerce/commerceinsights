<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use Tightenco\Collect\Support\Collection;

class Orders extends BaseFormatter {

    public static $key = 'orders';

    function totals() {
        $groups = $this->empty->merge($this->groupedData)->map(function ($group) { return $group->count(); });
        $ordersPerDay = $groups->sum() / $groups->count();

        return collect([
            'Total' => $this->data->count(),
            'Average' => number_format($ordersPerDay, 2),
            'Best' => $groups->max(),
            'Total Price' => Craft::$app->getFormatter()->asCurrency($this->data->sum('totalPrice')),
            'Total Paid' => Craft::$app->getFormatter()->asCurrency($this->data->sum('totalPaid')),
        ]);
    }

    function format() {
        return $this->empty->merge($this->groupedData)
            ->map(function (Collection $group, $key) {
                return ['x' => $key, 'y' => $group->count()];
            })
            ->values()
            ->toArray();
    }

}