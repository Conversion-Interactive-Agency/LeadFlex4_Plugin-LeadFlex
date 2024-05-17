<?php
/**
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\services;

use conversionia\leadflex\twigextensions\BusinessLogicTwigExtensions;
use conversionia\leadflex\assets\site\SiteAsset;
use Craft;
use craft\base\Component;

class FrontendService extends Component
{
    public function init()
    {
        $this->registerVariables();
        $this->regisertAssets();
    }
    public function registerVariables()
    {
        $extensions = [
            BusinessLogicTwigExtensions::class,
        ];

        foreach ($extensions as $extension) {
            Craft::$app->view->registerTwigExtension(new $extension);
        }
    }

    public function regisertAssets()
    {
        Craft::$app->view->registerAssetBundle(SiteAsset::class);
    }
}
