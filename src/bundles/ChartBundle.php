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

        $this->sourcePath = __DIR__ . '/../../dist/';

        $this->depends = [CpAsset::class];

        $jsPattern = $isProduction ? "/\d+\\.bundle\\.min\\.js/" : "/\d+\\.bundle\\.js/";
        $cssPattern = "/.+\\.css/";

        $files = scandir($this->sourcePath);
        $jsFiles = [$isProduction ? 'bundle.min.js' : 'bundle.js'];
        $cssFiles = [];
        foreach($files as $file) {
            if (preg_match($jsPattern, $file)) {
                $jsFiles[] = $file;
            } else if (preg_match($cssPattern, $file)) {
                $cssFiles[] = $file;
            }
        }

        $this->js = $jsFiles;
        $this->css = $cssFiles;

        parent::init();
    }
}
