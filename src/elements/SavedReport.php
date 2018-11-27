<?php
namespace fostercommerce\commerceinsights\elements;

use craft\base\Element;

class SavedReport extends Element
{
    public $type;
    public $name;
    public $query;

    public function afterSave(bool $isNew)
    {
        if ($isNew) {
            \Craft::$app->db->createCommand()
                ->insert('{{%saved_reports}}', [
                    'id' => $this->id,
                    'type' => $this->type,
                    'name' => $this->name,
                    'query' => $this->query,
                ])
                ->execute();
        } else {
            \Craft::$app->db->createCommand()
                ->update('{{%saved_reports}}', [
                    'type' => $this->type,
                    'name' => $this->name,
                    'query' => $this->query,
                ], ['id' => $this->id])
                ->execute();
        }

        parent::afterSave($isNew);
    }
}
