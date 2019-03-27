<?php
/**
 * Commerce Insights plugin for Craft CMS
 *
 * Better reporting for Craft Commerce
 *
 * @author
 * @copyright Copyright (c) 2018
 * @link      https://github.com/
 * @package   CommerceInsights
 * @since     0.0.1
 */

namespace fostercommerce\commerceinsights;

use Craft;
use craft\commerce\elements\Order;
use craft\web\twig\variables\CraftVariable;
use fostercommerce\commerceinsights\formatters\AverageRevenuePerOrder;
use fostercommerce\commerceinsights\formatters\BaseFormatter;
use fostercommerce\commerceinsights\formatters\Revenue;
use fostercommerce\commerceinsights\formatters\Orders;
use fostercommerce\commerceinsights\formatters\Customers;
use fostercommerce\commerceinsights\formatters\Products;
use fostercommerce\commerceinsights\plugin\Routes;
use fostercommerce\commerceinsights\services\Url;
use yii\debug\models\search\Base;
use yii\log\Logger;
use yii\base\Event;

class Plugin extends \craft\base\Plugin
{
    use Routes;

    /**
     * @inheritdoc
     */
    public $hasCpSection = true;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_registerCpRoutes();

        $this->setComponents([
            'paramParser' => \fostercommerce\commerceinsights\services\ParamParser::class,
        ]);

        BaseFormatter::addFormatter(Revenue::class);
        BaseFormatter::addFormatter(Orders::class);
        BaseFormatter::addFormatter(Customers::class);
        BaseFormatter::addFormatter(Products::class);

        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $e) {
            /** @var CraftVariable $variable */
            $variable = $e->sender;
            $variable->set('url', Url::class);
        });

        Craft::$app->view->registerTwigExtension(new web\twig\Extension);
    }

    /**
     * @inheritdoc
     */
    public function getCpNavItem()
    {
        $item = parent::getCpNavItem();
        $item['label'] = Craft::t('commerceinsights', 'Commerce Insights');
        $item['badgeCount'] = 0;
        $item['subnav'] = [
            'dashboard' => ['label' => Craft::t('commerceinsights', 'Dashboard'), 'url' => 'commerceinsights/view/dashboard'],
            'revenue' => ['label' => Craft::t('commerceinsights', 'Revenue'), 'url' => 'commerceinsights/view/revenue'],
            'orders' => ['label' => Craft::t('commerceinsights', 'Orders'), 'url' => 'commerceinsights/view/orders'],
            'saved' => ['label' => Craft::t('commerceinsights', 'Saved'), 'url' => 'commerceinsights/view/saved'],
            // 'customers' => ['label' => Craft::t('commerceinsights', 'Customers'), 'url' => 'commerceinsights/customers'],
            // 'products' => ['label' => Craft::t('commerceinsights', 'Products'), 'url' => 'commerceinsights/products'],
        ];
        return $item;
    }
}
