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
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use Twig\Markup;
use yii\base\Event;
use conversionia\leadflex\Leadflex;
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
        $this->registerEvents();
        $this->registerVariables();
        $this->registerAssets();
    }

    public function registerVariables() :void
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

    public function registerEvents() : void
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

        // Base template directory
        Event::on(View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS,
            function (RegisterTemplateRootsEvent $event) {
                $id = Leadflex::$plugin->id;
                $event->roots[$id] = Leadflex::$plugin->getBasePath() . DIRECTORY_SEPARATOR .'templates';
            }
        );

        if (!Leadflex::$plugin->getSettings()->disableConsentBanner){
            // Inject the Consent Banner
            Event::on(
                View::class,
                View::EVENT_END_BODY,
                function () {
                    echo Leadflex::$plugin->frontend->buildConsentBanner();
                }
            );
        };
    }

    public function registerAssets() : void
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
        $template = Leadflex::$plugin->getSettings()->consentBannerPath;
        if (Craft::$app->view->doesTemplateExist($template)) {
            return Craft::$app->view->renderTemplate($template);
        } else {
            return $this->defaultConsentBanner();
        }
    }

    public function buildBannerMessage() : Markup
    {
        $settings = Leadflex::$plugin->getSettings();
        $message = $settings->consentBannerText;

        if (empty($message)) {
            // get siteUrl of the project using the Craft
            $siteUrl = Craft::$app->getSites()->getCurrentSite()->baseUrl;
            // remove the protocol from the url and remove the last character if it is a slash
            $siteUrl = rtrim(preg_replace('/^https?:\/\//', '', $siteUrl), '/');

            $ppEntry = Entry::find()->section('page')->slug('privacy-policy')->one();
            $pp = $ppEntry ? "<a href='". $ppEntry->url ."'>Privacy Policy</a>" : "Privacy Policy";

            $message = "<h2 class='font-bold text-lg mb-2'>We Value Your Privacy</h2><p class='text-base mb-5'>Welcome to <b>". $siteUrl ."</b>! We're glad you're here and want you to know that we respect your 
            privacy and your right to control how we collect and use your personal data. Please read our <?php echo $pp; ?>
            to learn about our privacy practices or click 'Customize Preferences' to exercise control over your data.</p>";
        }

        return new Markup($message, Craft::$app->charset);
    }

    public function defaultConsentBanner() : string
    {
        $message = $this->buildBannerMessage();
        // Get view services
        $view = Craft::$app->getView();

        $templatePath = 'leadflex/site/consentBanner';
        $template = $view->renderTemplate($templatePath, [
            'message' => $message,
        ]);

        return $template;
    }
}
