<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use Tightenco\Collect\Support\Collection;

class Customers extends BaseFormatter
{
    public static $key = 'customers';

    public function __construct($data, $min = null, $max = null)
    {
        // TODO:
        parent::__construct($data, $min, $max);
        $this->groupData(function ($item) {
            return (new DateTime($item['dateOrdered']))->format($this->stepFormat);
        });
    }

    public function totals()
    {
        $groups = $this->empty->merge($this->groupedData)->map(function ($group) {
            return $group->count();
        });
        $ordersPerDay = $groups->sum() / $groups->count();

        return collect([
            'Total' => $this->data->count(),
            'Average' => number_format($ordersPerDay, 2),
            'Best' => $groups->max(),
            'Total Price' => Craft::$app->getFormatter()->asCurrency($this->data->sum('totalPrice')),
            'Total Paid' => Craft::$app->getFormatter()->asCurrency($this->data->sum('totalPaid')),
        ]);
    }

    public function format()
    {
        return $this->empty->merge($this->groupedData)
            ->map(function (Collection $group, $key) {
                return ['x' => $key, 'y' => $group->count()];
            })
            ->values()
            ->toArray();
    }

    public function csv()
    {
        return [];
    }
}
