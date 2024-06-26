<?php
/**
 * Reporter plugin for Craft CMS 3.x
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\services;

use craft\base\Component;
use yii\base\Event;
use verbb\formie\services\Integrations;
use verbb\formie\events\RegisterIntegrationsEvent;

use conversionia\leadflex\webhooks\DriverReachFormie;
use conversionia\leadflex\webhooks\TenstreetFormie;
use conversionia\leadflex\webhooks\EbeFormie;
use conversionia\leadflex\webhooks\UkgFormie;
use conversionia\leadflex\webhooks\TalentDriverFormie;
use conversionia\leadflex\webhooks\TruckRightFormie;
use conversionia\leadflex\webhooks\SalesForceFormie;
use conversionia\leadflex\webhooks\StarsCampusFormie;

class WebhooksService extends Component
{
    public function registerEvents()
    {
        Event::on(
            Integrations::class,
            Integrations::EVENT_REGISTER_INTEGRATIONS,
            static function(RegisterIntegrationsEvent $event) {
                $event->webhooks[] = DriverReachFormie::class;
                $event->webhooks[] = EbeFormie::class;
                $event->webhooks[] = SalesForceFormie::class;
                $event->webhooks[] = StarsCampusFormie::class;
                $event->webhooks[] = TalentDriverFormie::class;
                $event->webhooks[] = TenstreetFormie::class;
                $event->webhooks[] = TruckRightFormie::class;
                $event->webhooks[] = UkgFormie::class;
            }
        );
    }
}
