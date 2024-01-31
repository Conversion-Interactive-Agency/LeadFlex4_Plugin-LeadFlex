<?php

namespace conversionia\leadflex\twigextensions;

use Craft;
use craft\elements\Entry;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use yii\web\Cookie;

class FrontendTwigExtensions extends AbstractExtension implements GlobalsInterface
{
    public function getGlobals(): array
    {
        // Map the url to an entry
        $job = Entry::find()->section('jobs')->one();

        $view = Craft::$app->getView();
        $referrer = $view->getTwig()->getGlobals()['referrer'];

        // Get the value from field defaultAtsLinkglobal in the global group "companyInfo"
        $companyInfo = Craft::$app->globals->getSetByHandle("companyInfo");
        $baseUrl = $job["atsLink"] ?: $companyInfo->getFieldValue("atsLink");
        $intelliAppUrlHasQuery = strpos($baseUrl, "?") !== false;
        $atsReferrerFormat = $companyInfo->getFieldValue("atsApplyButtonFormatting");
        $referrerKey = $intelliAppUrlHasQuery ? str_replace("?", "&", $atsReferrerFormat) : str_replace("&", "?", $atsReferrerFormat);
        return [
            'ApplicationUrl'  => $baseUrl . $referrerKey . $referrer
        ];
    }
}

