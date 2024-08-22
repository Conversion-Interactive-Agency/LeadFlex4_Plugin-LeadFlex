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
        $this->registerGeo();
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

    public function registerGeo()
    {
        $geo = $this->getGeo();
        header('X-LF-Geo: ' . ($geoData?->country ?? 'false'));
    }

    public function getGeo(): string
    {
        if (!empty(Craft::$app->request->userIP)) {
            try {
                $geoData = json_decode(file_get_contents('https://api.country.is/' . Craft::$app->request->userIP));
            } catch (\Exception $e) {}
        }

        return $geoData?->country ?? 'false';
    }
}
