<?php

namespace conversionia\leadflex\events;

use yii\base\Event;

class ModifyLocationsEvent extends Event
{
    /**
     * @var array The data that will be returned in the JSON response for all locations / jobs
     */
    public $info;
}
