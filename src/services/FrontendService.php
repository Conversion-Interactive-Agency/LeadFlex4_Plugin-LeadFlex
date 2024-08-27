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
use craft\elements\Entry;
use yii\base\Event;

use conversionia\leadflex\assets\site\SiteAsset;

use conversionia\leadflex\twigextensions\BusinessLogicTwigExtensions;
use conversionia\leadflex\twigextensions\FrontendTwigExtensions;
use conversionia\leadflex\twigextensions\TwigFiltersExtensions;
use conversionia\leadflex\helpers\SubmissionHelper;
use conversionia\leadflex\helpers\EntryHelper;

use conversionia\leadflex\variables\LeadflexVariable;
use craft\web\twig\variables\CraftVariable;

class FrontendService extends Component
{

    private $convirza = [];

    public function registerFrontend()
    {
        $this->registerVariables();
        $this->registerPluginVariable();
        $this->registertAssets();
    }

    public function registerVariables()
    {
        $extensions = [
            BusinessLogicTwigExtensions::class,
            // FrontendTwigExtensions::class,
            TwigFiltersExtensions::class
        ];

        foreach ($extensions as $extension) {
            Craft::$app->view->registerTwigExtension(new $extension);
        }
    }

    public function registerPluginVariable()
    {
        // Register our plugin
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('leadflex', LeadflexVariable::class);
            }
        );
    }

    public function registertAssets()
    {
        Craft::$app->view->registerAssetBundle(SiteAsset::class);
    }
    
    public function getConvirza($job): array
    {
        if (!empty($this->convirza)) return $this->convirza;
        $project = Entry::find()->section('project')->one();
        if (EntryHelper::doFieldsExists($job, ['customPhoneTag', 'phone'])) {
            $this->convirza['tag'] = $job->getFieldValue('customPhoneTag') ?? $project->getFieldValue("customPhoneTag");
            $this->convirza['number'] = $job->getFieldValue('phone') ?? $project->getFieldValue("phone");
            $this->convirza['tel'] = "tel:".SubmissionHelper::cleanPhone($this->convirza['number']);
        } else {
            $this->convirza['tag'] = $project->getFieldValue("customPhoneTag");
            $this->convirza['number'] = $project->getFieldValue("phone");
            $this->convirza['tel'] = "tel:".SubmissionHelper::cleanPhone($this->convirza['number']);
        }
        return $this->convirza;
    }
    public function buildConsentBanner() : string
    {
        $template = Leadflex::$plugin->getSettings()->cookieConsentBannerPath;
        if (Craft::$app->view->doesTemplateExist($template)) {
            return Craft::$app->view->renderTemplate($template);
        } else {
            return $this->defaultConsentBanner();
        }
    }

    public function defaultConsentBanner()
    {
        $settings = Leadflex::$plugin->getSettings();
        return "<section class='fixed left-0 bottom-0 right-0 z-10 p-4' data-component='consent-modal'>
              <div class='container xl:max-w-[70rem] mx-auto py-6 px-12 md:px-24 border bg-white content relative'>
                <svg class='absolute top-4 right-4 p-2 h-12 w-12 cursor-pointer' id='dismissSelection'>
                  <use xlink:href='#close'></use>
                </svg>
                <div class='tab' id='tab-1'>
                  <h2 class='mb-4'>We Value Your Privacy</h2>
                  <div class='mb-8'>
                    ". $settings->cookieConsentBannerText ."
                  </div>
                  <div id='consent-cookie-types' class='flex flex-col mb-6 hidden'>
                    <div class='flex'>
                      <input type='checkbox' name='consent-cookie-marketing' id='consent-cookie-marketing' class='consent-checkbox' data-consent-types='ad_personalization' checked=''>
                      <label for='consent-cookie-marketing' class='input-toggle'>Toggle marketing cookies</label>
                      <label for='consent-cookie-marketing'>Marketing Cookies</label>
                    </div>
                    <div class='flex'>
                      <input type='checkbox' name='consent-cookie-conversion' id='consent-cookie-conversion' class='consent-checkbox' data-consent-types='ad_storage,ad_user_data' checked=''>
                      <label for='consent-cookie-conversion' class='input-toggle'>Toggle conversion tracking cookies</label>
                      <label for='consent-cookie-conversion'>Conversion Tracking Cookies</label>
                    </div>
                    <div class='flex'>
                      <input type='checkbox' name='consent-cookie-analytics' id='consent-cookie-analytics' class='consent-checkbox' data-consent-types='analytics_storage' checked=''>
                      <label for='consent-cookie-analytics' class='input-toggle'>Toggle analytics cookies</label>
                      <label for='consent-cookie-analytics'>Analytics</label>
                    </div>
                  </div>
                  <div>
                    <button class='button secondary uppercase' id='acceptSelection'>I understand</button>
                    <button class='button secondary is-inverse uppercase' id='selectCookieTypes'>Cookie Preferences</button>
                  </div>
                </div>
              </div>
            </section>
        ";
    }
}
