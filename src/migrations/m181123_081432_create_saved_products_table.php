<?php

namespace fostercommerce\commerceinsights\migrations;

use Craft;
use craft\db\Migration;

class m181123_081432_create_saved_products_table extends Migration
{
    public function safeUp()
    {
        if (!$this->db->tableExists('{{%saved_reports}}')) {
            $this->createTable('{{%saved_reports}}', [
                'id' => $this->integer()->notNull(),
                'type' => $this->string()->notNull(),
                'name' => $this->string()->notNull(),
                'query' => $this->json()->notNull(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
                'PRIMARY KEY(id)',
            ]);
        }
    }

    public function safeDown()
    {
        if ($this->db->tableExists('{{%saved_reports}}')) {
            $this->db->dropTable('{{%saved_reports}}');
        }
    }
}
