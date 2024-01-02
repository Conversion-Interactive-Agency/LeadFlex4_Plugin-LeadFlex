<?php

namespace conversionia\leadflex\variables;

use craft\elements\Entry;
use conversionia\leadflex\Leadflex;
use Craft;

class LeadflexVariable
{
    // Public Methods
    // =========================================================================
    /**
     * Get the plugin's name
     *
     * @return null|string
     */

    /**
     * Get the merged values of the current and default job description
     * {% set job = craft.leadflex.getJob($entry) %}
     * @var Entry
     *
     * @return Entry
     */
    public function getJob() : Entry
    {
        return Leadflex::$plugin->entry->getJob();
    }
}
