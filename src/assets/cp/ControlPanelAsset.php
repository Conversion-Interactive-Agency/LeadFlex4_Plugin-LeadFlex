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
            'js/app.js',
        ];

        //$this->css = [
        //    'css/app.css',
        //];

        parent::init();
    }
}
