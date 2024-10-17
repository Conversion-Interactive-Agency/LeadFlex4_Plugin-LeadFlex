<?php
namespace conversionia\leadflex\assets\map;

use craft\web\AssetBundle;

class MapAsset extends AssetBundle
{
    public function init()
    {
        // define the path that your publishable resources live
        $this->sourcePath = '@conversionia/assets/dist';


        $this->js = [
            'js/map.js',
        ];

        $this->css = [
           'css/map.css',
        ];

        parent::init();
    }
}
