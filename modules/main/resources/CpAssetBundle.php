<?php

namespace modules\main\resources;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class CpAssetBundle extends AssetBundle
{
    public function init()
    {
        $this->sourcePath = '@modules/main/resources/dist';

        // Make sure regular cp styles are loaded before custom ones.
        $this->depends = [CpAsset::class];

        $this->css = [
            'cpstyles.css',
        ];

        $this->js = [
            'cpscripts.js',
        ];

        parent::init();
    }
}
