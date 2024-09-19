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

use Craft;

use craft\base\Component;

use conversionia\leadflex\helpers\EntryHelper;

use HTMLPurifier_Exception;
use yii\base\Event;
use craft\events\ModelEvent;
use craft\elements\Entry;
use craft\base\Element;
use craft\helpers\StringHelper;

use craft\htmlfield\events\ModifyPurifierConfigEvent;
use craft\redactor\Field;

class EntryService extends Component
{
    public function registerEvents()
    {
        Event::on(Entry::class, Element::EVENT_BEFORE_SAVE, [$this, 'entryBeforeSave']);
        Event::on(Field::class, Field::EVENT_MODIFY_PURIFIER_CONFIG, [$this, 'modifyPurifierConfig']);
    }

    function entryBeforeSave(ModelEvent $event)
    {
        $entry = $event->sender;

        // Required Fields Check
        $fields = ['location','statewideJob','advertiseJob','assignedCampaign','defaultJobDescription'];
        if (!EntryHelper::doFieldsExists($entry, $fields)) {
            return null;
        }

        // Check if the entry has a campaign - if not, prevent from being included into XML feed / jobs.json
        $hasCampaign = !is_null($entry->getFieldValue('assignedCampaign')->one());
        $includeJobCampaignEvaluation = Leadflex::$plugin->getSettings()->includeJobCampaignEvaluation;
        if ($includeJobCampaignEvaluation && (!$entry->enabled || !$hasCampaign)) {
            $event->sender->setFieldValue('advertiseJob', 'false');
            $event->sender->setFieldValue('assignedCampaign', []);
        }

        // rebuilding slugs w/ the defaultJobDescription or adHeadline
        $disableCustomSlugGeneration = Leadflex::$plugin->getSettings()->disableCustomSlugGeneration;
        if (!$disableCustomSlugGeneration)
        {
            $defaultJob = $entry->getFieldValue('defaultJobDescription')->one();
            if (!is_null($defaultJob)){
                $job = $this->mergeEntries($entry, $defaultJob);
                $titleText = $job->adHeadline ?: $defaultJob->title;
                $entry->slug = StringHelper::slugify($titleText);
            }
        }

        // Set statewideJob based on location - used for jobSearch component and orderBy
        $location = $entry->getFieldValue('location');
        $isStatewide = empty($location['city']);
        $event->sender->setFieldValue('statewideJob', $isStatewide);
    }

    // Consent Banner triggers
    /**
     * @throws HTMLPurifier_Exception
     */
    public function modifyPurifierConfig(ModifyPurifierConfigEvent $event): void
    {
        $config = $event->config;
        $def = $config->getDefinition('HTML', true);

        if ($def) {
            $def->addAttribute('a','data-consent', 'Text',);
            $def->addAttribute('a','data-consent-view', 'Text',);
        }
    }

    // Frontend Services
    public function mergeEntries(Entry $primary, Entry $fallback = null) : Entry
    {
        $job = clone $primary;
        if (is_null($fallback)) return $primary;

        // Build array of fields handles from $fallback
        $fallbackFields = $fallback->getType()->getFieldLayout()->getCustomFields();
        $fallbackFieldHandles = array_column($fallbackFields, 'handle');

        // Merging logic will go here
        foreach ($primary->getFieldLayout()->getCustomFields() as $field) {
            $handle = $field->handle;
            $value = $primary->getFieldValue($handle);
            $job->setfieldValue($handle, $value);

            // Check if primary field is empty
            if (empty($value) && in_array($handle, $fallbackFieldHandles)) {
                // Check if fallback has the field and it's not empty
                $fallbackValue = $fallback->getFieldValue($handle);
                if (!empty($fallbackValue)) {
                    // Assign fallback value to primary
                    $job->setfieldValue($handle, $fallbackValue);
                }
            }
        }

        return $job;
    }

    public function buildExternalApplicationUrl($job)
    {
        $view = Craft::$app->getView();
        $referrer = $view->getTwig()->getGlobals()['referrer'];

        // Get the value from field defaultAtsLinkglobal in the global group "project"
        $project = Entry::find()->section('project')->one();
        $baseUrl = $job["atsLink"] ?: $project->getFieldValue("atsLink");
        $intelliAppUrlHasQuery = strpos($baseUrl, "?") !== false;
        $atsReferrerFormat = $project->getFieldValue("atsApplyButtonFormatting");
        $referrerKey = $intelliAppUrlHasQuery ? str_replace("?", "&", $atsReferrerFormat) : str_replace("&", "?", $atsReferrerFormat);
        return $baseUrl . $referrerKey . $referrer;
    }
}
