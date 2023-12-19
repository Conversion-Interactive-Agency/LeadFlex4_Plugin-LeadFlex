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
}
