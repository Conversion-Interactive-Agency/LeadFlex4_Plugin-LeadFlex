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
use Craft;
use craft\base\Component;

class TwigVariablesService extends Component
{
    public function registerVariables()
    {
        $extensions = [
            BusinessLogicTwigExtensions::class,
        ];

        foreach ($extensions as $extension) {
            Craft::$app->view->registerTwigExtension(new $extension);
        }
    }
}
