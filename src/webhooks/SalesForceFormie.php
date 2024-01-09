<?php
namespace conversionia\leadflex\webhooks;

use conversionia\leadflex\webhooks\TenstreetFormie;

use Craft;

class SalesForceFormie extends TenstreetFormie
{
// Form submissions are modeled the same as Tenstreet Json.

    public $webhook;

    public static function displayName(): string
    {
        return Craft::t('formie', 'SalesForce ATS');
    }
}
