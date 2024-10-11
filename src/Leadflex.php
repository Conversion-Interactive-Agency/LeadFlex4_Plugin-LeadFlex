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

use conversionia\leadflex\models\Settings;
use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;

use conversionia\leadflex\services\EntryService;
use conversionia\leadflex\services\ControlPanelService;
use conversionia\leadflex\services\FeedMeService;
use conversionia\leadflex\services\FrontendService;
use conversionia\leadflex\services\RoutesService;
use conversionia\leadflex\services\TemplatesService;

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
 * @property  Settings $settings
 * @method    Settings getSettings()
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

    public const EVENT_BEFORE_RETURN_JSON = 'beforeReturnJson';

    // Public Properties
    // =========================================================================

    /**
     * To execute your plugin’s migrations, you’ll need to increase its schema version.
     *
     * @var string
     */
    public string $schemaVersion = '1.0.0';

    /**
     * Set to `true` if the plugin should have a settings view in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSettings = true;

    /**
     * Set to `true` if the plugin should have its own section (main nav item) in the control panel.
     *
     * @var bool
     */
    public bool $hasCpSection = false;

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

    /**
     * @var string
     */
    public $controllerNamespace;

    /**
     * @var mixed|object|null
     */
    public mixed $entry;

    public function init() : void
    {
        parent::init();
        self::$plugin = $this;
        Craft::setAlias('@conversionia', __DIR__);

        // Register services
        $this->setComponents([
            'controlpanel' => ControlPanelService::class,
            'entry' => EntryService::class,
            'feedme' => FeedMeService::class,
            'templates' => TemplatesService::class,
            'routes' => RoutesService::class,
            'frontend' => FrontendService::class,
        ]);

        // Now you can access the services via $this->get('entry') or $this->entry
        $this->entry = $this->get('entry');

        // Set alias for this module


        // Register Events
        $request = Craft::$app->getRequest();
        // Console requests
        if ($request->getIsConsoleRequest()) {
            $this->controllerNamespace = 'conversionia\leadflex\console\controllers';
            $this->feedme->registerEvents();
        }

        // Web requests
        $this->templates->registerTemplates();
        if ($request->getIsCpRequest()) {
            $this->controlpanel->registerEvents();
        } elseif ($request->getIsSiteRequest()) {
            $this->frontend->registerFrontend();
            $this->routes->registerEvents();
        }

        $this->entry->registerEvents();
    }

    // Protected Methods
    // =========================================================================
    /**
     * Creates and returns the model used to store the plugin’s settings.
     *
     *
     */
    protected function createSettingsModel(): Settings
    {
        return new Settings();
    }

    protected function settingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate(
            'leadflex/_cp/settings',
            [ 'settings' => $this->getSettings() ]
        );
    }

    public function setSettings(array $settings): void
    {
        // Hook into the before-save logic here
        $this->beforeSaveSettings($settings);

        // Call the parent setSettings() method to proceed with saving
        parent::setSettings($settings);
    }
}
