<?php

namespace conversionia\leadflex\models;

use craft\base\Model;

/**
 *
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================
    /**
     * @var string The section to use for the job listings
     */
    public $section = 'jobs';

    /**
     * @var array[] ATS keys mapping to location field keys
     */
    public $locationKeys = [
        'city' => 'City',
        'state' => 'State',
        'zip' => 'PostalCode'
    ];

    /**
     * @var boolean Disable custom slug generation for jobs {adheadline}-{jobid}
     */
    public $disableCustomSlugGeneration = false;

    /**
     * @var boolean Disable the Job Status = Disabled behavior - "No Campaign ---> No Advertise".
     */
    public $includeJobCampaignEvaluation = true;

    /**
     * @var string The default Cookie Consent banner html to use when creating a new job
     */
    public string $consentBannerText = "";
    
    /**
     * @var string default path to LeadFlex cookie consent banner for override
     */
    public string $consentBannerPath = "_leadflex/consentBanner";

    /**
     * @var bool disable the cookie consent banner from being injected into the end of the body via $LeadFlex->frontend->registerEvents();
     */
    public bool $disableConsentBanner = false;
}
