<?php
namespace conversionia\leadflex\assets\cp;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class ControlPanelAsset extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@conversionia/assets/cp/dist';

        // define the dependencies
        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/leadflex-cp.js',
        ];

        $this->css = [
            'css/leadflex-cp.css',
        ];

        parent::init();
    }
}
