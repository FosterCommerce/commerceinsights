<?php

namespace fostercommerce\commerceinsights\plugin;

use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;

trait Routes
{

    // Private Methods
    // =========================================================================

    private function _registerCpRoutes()
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            // $event->rules['commerceinsights'] = 'commerceinsights/dashboard/dashboard-index';
            $event->rules['commerceinsights/revenue'] = 'commerceinsights/revenue/revenue-index';
            $event->rules['commerceinsights/revenue/<format:json>'] = 'commerceinsights/revenue/revenue-index';
            $event->rules['commerceinsights/revenue/<format:csv>'] = 'commerceinsights/revenue/revenue-index';
            $event->rules['commerceinsights/orders'] = 'commerceinsights/order/order-index';
            $event->rules['commerceinsights/orders/<format:json>'] = 'commerceinsights/order/order-index';
            $event->rules['commerceinsights/orders/<format:csv>'] = 'commerceinsights/order/order-index';
            $event->rules['commerceinsights/products'] = 'commerceinsights/product/index';
            $event->rules['commerceinsights/products/<format:json>'] = 'commerceinsights/product/index';
            $event->rules['commerceinsights/products/<format:csv>'] = 'commerceinsights/product/index';
            $event->rules['commerceinsights/saved'] = 'commerceinsights/saved/list';
            $event->rules['commerceinsights/saved/save'] = 'commerceinsights/saved/save-report';
            $event->rules['commerceinsights/saved/<id>'] = 'commerceinsights/saved/get-report';
            $event->rules['commerceinsights/saved/delete/<id>'] = 'commerceinsights/saved/delete-report';
            // $event->rules['commerceinsights/products'] = 'commerceinsights/product/product-index';
            // $event->rules['commerceinsights/products/<format:json>'] = 'commerceinsights/product/product-index';
            // $event->rules['commerceinsights/products/<format:csv>'] = 'commerceinsights/product/product-index';
            // $event->rules['commerceinsights/customers'] = 'commerceinsights/customer/customer-index';
            // $event->rules['commerceinsights/customers/<format:json>'] = 'commerceinsights/customer/customer-index';
            // $event->rules['commerceinsights/customers/<format:csv>'] = 'commerceinsights/customer/customer-index';
            // new vue stuff
            $event->rules['commerceinsights/view/<view>'] = 'commerceinsights/vue/index';
        });
    }
}
