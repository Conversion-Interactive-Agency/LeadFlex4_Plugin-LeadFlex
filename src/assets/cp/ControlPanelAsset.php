<?php
namespace conversionia\leadflex\assets\cp;

use craft\web\AssetBundle;

class ControlPanelAsset extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@conversionia/assets/cp/dist';


        $this->js = [
            'js/cp.js',
        ];

        //$this->css = [
        //    'css/cp.css',
        //];

        parent::init();
    }
}
