<?php

namespace fostercommerce\commerceinsights\services;

use Craft;
use yii\base\Component;
use League\Csv\Writer;

class Csv extends Component
{
   public function generate($module, $data)
    {

        $csv = Writer::createFromString('');
        // Always output a CSV, whether its empty or not
        if (sizeof($data) > 0) {
            $csv->insertAll($data);
        }
        $csv->output("{$module}.csv");
        exit(0);
    }
}