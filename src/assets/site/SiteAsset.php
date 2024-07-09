<?php
namespace conversionia\leadflex\assets\site;

use craft\web\AssetBundle;

class SiteAsset extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@conversionia/assets/dist';


        $this->js = [
            'js/site.js',
        ];

        //$this->css = [
        //    'css/site.css',
        //];

        parent::init();
    }
}
