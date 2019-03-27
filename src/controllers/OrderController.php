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
use DateTime;

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

    private function asCurrency($value)
    {
        return isset($value)
            ? Craft::$app->formatter->asCurrency($value)
            : Craft::$app->formatter->asCurrency(0);
    }

    public function getOrderQuery($min, $max)
    {
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

        $userQuery = (new Query())
            ->select(['id', 'email'])
            ->from('{{%users}}');

        $query = (new Query())
            ->select([
                'orders.*',
                'statuses.handle as orderStatus',
                'taxAdjustment.total as totalTax',
                'discountAdjustment.total as totalDiscount',
                'user.id as userId',
            ])
            ->from(['{{%commerce_orders}} orders'])
            ->leftJoin('{{%commerce_orderstatuses}} statuses', '[[statuses.id]] = [[orders.orderStatusId]]')
            ->leftJoin(['taxAdjustment' => $taxQuery], '[[taxAdjustment.orderId]] = [[orders.id]]')
            ->leftJoin(['discountAdjustment' => $discountQuery], '[[taxAdjustment.orderId]] = [[orders.id]]')
            ->leftJoin(['user' => $userQuery], '[[user.email]] = [[orders.email]]');

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

        return collect($query->all());
    }

    public function actionOrderIndex($format='html')
    {
        $statuses = CommercePlugin::getInstance()->orderStatuses;

        $min = $this->params->start();
        $max = $this->params->end();

        $currMin = new DateTime($min);
        $prevMin = (clone $currMin)->sub($currMin->diff(new DateTime($max)))->format('Y-m-d');

        $rows = $this->getOrderQuery($min, $max);
        $prevPeriodRows = $this->getOrderQuery($prevMin, $min);

        $formatterClass = BaseFormatter::getFormatter(Orders::class);
        $formatter = new $formatterClass($rows);
        $prevPeriodFormatter = new $formatterClass($prevPeriodRows, $prevMin, $min);

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

        $formattedRows = $rows->map(function ($row) {
            $row['totalPrice'] = $this->asCurrency($row['totalPrice']);
            $row['totalTax'] = $this->asCurrency($row['totalTax']);
            $row['totalDiscount'] = $this->asCurrency($row['totalDiscount']);
            $row['totalPaid'] = $this->asCurrency($row['totalPaid']);
            return $row;
        });

        $data = [
            'totals' => $formatter->totals(),
            'statuses' => $orderStatuses,
            'formatter' => $formatter::$key,
            'chartShowsCurrency' => $formatter->showsCurrency(),
            'chartData' => $formatter->format(),
            'secondaryChartData' => $prevPeriodFormatter->format(),
            'min' => $min,
            'max' => $max,
            'range' => $this->params->range(),
            'selectedStatus' => $status,
            'search' => (object) $this->params->search,
            'tableData' => $formattedRows,
        ];

        // if (Craft::$app->request->isAjax || $format == 'json') {
        return $this->asJson($data);
        // }

        // return $this->renderTemplate('commerceinsights/orders/_index', $data);
    }
}
