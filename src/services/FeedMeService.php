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

use conversionia\leadflex\Leadflex;
use craft\base\Component;
use yii\base\Event;
use verbb\formie\services\Integrations;
use verbb\formie\events\RegisterIntegrationsEvent;

use craft\feedme\events\FeedProcessEvent;
use craft\feedme\services\Process;

use conversionia\leadflex\helpers\EntryHelper;

class FeedMeService extends Component
{
    public function registerEvents(): void
    {
        Event::on(Process::class, Process::EVENT_STEP_BEFORE_PARSE_CONTENT, [$this, 'beforeParseContent']);
    }

    public function beforeParseContent(FeedProcessEvent $event)
    {
        $entry = $event->element;
        $feedData = $event->feedData;

        if (!EntryHelper::isJobEntry($entry)) {
            return false;
        }

        $isExistingElement = $entry->id;

        if ($isExistingElement) {
            unset($event->feed['fieldMapping']['title']);
            unset($event->feed['fieldMapping']['slug']);

            if ($this->doesLocationMatch($entry, $feedData)){
                unset($event->feed['fieldMapping']['location']);
            }

            return $event;
        }
    }

    // FeedMe recognizing changes every time with locations.
    // Solution is to remove from the feed['fieldMapping'] if the key places are the same.
    private function doesLocationMatch($entry, $feedData): bool
    {
        $currentLocation = $entry->getFieldValue('location');
        if ($currentLocation === null) {
            return false;
        }

        $locationKeys = Leadflex::$plugin->getSettings()->locationKeys;

        foreach ($locationKeys as $key => $value) {
            if(!isset($feedData[$value])) {
                return false;
            }

            if ($currentLocation[$key] != $feedData[$value]){
                return false;
            }
        }
        return true;
    }
}
