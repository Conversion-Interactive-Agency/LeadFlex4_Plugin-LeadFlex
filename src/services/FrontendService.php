<?php
/**
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\services;

use Yii;
use Craft;
use craft\base\Component;
use craft\elements\Entry;
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

use craft\web\View;


class FrontendService extends Component
{

    private $convirza = [];
    private string $leadAssistID;


    public function registerFrontend()
    {
        $this->leadAssistID = Leadflex::$plugin->getSettings()->leadAssistID ?? '';
        $this->registerVariables();
        $this->registerPluginVariable();
        $this->registertAssets();
        $this->registerGeo();
        $this->registerLeadAssistChat();
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

    public function registerLeadAssistChat()
    {
        $leadAssistID = $this->leadAssistID;
        if (!empty($leadAssistID)) {
            Event::on(View::class, View::EVENT_BEFORE_RENDER_PAGE_TEMPLATE,
                function() use ($leadAssistID) {
                    $chatSourceUrl = 'https://leadassist.ai/js/chat-widget.js';
                    Craft::$app->view->registerJsFile($chatSourceUrl, ['client' => $leadAssistID]);
                }
            );
        }
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
        header('X-LF-Geo: ' . $this->getGeo());
    }

    public function getGeo(): string
    {
        $userIP = Craft::$app->request->userIP;

        if (empty($userIP))
            return 'false';

        // Generate a cache key based on the user IP
        $cacheKey = 'geoData_' . sha1($userIP);
        $cache = Yii::$app->cache;

        // Try to get data from cache to increase speed
        $geoData = $cache->get($cacheKey);

        if ($geoData === false) {
            try {
                $context = stream_context_create([
                    'http' => [
                        'timeout' => 2,
                    ],
                ]);
                $geoData = json_decode(file_get_contents('https://api.country.is/' . $userIP, false, $context));

                // Cache the result for 1 day
                $cache->set($cacheKey, $geoData, 86400);
            } catch (\Exception $e) {}

        }

        return $geoData?->country ?? 'false';
    }
}
