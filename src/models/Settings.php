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
     * @var boolean Disable custom slug generation for jobs {id}/{slugified-title}
     */
    public bool $disableCustomSlugGeneration = false;

    /**
     * @var boolean Disable the Job Status = Disabled behavior - "No Campaign ---> No Advertise".
     */
    public bool $enableJobCampaignEvaluation = true;

    /**
     * @var string The classes to use for the filter input on the Job Search page
     */
    public string $filterClass = "w-full rounded-[5px] px-1 py-1";

    /**
     * @var array Array of field handles to use for the filter input on the Job Search page
     */
    public array $filterFieldHandles = ['driverType', 'trailerType', 'jobType'];
}
