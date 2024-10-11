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
use conversionia\leadflex\exporters\GeosheetExporter;
use conversionia\leadflex\webhooks\DriverReachFormie;
use conversionia\leadflex\webhooks\EbeFormie;
use conversionia\leadflex\webhooks\LeasePathFormie;
use conversionia\leadflex\webhooks\SalesForceFormie;
use conversionia\leadflex\webhooks\StarsCampusFormie;
use conversionia\leadflex\webhooks\TalentDriverFormie;
use conversionia\leadflex\webhooks\TenstreetFormie;
use conversionia\leadflex\webhooks\TruckRightFormie;
use conversionia\leadflex\webhooks\UkgFormie;
use craft\base\Element;
use craft\elements\Entry;
use craft\events\ModelEvent;
use craft\events\RegisterElementExportersEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\services\Plugins;
use craft\web\View;
use verbb\formie\elements\Form;
use verbb\formie\events\RegisterIntegrationsEvent;
use verbb\formie\services\Integrations;
use yii\base\Event;
use yii\caching\TagDependency;
use conversionia\leadflex\Leadflex;

class ControlPanelService extends Component
{

    private int $timeout = 10;
    public function registerEvents()
    {
        $this->registerFormUpdates();
        $this->registerWebhooks();
        $this->registerExports();
        $this->registerSettings();
    }

    public function registerExports()
    {
        // Register exporters
        Event::on(
            Entry::class,
            Element::EVENT_REGISTER_EXPORTERS,
            static function (RegisterElementExportersEvent $event) {
                $event->exporters[] = GeosheetExporter::class;
            }
        );
    }

    function registerWebhooks(): void
    {
        Event::on(
            Integrations::class,
            Integrations::EVENT_REGISTER_INTEGRATIONS,
            static function(RegisterIntegrationsEvent $event) {
                $event->webhooks[] = DriverReachFormie::class;
                $event->webhooks[] = EbeFormie::class;
                $event->webhooks[] = LeasePathFormie::class;
                $event->webhooks[] = SalesForceFormie::class;
                $event->webhooks[] = StarsCampusFormie::class;
                $event->webhooks[] = TalentDriverFormie::class;
                $event->webhooks[] = TenstreetFormie::class;
                $event->webhooks[] = TruckRightFormie::class;
                $event->webhooks[] = UkgFormie::class;
            }
        );
    }

    public function registerFormUpdates()
    {
        Event::on(Form::class, Form::EVENT_AFTER_SAVE, function(ModelEvent $event) {
            $cache = Craft::$app->getCache();
            TagDependency::invalidate($cache, 'graphql');
        });
    }

    public function registerSettings(): void
    {
        Event::on(
            Plugins::class,
            Plugins::EVENT_BEFORE_SAVE_PLUGIN_SETTINGS,
            function (Event $event) {
                $plugin = $event->plugin;
                $settings = $plugin->settings;

                if ($plugin->handle === 'leadflex') {
                    if (isset($settings['leadFlexDomains']) && is_array($settings['leadFlexDomains'])) {
                        $updatedDomains = [];
                        foreach ($settings['leadFlexDomains'] as $domainEntry) {
                            if (isset($domainEntry['domain'])) {
                                $domainUrl = rtrim($domainEntry['domain'], '/'); // Remove trailing slashes
                                // Strip any protocol (http:// or https://)
                                $domainUrl = preg_replace('/^https?:\/\//', '', $domainUrl);
                            }
                            if (!$this->isDomainReachable('https://'.$domainUrl)) {
                                Craft::$app->getSession()->setError("The domain '{$domainUrl}' could not be reached and has been removed. Please check the domain and try again. Contact support if you need assistance.");
                            } else {
                                // Update the domain entry
                                $updatedDomains[] = ['domain' => $domainUrl];
                            }
                        }
                        // Update the settings with the validated domains
                        $event->plugin->settings['leadFlexDomains'] = $updatedDomains;
                    }
                }
            }
        );
    }

    private function isDomainReachable(string $domain): bool
    {
        $ch = curl_init($domain);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_NOBODY, true); // Do not download the content
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);

        // Optional: Suppress SSL certificate errors if you want to ignore SSL issues
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        return ($httpCode >= 200 && $httpCode < 400); // Check for a valid HTTP status code
    }
}
