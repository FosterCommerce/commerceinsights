<?php

namespace fostercommerce\commerceinsights\controllers;

use Craft;
use fostercommerce\commerceinsights\bundles\ChartBundle;
use fostercommerce\commerceinsights\formatters\BaseFormatter;
use fostercommerce\commerceinsights\Plugin;
use Tightenco\Collect\Support\Collection;
use yii\web\Response;
use craft\commerce\elements\Order;

class DashboardController extends \craft\web\Controller {

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
    }

    /**
     * Index of sales
     */
    public function actionDashboardIndex($format='html')
    {

        return $this->renderTemplate('commerceinsights/_index');
        
    }

}