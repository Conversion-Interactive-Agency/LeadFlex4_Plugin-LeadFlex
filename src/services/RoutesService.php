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
use yii\base\Event;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;


class RoutesService extends Component
{
    public function registerEvents()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules["jobs/<entryId:[0-9]+>"] = 'leadflex/routes/jobs';
                $event->rules["jobs/<entryId:[0-9]+>/<slug:[^\/]+>"] = 'leadflex/routes/jobs';
            }
        );
    }
}
