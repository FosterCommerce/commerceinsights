<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use Tightenco\Collect\Support\Collection;

class Orders extends BaseFormatter
{
    public static $key = 'orders';

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
        $craftFormatter = Craft::$app->getFormatter();
        $headers = [
            'Date', 'Number', 'Order Status', 'Total Price', 'Tax',
            'Discount', 'Total Paid', 'Email',
        ];

        $rows = array();
        foreach ($this->data as $order) {
            $rows[] = [
                $order->dateOrdered->format('m/d/Y'),
                $order->number,
                $order->orderStatus->name,
                $craftFormatter->asCurrency($order->totalPrice),
                $craftFormatter->asCurrency($order->getLineItems()[0]->getAdjustmentsTotalByType('tax') ?: 0),
                $craftFormatter->asCurrency($order->totalDiscount),
                $craftFormatter->asCurrency($order->totalPaid),
                $order->email,
            ];
        }

        return array_merge([$headers], $rows);
    }
}
