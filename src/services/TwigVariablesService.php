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
use yii\base\Event;

use conversionia\leadflex\twigextensions\BusinessLogicTwigExtensions;
use conversionia\leadflex\twigextensions\FrontendTwigExtensions;
use conversionia\leadflex\twigextensions\TwigFiltersExtensions;

use conversionia\leadflex\variables\LeadflexVariable;
use craft\web\twig\variables\CraftVariable;

class TwigVariablesService extends Component
{

    public function registerFrontend()
    {
        $this->registerVariables();
        $this->registerPluginVariable();

    }

    public function registerVariables()
    {
        $extensions = [
            BusinessLogicTwigExtensions::class,
            // FrontendTwigExtensions::class,
            TwigFiltersExtensions::class
        ];

        foreach ($extensions as $extension) {
            Craft::$app->view->registerTwigExtension(new $extension);
        }
    }

    public function registerPluginVariable()
    {
        // Register our plugin
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
}
