<?php
namespace conversionia\leadflex;

use Craft;

use conversionia\leadflex\webhooks\DriverReachFormie;
use conversionia\leadflex\webhooks\TenstreetFormie;
use conversionia\leadflex\webhooks\EbeFormie;
use conversionia\leadflex\webhooks\UkgFormie;
use conversionia\leadflex\exporters\GeosheetExporter;

use conversionia\leadflex\assets\ControlPanel;

use craft\feedme\events\FeedProcessEvent;
use craft\feedme\services\Process;

use verbb\formie\events\RegisterIntegrationsEvent;
use verbb\formie\services\Integrations;

use yii\base\Event;
use yii\base\Exception;
use yii\base\Module;
use yii\base\InvalidConfigException;

use craft\errors\ElementNotFoundException;
use craft\elements\Entry;
use craft\base\Element;
use craft\events\ModelEvent;
use craft\events\RegisterElementExportersEvent;
use craft\helpers\StringHelper;

class LeadFlex extends Module
{
    public $key = 'jobs';
    /**
     * @var string
     */
    public $controllerNamespace;

    /**
     * Initializes the plugin.
     */
    public function init()
    {
        parent::init();

        // Set alias for this module
        Craft::setAlias('@conversionia', __DIR__);

        $request = Craft::$app->getRequest();

        // Adjust controller namespace for console requests
        if ($request->getIsConsoleRequest()) {
            $this->controllerNamespace = 'conversionia\leadflex\console\controllers';
            $this->_registerConsoleEventListeners();
        } else {
            if ($request->getIsCpRequest()) {
                Craft::$app->view->registerAssetBundle(ControlPanel::class);
                $this->_registerExporters();
            }
        }

        $this->_registerFormieIntegrations();
        $this->_registerSaveEntryEvents();
    }

    private function _registerConsoleEventListeners()
    {
        Event::on(Process::class, Process::EVENT_STEP_BEFORE_PARSE_CONTENT, [$this, 'beforeParseContent']);
    }

    private function _registerSaveEntryEvents()
    {
        Event::on(Entry::class, Element::EVENT_BEFORE_SAVE, [$this, 'entryBeforeSave']);
        Event::on(Entry::class, Element::EVENT_AFTER_SAVE, [$this, 'entryAfterSave']);
    }

    /**
     * @throws InvalidConfigException
     * Removing the slug field from the feed mapping for the jobs section
     */
    public function beforeParseContent(FeedProcessEvent $event)
    {
        $entry = $event->element;
        if(!$entry instanceof Entry){
            return false;
        }

        $handle = strtolower($entry->section->handle);
        if (!$entry->isNewForSite && $handle == $this->key) {
            unset($event->feed['fieldMapping']['slug']);
        }
    }

    function entryBeforeSave(ModelEvent $event)
    {
        $entry = $event->sender;
        $handle = strtolower($entry->section->handle);
        $validated = $handle === $this->key;

        if ($validated) {
            $location = $entry->getFieldValue('location');
            $isStatewide = empty($location['city']);
            $event->sender->setFieldValue('statewideJob', $isStatewide);
        }

        // If it has an ID and the slug is not protected, then we can update the slug.
        if ($validated && isset($entry->id) && !$entry->getFieldValue('protectedSlug')) {
            $entry->setFieldValue('oldSlug', $entry->slug);
            $defaultJob = $entry->getFieldValue('defaultJobDescription')->one();
            $titleText = !empty($entry->adHeadline) ? $entry->adHeadline : (!empty($defaultJob->adHeadline) ? $defaultJob->adHeadline : $defaultJob->title);
            $title = StringHelper::slugify($titleText);
            $entry->slug = $title . "-" . $entry->id;
            $entry->setFieldValue('protectedSlug', true);
        }
    }

    /**
     * @throws Exception
     * @throws \Throwable
     * @throws ElementNotFoundException
     */
    function entryAfterSave(ModelEvent $event)
    {
        $entry = $event->sender;
        $handle = strtolower($entry->section->handle);
        $validated = $handle === $this->key && $entry->firstSave;

        if ($validated) {
            $id = $entry->id;

            $defaultJob = $entry->getFieldValue('defaultJobDescription')->one();
            $titleText = !empty($entry->adHeadline) ? $entry->adHeadline : (!empty($defaultJob->adHeadline) ? $defaultJob->adHeadline : $defaultJob->title);
            $title = StringHelper::slugify($titleText);

            $entry->slug = $title . "-" . $id;
            $entry->firstSave = false;
            Craft::$app->elements->saveElement($entry);
        }
    }

    /**
     * Register custom webhook for Formie.
     */
    private function _registerFormieIntegrations()
    {
        Event::on(
            Integrations::class,
            Integrations::EVENT_REGISTER_INTEGRATIONS,
            static function(RegisterIntegrationsEvent $event) {
                $event->webhooks[] = TenstreetFormie::class;
                $event->webhooks[] = DriverReachFormie::class;
                $event->webhooks[] = EbeFormie::class;
                $event->webhooks[] = UkgFormie::class;
            }
        );
    }

    private function _registerExporters()
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
}
