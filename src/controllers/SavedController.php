<?php

namespace fostercommerce\commerceinsights\controllers;

use Craft;
use craft\db\Query;
use craft\helpers\Json;
use fostercommerce\commerceinsights\Plugin;
use fostercommerce\commerceinsights\elements\SavedReport;
use yii\web\Response;

class SavedController extends \craft\web\Controller
{
    public $enableCsrfValidation = false;

    protected $allowAnonymous = ['actionSaveReport', 'actionList', 'actionGetReport'];

    private function required($value)
    {
        $set = isset($value);

        if (is_string($value)) {
            return $set && strlen(trim($value)) > 0;
        }

        return $set;
    }

    public function actionSaveReport()
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $data = Json::decode(Craft::$app->request->getRawBody(), true);

        if (
            !$this->required($data['type']) ||
            !$this->required($data['name']) ||
            !$this->required($data['query'])
        ) {
            return $this->asErrorJson('Missing required fields');
        }

        $savedReport = new SavedReport([
            'type' => $data['type'],
            'name' => $data['name'],
            'query' => $data['query'],
        ]);

        $result = Craft::$app->elements->saveElement($savedReport, false);

        return $this->asJson(['success' => $result]);
    }

    public function actionList()
    {
        return $this->asJson(
            (new Query())
                ->select(['*'])
                ->from('{{%saved_reports}}')
                ->all()
        );
    }

    public function actionGetReport($id)
    {
        $results = (new Query())
            ->select(['*'])
            ->from('{{%saved_reports}}')
            ->where(['id' => $id])
            ->all();

        if (sizeof($results) > 0) {
            return $this->asJson(json_decode($results[0]['query']));
        }

        return $this->asJson((object) []);
    }

    public function actionDeleteReport($id)
    {
        $result = Craft::$app->getDb()->createCommand()
            ->delete('{{%saved_reports}}', ['id' => $id])
            ->execute();

        return $this->asJson(['success' => $result === 1]);
    }
}
