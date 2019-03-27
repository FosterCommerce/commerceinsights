<?php

namespace fostercommerce\commerceinsights\controllers;

use Craft;
use fostercommerce\commerceinsights\bundles\ChartBundle;
use fostercommerce\commerceinsights\formatters\BaseFormatter;
use fostercommerce\commerceinsights\formatters\Revenue;
use fostercommerce\commerceinsights\Plugin;
use fostercommerce\commerceinsights\services\ParamParser;
use Tightenco\Collect\Support\Collection;
use yii\web\Response;
use craft\db\Query;
use craft\helpers\Db;
use DateTime;

class RevenueController extends \craft\web\Controller
{

    /** @var ParamParser */
    private $params;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // $this->requirePermission('commerce-manageSales');
        $this->view->registerAssetBundle(ChartBundle::class);

        $this->params = Plugin::getInstance()->paramParser;
    }


    private function getRevenueQuery($min, $max, $request)
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

        $query = (new Query())
            ->select(['orders.*', 'statuses.handle as orderStatus', 'taxAdjustment.total as totalTax', 'discountAdjustment.total as totalDiscount'])
            ->from(['{{%commerce_orders}} orders'])
            ->leftJoin('{{%commerce_orderstatuses}} statuses', '[[statuses.id]] = [[orders.orderStatusId]]')
            ->leftJoin(['taxAdjustment' => $taxQuery], '[[taxAdjustment.orderId]] = [[orders.id]]')
            ->leftJoin(['discountAdjustment' => $discountQuery], '[[taxAdjustment.orderId]] = [[orders.id]]');

        $query
            ->andWhere(['isCompleted' => true])
            ->andWhere(['>=', 'dateOrdered', Db::prepareDateForDb($min)])
            ->andWhere(['<', 'dateOrdered', Db::prepareDateForDb($max)])
            ->andWhere(['!=', 'paidStatus', 'unpaid'])
            ->orderBy(
                implode(' ', [
                    Craft::$app->request->getParam('sort') ?: 'dateOrdered',
                    Craft::$app->request->getParam('dir') ?: 'asc'
                ])
            );

        $rows = collect($query->all());
        return $rows;
    }

    public function actionRevenueIndex($format='html')
    {
        $request = Craft::$app->request;
        $min = $this->params->start();
        $max = $this->params->end();

        $currMin = new DateTime($min);
        $prevMin = (clone $currMin)->sub($currMin->diff(new DateTime($max)))->format('Y-m-d');

        $formatterClass = BaseFormatter::getFormatter(Revenue::class);

        $rows = $this->getRevenueQuery($min, $max, $request);
        $prevPeriodRows = $this->getRevenueQuery($prevMin, $min, $request);

        $formatter = new $formatterClass($rows);
        $prevPeriodFormatter = new $formatterClass($prevPeriodRows, $prevMin, $min);

        $prevTotal = $prevPeriodFormatter->totalPaid();
        $diff = $formatter->totalPaid() - $prevTotal;
        $totalDiff = abs($diff);
        $change = $diff >= 0 ? Craft::t('commerceinsights', 'up') : Craft::t('commerceinsights', 'down');
        $percentage =
            $prevTotal == 0
            ? ($totalDiff === 0 ? 0 : 100)
            : ($totalDiff / $prevTotal) * 100;

        if ($format == 'csv') {
            return Plugin::getInstance()->csv->generate('revenue', $formatter->csv());
        }

        $totals = $formatter->totals();

        $summaryHtml = Craft::$app->view->renderTemplate('commerceinsights/revenue/summary', [
            'total' => $totals['Total'],
            'min' => $min,
            'max' => $max,
            'change' => $change,
            'diff' => $totalDiff,
            'diffPercentage' => $percentage,
        ]);

        $totalsHtml = Craft::$app->view->renderTemplate('commerceinsights/revenue/totals', [
            'totals' => $totals,
        ]);

        $data = [
            'formatter' => $formatter::$key,
            'summary' => $summaryHtml,
            'totals' => $totals, // $totalsHtml,
            'chartShowsCurrency' => $formatter->showsCurrency(),
            'chartData' => $formatter->format(),
            'secondaryChartData' => $prevPeriodFormatter->format(),
            'min' => $min,
            'max' => $max,
            'range' => $this->params->range(),
            'tableData' => $rows,
        ];

        // if ($request->isAjax || $format == 'json') {
        return $this->asJson($data);
        // }

        // return $this->renderTemplate('commerceinsights/revenue/_index', $data);
    }
}
