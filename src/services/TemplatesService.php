<?php

namespace conversionia\leadflex\services;

use conversionia\leadflex\Leadflex;
use craft\base\Component;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use yii\base\Event;

class TemplatesService extends Component
{
    public function registerTemplates()
    {
        // Base template directory
        Event::on(View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event) {
                $id = Leadflex::$plugin->id;
                $event->roots[$id] = Leadflex::$plugin->getBasePath() . DIRECTORY_SEPARATOR .'templates';
            }
        );
    }
}
