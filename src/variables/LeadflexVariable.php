<?php
namespace conversionia\leadflex\variables;

use conversionia\leadflex\Leadflex;

use Craft;
use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use modules\businesslogic\BusinessLogic;

/**
 * LeadFlex Twig Variables
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.leadflex }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Jeff Benusa, JD Griffin
 * @package   Leadflex
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

    /**
     * getGeo
     *
     * Get the user IP from craft and check it against the country.is API.
     * Used in twig templates: `{% set userGeo = craft.leadflex.getGeo() %}`
     *
     * @since 4.4.0
     *
     * @return string Two character country code or 'false' on failure
     */
    public function getGeo() : string
    {
        return Leadflex::$plugin->frontend->getGeo();
    }
}
