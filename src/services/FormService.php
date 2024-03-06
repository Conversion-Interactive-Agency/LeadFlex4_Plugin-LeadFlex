<?php
/**
 * Reporter plugin for Craft CMS 3.x
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\services;

use conversionia\leadflex\Leadflex;

use Craft;

use craft\events\ModelEvent;
use verbb\formie\elements\Form;
use yii\base\Component;
use yii\base\Event;
use yii\caching\TagDependency;

class FormService extends Component
{
    public function registerEvents()
    {
        Event::on(Form::class, Form::EVENT_AFTER_SAVE, function() {
            $cache = Craft::$app->getCache();
            TagDependency::invalidate($cache, 'graphql');
        });
    }
}
