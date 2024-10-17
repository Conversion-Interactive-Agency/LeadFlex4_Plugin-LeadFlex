<?php
/**
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\services;

use conversionia\leadflex\assets\site\SiteAsset;

use yii\base\Event;

use Craft;
use craft\base\Component;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\web\View;

class RoutesService extends Component
{
    public function registerEvents()
    {
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                // handling urls from initial url patterns to match with new
                $event->rules["jobs/<entryId:[0-9]+>"] = 'leadflex/routes/jobs';
                $event->rules["jobs/<entryId:[0-9]+>/<slug:[^\/]+>"] = 'leadflex/routes/jobs';
                $event->rules["jobs/<slug:[^\/]+>"] = 'leadflex/routes/old-jobs';

                // merging of LeadFlex domains
                $event->rules["leadflex/jobs.json"] = 'leadflex/json/index';

                // Overlap Map Tool
                $event->rules["leadflex/map"] = 'leadflex/map/index';
                $event->rules["leadflex/map/locations"] = 'leadflex/map/locations';
            }
        );

        Event::on(
            View::class,
            View::EVENT_END_BODY,
            function (Event $event) {
                Craft::$app->getView()->registerAssetBundle(SiteAsset::class);
            }
        );
    }
}
