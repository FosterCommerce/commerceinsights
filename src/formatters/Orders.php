<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use Tightenco\Collect\Support\Collection;

class Orders extends BaseFormatter
{
    public static $key = 'orders';

    public function totals()
    {
        $merged = $this->empty->merge($this->groupedData);
        $groups = $merged->map(function ($group) {
            return $group->sum('totalPrice');
        });
        $filtered = $merged
            ->filter(function ($group) {
                return $group->count() > 0;
            })
            ->map(function ($group) {
                return [
                    'totalPrice' => $group->sum('totalPrice'),
                    'count' => $group->count(),
                ];
            });

        $formatter = Craft::$app->formatter;
        return collect([
            'Total' => $this->data->count(),
            'Average' => $formatter->asCurrency($filtered->sum('count') ? $filtered->sum('totalPrice') / $filtered->sum('count') : 0),
            'Best' => $formatter->asCurrency($groups->max()),
            'Total Price' => $formatter->asCurrency($this->data->sum('totalPrice')),
            'Total Revenue' => $formatter->asCurrency($this->data->sum('totalPaid')),
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
