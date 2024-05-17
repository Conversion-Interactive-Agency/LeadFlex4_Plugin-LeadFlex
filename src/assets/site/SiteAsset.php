<?php
namespace conversionia\leadflex\assets\site;

use craft\web\AssetBundle;

class SiteAsset extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@conversionia/assets/site/dist';


        $this->js = [
            'js/app.js',
        ];

        //$this->css = [
        //    'css/app.css',
        //];

        parent::init();
    }
}
