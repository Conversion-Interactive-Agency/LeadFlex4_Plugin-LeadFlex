<?php

namespace conversionia\leadflex\events;

use yii\base\Event;
use craft\elements\Entry;

/**
 * ModifySingleLocationJsonEvent class.
 * Used to modify the JSON response for a single job
 */
class ModifySingleLocationEvent extends Event
{
    /**
     * @var array The data that will be returned in the JSON response for a single job
     */
    public $info;

    /**
     * @var Entry The entry that is being processed - merged from the default job description and the entry overrides
     */
    public Entry $entry;
}
