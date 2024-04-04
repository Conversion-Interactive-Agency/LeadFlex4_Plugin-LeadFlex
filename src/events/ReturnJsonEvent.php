<?php
namespace conversionia\leadflex\events;

use yii\base\Event;

class ReturnJsonEvent extends Event
{
    // Properties
    // =========================================================================
    public $data;
    public $form;
    public $json;
    public $submission;
    public $webhook;
}
