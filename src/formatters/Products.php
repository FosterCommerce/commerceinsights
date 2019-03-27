<?php

namespace fostercommerce\commerceinsights\formatters;

use Craft;
use Tightenco\Collect\Support\Collection;

class Products extends BaseFormatter
{
    public static $key = 'products';

    public function format()
    {
        return [];
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
