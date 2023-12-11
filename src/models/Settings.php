<?php

namespace conversionia\leadflex\models;

use craft\base\Model;

class Settings extends Model
{
    // Public Properties
    // =========================================================================
    public $section = 'jobs';

    public $locationKeys = [
        'city' => 'City',
        'state' => 'State',
        'zip' => 'PostalCode'
    ];
}
