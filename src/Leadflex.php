<?php
/**
 * Leadflex plugin for Craft CMS 3.x
 *
 * This is a generic Craft CMS plugin
 *
 * @link      http://DoeDesign.com/
 * @copyright Copyright (c) 2023 Eric LaFontsee
 */

namespace conversionia\leadflex;

use conversionia\leadflex\assets\ControlPanel;
use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;

use conversionia\leadflex\services\WebhooksService;
use conversionia\leadflex\services\ExportsService;
use conversionia\leadflex\services\EntryService;
use conversionia\leadflex\services\FeedmeService;
use conversionia\leadflex\services\TwigVariablesService;

use craft\elements\Entry;
use craft\base\Element;
use craft\events\ModelEvent;

use craft\helpers\StringHelper;

/**
 * Craft plugins are very much like little applications in and of themselves. We’ve made
 * it as simple as we can, but the training wheels are off. A little prior knowledge is
 * going to be required to write a plugin.
 *
 * For the purposes of the plugin docs, we’re going to assume that you know PHP and SQL,
 * as well as some semi-advanced concepts like object-oriented programming and PHP namespaces.
 *
 * https://docs.craftcms.com/v3/extend/
 *
 * @author    Eric LaFontsee
 * @package   Leadflex
 * @since     1.0.0
 *
 */
class Leadflex extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * Static property that is an instance of this plugin class so that it can be accessed via
     * Leadflex::$plugin
     *
     * @var Leadflex
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public $hasCpSettings = false;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public $hasCpSection = false;

    // Public Methods
    // =========================================================================

    /**
     * Set our $plugin static property to this class so that it can be accessed via
     * Leadflex::$plugin
     *
     * Called after the plugin class is instantiated; do any one-time initialization
     * here such as hooks and events.
     *
     * If you have a '/vendor/autoload.php' file, it will be loaded for you automatically;
     * you do not need to load it in your init() method.
     *
     */
    public $section = 'jobs';
    /**
     * @var string
     */
    public $controllerNamespace;

    public function init()
    {
        parent::init();

        // Set alias for this module
        Craft::setAlias('@conversionia', __DIR__);

        // Register our services
        $this->setComponents([
            'controlpanel' => ControlPanel::class,
            'entry' => EntryService::class,
            'exports' => ExportsService::class,
            'feedme' => FeedmeService::class,
            'webhooks' => WebhooksService::class,
            'twig' => TwigVariablesService::class
        ]);

        // Register Events
        $request = Craft::$app->getRequest();
        // Adjust controller namespace for console requests
        if ($request->getIsConsoleRequest()) {
            $this->controllerNamespace = 'conversionia\leadflex\console\controllers';
            $this->entry->registerEvents();
            $this->feedme->registerEvents();
        }

        if ($request->getIsCpRequest()) {
            $this->controlpanel->init();
            $this->exports->registerEvents();
            $this->webhooks->registerEvents();
        }

        if($request->getIsSiteRequest){
            $this->twig->registerVariables();
        }
    }
}
