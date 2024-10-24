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
    public string $section = 'jobs';

    /**
     * @var array[] ATS keys mapping to location field keys
     */
    public array $locationKeys = [
        'city' => 'City',
        'state' => 'State',
        'zip' => 'PostalCode'
    ];

    /**
     * @var boolean Disable custom slug generation for jobs {adheadline}-{jobid}
     */
    public bool $disableCustomSlugGeneration = false;

    /**
     * @var boolean Disable the Job Status = Disabled behavior - "No Campaign ---> No Advertise".
     */
    public bool $includeJobCampaignEvaluation = true;

    /**
     * @var string Defaults the direct referrer to 'lf_direct'.
     */
    public string $defaultDirectReferrer = 'lf_direct';

    /**
     * @var array[] Flex domains to include in the job listing
     */
    public array $leadFlexDomains = [];

    /**
     * @var string leadAssist Chat ID, default empty string
     */
    public $leadAssistID = '';

    public function defineRules(): array
    {
        return [
            [['defaultDirectReferrer'], 'string'],
            [['leadAssistID'], 'string', 'length' => 32, 'skipOnEmpty' => true],
            ['leadFlexDomains', 'safe'], // Updated rule for fully qualified domains
            ['disableCustomSlugGeneration', 'boolean'],
            ['includeJobCampaignEvaluation', 'boolean'],
            ['section', 'string'],
            ['locationKeys', 'safe'],
        ];
    }
}
