<?php
/**
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\services;

use Craft;
use craft\base\Component;
use craft\elements\Entry;
use craft\web\twig\variables\CraftVariable;

use conversionia\leadflex\twigextensions\BusinessLogicTwigExtensions;
use conversionia\leadflex\variables\LeadflexVariable;

class TwigVariablesService extends Component
{

    public function init()
    {
        // Register our variables within the Craft Variable
        // example -> craft.leadflex.{function()}
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('leadflex', LeadflexVariable::class);
            }
        );
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
}

