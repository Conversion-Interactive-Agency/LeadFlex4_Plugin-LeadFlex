<?php
/**
 * Reporter plugin for Craft CMS 3.x
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\variables;

use conversionia\leadflex\Leadflex;

use Craft;
use Twig\Markup;

use craft\elements\Entry;
use craft\errors\InvalidFieldException;
use modules\businesslogic\BusinessLogic;

/**
 * Reporter Variable
 *
 * Craft allows plugins to provide their own template variables, accessible from
 * the {{ craft }} global variable (e.g. {{ craft.reporter }}).
 *
 * https://craftcms.com/docs/plugins/variables
 *
 * @author    Jeff Benusa
 * @package   Reporter
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

    public function renderCookieConsentBanner() : Markup
    {
        $html = Leadflex::$plugin->frontend->buildConsentBanner();
        return new Markup($html, Craft::$app->charset);
    }
}
