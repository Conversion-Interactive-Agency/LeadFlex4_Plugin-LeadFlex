<?php
/**
 * LeadFlex plugin for Craft CMS 4.x
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\variables;

use conversionia\leadflex\Leadflex;

use Craft;
use craft\elements\db\ElementQuery;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;

/**
 * LeadFlex Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.leadflex }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Jeff Benusa
 * @package   LeadFlex
 * @since     1.0.0
 */
class LeadflexVariable
{
    // Public Methods
    // =========================================================================
    public function buildJobEntry($primary, $rel) : Entry
    {
        return Leadflex::$plugin->entry->mergeEntries($primary, $rel);
    }

    public function buildExternalApplicationUrl($job) : String
    {
        return Leadflex::$plugin->entry->buildExternalApplicationUrl($job);
    }

    public function getConvirza($job) : array
    {
        return Leadflex::$plugin->frontend->getConvirza($job);
    }

    public function getJobs($filters, $location) : ElementQuery
    {
        return Leadflex::$plugin->frontend->getJobs($filters, $location);
    }

    public function getFiltersFields() : array
    {
        return Leadflex::$plugin->frontend->getFiltersFields();
    }

    public function buildFilter($field, $value, $sprigVariable) : string
    {
        return Leadflex::$plugin->frontend->buildFilter($field, $value, $sprigVariable);
    }
}
