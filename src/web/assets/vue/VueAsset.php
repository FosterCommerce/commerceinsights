<?php

namespace fostercommerce\commerceinsights\web\assets\vue;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class VueAsset extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = __DIR__ . '/js/dist/';

        $this->depends = [CpAsset::class];

        $manifest = file_get_contents("{$this->sourcePath}manifest.json");
        $manifest = json_decode($manifest, true);

        $this->js = [$manifest['main.js']];

        parent::init();
    }
}
