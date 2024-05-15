<?php

namespace conversionia\leadflex\migrations;

use Craft;
use craft\db\Migration;
use craft\elements\Entry;

/**
 * m240513_194109_slug_to_old_slug_field migration.
 */
class m240514_204109_updating_jobs_url_pattern extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // Get all the entries in the jobs section.
        $jobs = Entry::find()->section('jobs')->anyStatus()->all();
        // Copy the current's slug into the Old Slug field.
        foreach ($jobs as $job) {
            $job->setFieldValues(['oldSlug' => $job->slug]);
            echo $job->url . " updated.  \n";
            Craft::$app->getElements()->saveElement($job);
        }

        // Get the section by handle
        $section = Craft::$app->sections->getSectionByHandle('jobs');

        // Check if the section exists
        if ($section) {
            // Get the primary site ID
            $primarySiteId = Craft::$app->sites->getPrimarySite()->id;

            // Get the site settings for the primary site
            $siteSettings = $section->getSiteSettings();
            foreach ($siteSettings as $settings) {
                if ($settings->siteId == $primarySiteId) {
                    // Update the uriFormat for the primary site only
                    $settings->uriFormat = 'jobs/{id}/{slug}';
                }
            }

            // Save the section with the updated settings
            if (!Craft::$app->sections->saveSection($section)) {
                Craft::error('Failed to save the section with updated site settings.', __METHOD__);
                // Optionally, handle and log errors
                $errors = $section->getErrors();
                foreach ($errors as $attribute => $errorMessages) {
                    Craft::error("Errors on $attribute: " . implode(", ", $errorMessages), __METHOD__);
                }
            }
        } else {
            Craft::error('No section found with the handle "jobs".', __METHOD__);
        }

    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m240513_194109_slug_to_old_slug_field cannot be reverted.\n";
        return false;
    }
}
