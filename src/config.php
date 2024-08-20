<?php
/**
 * LeadFlex plugin for Craft CMS 4.x
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

/**
 * LeadFlex config.php
 *
 * This file exists only as a template for the LeadFlex settings.
 * It does nothing on its own.
 *
 * Don't edit this file, instead copy it to 'craft/config' as 'leadflex.php'
 * and make your changes there to override default settings.
 *
 * Once copied to 'craft/config', this file will be multi-environment aware as
 * well, so you can have different settings groups for each environment, just as
 * you do for 'general.php'
 */

return [
    // The section to use for the job listings
    "section" => 'jobs',

    // ATS keys mapping to location field keys
    "locationKeys" => [
        'city' => 'City',
        'state' => 'State',
        'zip' => 'PostalCode'
    ],

    // Disable custom slug generation for jobs {id}/{slugified-title}
    "disableCustomSlugGeneration" => false,

    // Disable the Job Status = Disabled behavior - "No Campaign ---> No Advertise".
    "enableJobCampaignEvaluation" => true,

    // The classes to use for the filter input on the Job Search page
    "filterClass" => "w-full rounded-[5px] px-1 py-1",

    // Array of field handles to use for the filter input on the Job Search page
    "filterFieldHandles" => ['driverType', 'trailerType', 'jobType']
];
