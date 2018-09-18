<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use Tightenco\Collect\Support\Collection;

class AverageRevenuePerOrder extends BaseFormatter {

    protected $showsCurrency = true;
    public static $key = 'averageRevenuePerOrder';

    function totals() {
        return collect([
            'Average' => $this->data->pluck('totalPrice')->avg() ?: 0,
            'Total Price' => $this->data->sum('totalPrice'),
            'Total Paid' => $this->data->sum('totalPaid'),
        ])->map(function ($item) {
            return Craft::$app->getFormatter()->asCurrency($item);
        });
    }

    function format() {
        return $this->empty->merge($this->groupedData)
            ->map(function (Collection $group, $key) {
                return ['x' => $key, 'y' => $group->pluck('totalPrice')->avg() ?: 0];
            })
            ->values()
            ->toArray();
    }

}