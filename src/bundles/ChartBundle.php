<?php

namespace fostercommerce\commerceinsights\bundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ChartBundle extends AssetBundle
{
    public function init()
    {
        $craftEnvironment = defined('CRAFT_ENVIRONMENT') ? CRAFT_ENVIRONMENT : 'production';
        $isProduction = $craftEnvironment === 'production';

        // define the path that your publishable resources live
        $this->sourcePath = __DIR__ . '/../../dist/';

        $this->depends = [CpAsset::class];

        // define the relative path to CSS/JS files that should be registered with the page
        // when this asset bundle is registered
        $this->js = [
            $isProduction ? 'bundle.min.js' : 'bundle.js',
        ];

        $this->css = [
            'styles.css',
        ];

        parent::init();
    }
}
