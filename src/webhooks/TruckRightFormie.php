<?php
namespace conversionia\leadflex\webhooks;

use conversionia\leadflex\webhooks\TenstreetFormie;

use Craft;

class TruckRightFormie extends TenstreetFormie
{
    // Form submissions are modeled the same as Tenstreet Json.
    public static function displayName(): string
    {
        return Craft::t('formie', 'TruckRight ATS');
    }

    public function getDescription(): string
    {
        return Craft::t('formie', 'This is an TruckRight webhook. Extended from Tenstreet Webhook.');
    }
}
