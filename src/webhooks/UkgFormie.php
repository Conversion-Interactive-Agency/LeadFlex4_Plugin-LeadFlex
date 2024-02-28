<?php
namespace conversionia\leadflex\webhooks;

use Craft;
use conversionia\leadflex\webhooks\TenstreetFormie;

// Volume Types
use craft\base\LocalVolumeInterface;

// todo: Build postFowarinding from Digital Ocean;
// use vaersaagod\dospaces\Volume as DigitalOceanVolume;

class UkgFormie extends TenstreetFormie
{
    public static function displayName(): string
    {
        return Craft::t('formie', 'UKG ATS');
    }

    public function getDescription(): string
    {
        return Craft::t('formie', 'This is an UkgFormie webhook. Extended from Tenstreet Webhook.');
    }
}
