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
use craft\commerce\elements\Order;
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
        $query = Order::find()
            ->isCompleted(true)
            ->dateOrdered(['and', ">={$min}", "<{$max}"])
            ->orderBy(implode(' ', [$request->getParam('sort') ?: 'dateOrdered', $request->getParam('dir') ?: 'asc']));

        $q = $request->getParam('q');
        if (!empty($q)) {
            $query = $query->search($q);
        }

        $rows = collect($query->all());
        return $rows;
    }

    public function actionRevenueIndex($format='html')
    {
        $request = Craft::$app->request;
        $min = $this->params->start();
        $max = $this->params->end();

        $currMin = new DateTime($min);
        $prevMin = (clone $currMin)->sub($currMin->diff(new DateTime($max)));

        $formatterClass = BaseFormatter::getFormatter(Revenue::class);

        $rows = $this->getRevenueQuery($min, $max, $request);
        $prevPeriodRows = $this->getRevenueQuery($prevMin->format('Y-m-d'), $min, $request);

        $formatter = new $formatterClass($rows);
        $prevPeriodFormatter = new $formatterClass($prevPeriodRows);

        $prevTotal = $prevPeriodFormatter->totals()['TotalPaid'];
        $currTotal = $formatter->totals();
        $diff = $currTotal['TotalPaid'] - $prevTotal;
        $totalDiff = abs($diff);
        $change = $diff >= 0 ? Craft::t('commerceinsights', 'up') : Craft::t('commerceinsights', 'down');
        $percentage =
            $prevTotal == 0
            ? ($totalDiff === 0 ? 0 : 100)
            : ($totalDiff / $prevTotal) * 100;

        if ($format == 'csv') {
            return Plugin::getInstance()->csv->generate('revenue', $formatter->csv());
        }

        $summaryHtml = Craft::$app->view->renderTemplate('commerceinsights/revenue/summary', [
            'total' => $currTotal['Total'],
            'min' => $min,
            'max' => $max,
            'change' => $change,
            'diff' => $totalDiff,
            'diffPercentage' => $percentage,
        ]);

        $totalsHtml = Craft::$app->view->renderTemplate('commerceinsights/revenue/totals', [
            'totals' => $currTotal,
        ]);

        $data = [
            'formatter' => $formatter::$key,
            'summary' => $summaryHtml,
            'totals' => $totalsHtml,
            'chartShowsCurrency' => $formatter->showsCurrency(),
            'chartData' => $formatter->format(),
            'min' => $min,
            'max' => $max,
            'range' => $this->params->range(),
            'chartTable' => $this->getView()->renderTemplate('commerceinsights/_table', ['data' => $rows]),
        ];

        if ($request->isAjax || $format == 'json') {
            return $this->asJson($data);
        }

        return $this->renderTemplate('commerceinsights/revenue/_index', $data);
    }
}
