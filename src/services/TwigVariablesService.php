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
use yii\base\Event;

use conversionia\leadflex\twigextensions\BusinessLogicTwigExtensions;
use conversionia\leadflex\twigextensions\FrontendTwigExtensions;
use conversionia\leadflex\twigextensions\TwigFiltersExtensions;
use conversionia\leadflex\helpers\SubmissionHelper;
use conversionia\leadflex\helpers\EntryHelper;

use conversionia\leadflex\variables\LeadflexVariable;
use craft\web\twig\variables\CraftVariable;

class TwigVariablesService extends Component
{

    private $convirza = [];

    public function registerFrontend()
    {
        $this->registerVariables();
        $this->registerPluginVariable();
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
    
    public function getConvirza($job): array
    {
        if (!empty($this->convirza)) return $this->convirza;
        $companyInfo = Craft::$app->globals->getSetByHandle("companyInfo");

        if (EntryHelper::doFieldsExists($job, ['customPhoneTag', 'phone'])) {
            $this->convirza['tag'] = $job->getFieldValue('customPhoneTag') ?? $companyInfo->getFieldValue("customPhoneTag");
            $this->convirza['number'] = $job->getFieldValue('phone') ?? $companyInfo->getFieldValue("phone");
            $this->convirza['tel'] = "tel:".SubmissionHelper::cleanPhone($this->convirza['number']);
        } else {
            $this->convirza['tag'] = $companyInfo->getFieldValue("customPhoneTag");
            $this->convirza['number'] = $companyInfo->getFieldValue("phone");
            $this->convirza['tel'] = "tel:".SubmissionHelper::cleanPhone($this->convirza['number']);
        }
        return $this->convirza;
    }
}
