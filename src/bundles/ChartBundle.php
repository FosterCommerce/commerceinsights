<?php

namespace fostercommerce\commerceinsights\bundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ChartBundle extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = __DIR__ . '/../../dist/';

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            'bundle.min.js',
        ];

        $this->css = [
            'styles.css',
        ];

        parent::init();
    }
}
