<?php

namespace fostercommerce\commerceinsights\bundles;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class VueBundle extends AssetBundle
{
    public function init()
    {
        // $craftEnvironment = defined('CRAFT_ENVIRONMENT') ? CRAFT_ENVIRONMENT : 'production';
        // $isProduction = $craftEnvironment === 'production';

        $this->sourcePath = __DIR__ . '/../front/dist/';

        $this->depends = [CpAsset::class];

        // $jsPattern = $isProduction ? "/\d+\\.bundle\\.min\\.js/" : "/\d+\\.bundle\\.js/";
        // $cssPattern = "/.+\\.css/";

        // $files = scandir($this->sourcePath);
        // $jsFiles = ['main.js'];
        // $cssFiles = [];
        // foreach ($files as $file) {
        //     if (preg_match($jsPattern, $file)) {
        //         $jsFiles[] = $file;
        //     } elseif (preg_match($cssPattern, $file)) {
        //         $cssFiles[] = $file;
        //     }
        // }

        $this->js = ['main.js'];
        // $this->css = $cssFiles;

        parent::init();
    }
}
