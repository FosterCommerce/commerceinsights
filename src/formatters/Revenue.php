<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use Tightenco\Collect\Support\Collection;
use DateTime;

class Revenue extends BaseFormatter
{
    protected $showsCurrency = true;
    public static $key = 'revenue';

    public function __construct($data, $min = null, $max = null)
    {
        parent::__construct($data, $min, $max);
        $this->groupData(function ($item) {
            return (new DateTime($item['dateOrdered']))->format($this->stepFormat);
        });
    }

    public function totals()
    {
        $groups = $this->empty->merge($this->groupedData)->map(function ($group) {
            return $group->sum('totalPaid');
        });
        $count = $groups->count();
        $ordersPerDay = $groups->sum() / ($count > 0 ? $count : 1);

        $totalPaid = $this->data->sum('totalPaid');
        return collect([
            'Total' => Craft::$app->getFormatter()->asCurrency($totalPaid),
            'Average' => Craft::$app->getFormatter()->asCurrency($ordersPerDay),
        ]);
    }

    public function totalPaid()
    {
        return $this->data->sum('totalPaid');
    }

    public function format()
    {
        return $this->empty->merge($this->groupedData)
            ->map(function (Collection $group, $key) {
                return ['x' => $key, 'y' => $group->sum(function ($row) {
                    return $row['totalPrice'];
                })];
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
