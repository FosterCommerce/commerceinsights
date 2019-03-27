<?php

namespace fostercommerce\commerceinsights\controllers;

use Craft;
use fostercommerce\commerceinsights\bundles\ChartBundle;
use fostercommerce\commerceinsights\formatters\BaseFormatter;
use fostercommerce\commerceinsights\formatters\Products;
use fostercommerce\commerceinsights\Plugin;
use fostercommerce\commerceinsights\services\ParamParser;
use Tightenco\Collect\Support\Collection;
use yii\web\Response;
use craft\commerce\Plugin as CommercePlugin;
use craft\db\Query;
use craft\helpers\Db;
use DateTime;

class ProductController extends \craft\web\Controller
{

    /** @var ParamParser */
    private $params;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->params = Plugin::getInstance()->paramParser;
    }

    private function asCurrency($value)
    {
        return isset($value) && $value
            ? Craft::$app->formatter->asCurrency($value)
            : Craft::$app->formatter->asCurrency(0);
    }

    public function getProductQuery($min, $max)
    {
        $lineItemQuery = (new Query())
            ->select(['purchasableId', 'COUNT(*) as orderCount', 'SUM(qty) as qty', 'SUM(subTotal) as subTotal'])
            ->from('{{%commerce_lineitems}} lineItems')
            ->leftJoin('{{%commerce_orders}} orders', '[[lineItems.orderId]] = [[orders.id]]')
            ->groupBy(['purchasableId'])
            ->where(['isCompleted' => true])
            ->andWhere(['>=', 'dateOrdered', Db::prepareDateForDb($min)])
            ->andWhere(['<', 'dateOrdered', Db::prepareDateForDb($max)]);

        $query = (new Query())
            ->select([
                'products.id as productId',
                'productTypes.name as productType',
                'productTypes.handle as productTypeHandle',
                'productContent.title as productTitle',
                'variants.sku',
                'lineItems.orderCount',
                'lineItems.qty',
                'lineItems.subTotal',
                'variants.hasUnlimitedStock',
                'variants.stock',
            ])
            ->from(['{{%commerce_variants}} variants'])
            ->leftJoin(['lineItems' => $lineItemQuery], '[[variants.id]] = [[lineItems.purchasableId]]')
            ->leftJoin('{{%commerce_products}} products', '[[variants.productId]] = [[products.id]]')
            ->leftJoin('{{%content}} productContent', '[[products.id]] = [[productContent.elementId]]')
            ->leftJoin('{{%commerce_producttypes}} productTypes', '[[products.typeId]] = [[productTypes.id]]')
            ->where(['>', 'lineItems.orderCount', 0])
            ->orderBy(
                implode(' ', [
                    Craft::$app->request->getParam('sort') ?: 'lineItems.subTotal',
                    Craft::$app->request->getParam('dir') ?: 'desc'
                ])
            );

        // $query
            // ->andWhere(['isCompleted' => true])
            // ->andWhere(['>=', 'dateOrdered', Db::prepareDateForDb($min)])
            // ->andWhere(['<', 'dateOrdered', Db::prepareDateForDb($max)])


        foreach ($this->params->search as $key => $value) {
            $query->andWhere([$key => $value]);
        }

        return collect($query->all());
    }

    public function actionIndex($format='json')
    {
        $statuses = CommercePlugin::getInstance()->orderStatuses;

        $min = $this->params->start();
        $max = $this->params->end();

        $currMin = new DateTime($min);
        $prevMin = (clone $currMin)->sub($currMin->diff(new DateTime($max)))->format('Y-m-d');

        $rows = $this->getProductQuery($min, $max);

        $formatterClass = BaseFormatter::getFormatter(Products::class);
        $formatter = new $formatterClass($rows);

        if ($format == 'csv') {
            return Plugin::getInstance()->csv->generate('products', $formatter->csv());
        }

        $formattedRows = $rows->map(function ($row) {
            // $row['totalPrice'] = $this->asCurrency($row['totalPrice']);
            if ($row['hasUnlimitedStock'] === '1') {
                $row['stock'] = '∞';
            }

            $row['subTotal'] = $this->asCurrency($row['subTotal']);

            return $row;
        });

        $data = [
            'formatter' => $formatter::$key,
            'min' => $min,
            'max' => $max,
            'range' => $this->params->range(),
            'search' => (object) $this->params->search,
            'tableData' => $formattedRows,
        ];

        return $this->asJson($data);
    }
}
