<?php

namespace fostercommerce\commerceinsights\controllers;

use Craft;
use fostercommerce\commerceinsights\bundles\ChartBundle;
use fostercommerce\commerceinsights\formatters\BaseFormatter;
use fostercommerce\commerceinsights\formatters\Orders;
use fostercommerce\commerceinsights\Plugin;
use fostercommerce\commerceinsights\services\ParamParser;
use Tightenco\Collect\Support\Collection;
use yii\web\Response;
use craft\commerce\Plugin as CommercePlugin;
use craft\db\Query;
use craft\helpers\Db;

class OrderController extends \craft\web\Controller
{

    /** @var ParamParser */
    private $params;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // $this->requirePermission('commerce-manageOrders');
        $this->view->registerAssetBundle(ChartBundle::class);

        $this->params = Plugin::getInstance()->paramParser;
    }

    public function actionOrderIndex($format='html')
    {
        $statuses = CommercePlugin::getInstance()->orderStatuses;

        $min = $this->params->start();
        $max = $this->params->end();

        $taxQuery = (new Query())
            ->select(['orderId', 'type', 'SUM(amount) as total'])
            ->from('{{%commerce_orderadjustments}}')
            ->andWhere(['type' => 'tax'])
            ->groupBy(['orderId', 'type']);

        $discountQuery = (new Query())
            ->select(['orderId', 'type', 'SUM(amount) as total'])
            ->from('{{%commerce_orderadjustments}}')
            ->andWhere(['type' => 'discount'])
            ->groupBy(['orderId', 'type']);

        $query = (new Query())
            ->select(['orders.*', 'statuses.handle as orderStatus', 'taxAdjustment.total as totalTax', 'discountAdjustment.total as totalDiscount'])
            ->from(['{{%commerce_orders}} orders'])
            ->leftJoin('{{%commerce_orderstatuses}} statuses', '[[statuses.id]] = [[orders.orderStatusId]]')
            ->leftJoin(['taxAdjustment' => $taxQuery], '[[taxAdjustment.orderId]] = [[orders.id]]')
            ->leftJoin(['discountAdjustment' => $discountQuery], '[[taxAdjustment.orderId]] = [[orders.id]]');

        $status = Craft::$app->request->getParam('status');
        if ($status) {
            $query->andWhere(['statuses.handle' => $status]);
        }

        $query
            ->andWhere(['isCompleted' => true])
            ->andWhere(['>=', 'dateOrdered', Db::prepareDateForDb($min)])
            ->andWhere(['<', 'dateOrdered', Db::prepareDateForDb($max)])
            ->orderBy(
                implode(' ', [
                    Craft::$app->request->getParam('sort') ?: 'dateOrdered',
                    Craft::$app->request->getParam('dir') ?: 'asc'
                ])
            );

        foreach ($this->params->search as $key => $value) {
            $query->andWhere([$key => $value]);
        }

        $data = $query->all();
        $rows = collect($query->all());

        $formatterClass = BaseFormatter::getFormatter(Orders::class);
        $formatter = new $formatterClass($rows);

        if ($format == 'csv') {
            return Plugin::getInstance()->csv->generate('orders', $formatter->csv());
        }

        $totalsHtml = Craft::$app->view->renderTemplate('commerceinsights/orders/totals', [
            'totals' => $formatter->totals(),
        ]);

        $orderStatuses = [];
        foreach ($statuses->getAllOrderStatuses() as $status) {
            $orderStatuses[] = [
                'label' => $status->name,
                'value' => $status->handle,
            ];
        }

        $data = [
            'totals' => $formatter->totals(),
            'statuses' => $orderStatuses,
            'formatter' => $formatter::$key,
            'chartShowsCurrency' => $formatter->showsCurrency(),
            'chartData' => $formatter->format(),
            'min' => $min,
            'max' => $max,
            'range' => $this->params->range(),
            'selectedStatus' => $status,
            'search' => (object) $this->params->search,
            'tableData' => $rows,
        ];

        // if (Craft::$app->request->isAjax || $format == 'json') {
            return $this->asJson($data);
        // }

        // return $this->renderTemplate('commerceinsights/orders/_index', $data);
    }
}
