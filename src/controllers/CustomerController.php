<?php

namespace fostercommerce\commerceinsights\controllers;

use Craft;
use fostercommerce\commerceinsights\bundles\ChartBundle;
use fostercommerce\commerceinsights\formatters\BaseFormatter;
use fostercommerce\commerceinsights\Plugin;
use fostercommerce\commerceinsights\services\ParamParser;
use Tightenco\Collect\Support\Collection;
use yii\web\Response;
use craft\commerce\elements\Order;

class CustomerController extends \craft\web\Controller
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

    /**
     * Index of sales
     */
    public function actionCustomerIndex($format='html')
    {
        $min = $this->params->start();
        $max = $this->params->end();

        $query = Order::find()
            ->isCompleted(true)
            ->dateOrdered(['and', ">{$min}", "<{$max}"])
            ->orderBy(implode(' ', [Craft::$app->request->getParam('sort') ?: 'dateOrdered', Craft::$app->request->getParam('dir') ?: 'asc']));

        $q = Craft::$app->request->getParam('q');
        if (!empty($q)) {
            $query = $query->search($q);
        }

        $rows = collect($query->all());

        $formatterClass = BaseFormatter::getFormatter(Craft::$app->request->getParam('formatter'));
        $formatter = new $formatterClass($rows);

        $data = [
            'formatter' => $formatter::$key,
            'chartShowsCurrency' => $formatter->showsCurrency(),
            'totals' => $formatter->totals(),
            'chartData' => $formatter->format(),
            'min' => $min,
            'max' => $max,
            'chartTable' => $this->getView()->renderTemplate('commerceinsights/_table', ['data' => $rows]),
        ];

        if (Craft::$app->request->isAjax || $format == 'json') {
            return $this->asJson($data);
        }

        return $this->renderTemplate('commerceinsights/customers/_index', $data);
    }
}
