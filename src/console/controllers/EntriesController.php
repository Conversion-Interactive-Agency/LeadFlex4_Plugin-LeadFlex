<?php

namespace conversionia\leadflex\console\controllers;

use conversionia\leadflex\Leadflex;
use Craft;

use craft\elements\Entry;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * Entry Command
 *
 * Console commands related to entries.
 */
class EntriesController extends Controller
{

    /**
     * Handle leadflex/entries console commands
     *
     * The first line of this method docblock is displayed as the description
     * of the Console Command in ./craft help
     *
     * @return mixed
     */
    public function actionIndex()
    {
        echo "Deletes job entries older than 6 Months. This is adjustable via Leadflex plugin settings \n";
        return 1;
    }

    /**
     * Delete disabled jobs that hanven't been updated in 6 months from today's date
     *
     * @return int
     * @throws \Throwable
     */
    public function actionDeleteStaleJobs()
    {
        $settings = Leadflex::$plugin->getSettings();

        // Check to see if action is enabled
        $isJobDeletionEnabled = $settings->isJobDeletionEnabled;
        if (!$isJobDeletionEnabled) {
            Craft::warning("Job deletion feature disabled. Enable option in the Leadflex plugin Settings");
            echo "Job deletion feature disabled. Enable option in the Leadflex plugin Settings \n";
            return 1;
        }

        $numberOfMonths = $settings->jobDeletionMonths;

        $today = new \DateTime();
        $monthThreshold= $today->modify("-${numberOfMonths}")->format('Y-m-d');

        //Create query for jobs that are disabled and lastUpdated from $monthThreshold date
        $entries = Entry::find()
            ->section($settings->section)
            ->status('disabled')
            ->dateUpdated("< $monthThreshold")
            ->all();

        // Check if any job entries are found
        if (empty($entries)) {
            Craft::warning("No job entries found for $monthThreshold");
            echo "No entries older than {$numberOfMonths} found.\n";
            return 0;
        }

        // Initialize a counter for deleted entries
        $deletedCount = 0;

        // Iterate over entries and delete them
        foreach ($entries as $entry) {
            if (Craft::$app->getElements()->deleteElement($entry)) {
                Craft::info("Entry successfully deleted: " . $entry->id . "\n");
                echo "Entry successfully deleted: " . $entry->id . "\n";
                $deletedCount++;
            } else {
                Craft::error("Failed to delete entry: " . $entry->id . "\n");
                echo "Failed to delete entry: " . $entry->id . "\n";
            }
        }

        Craft::info("$deletedCount entries that were published before $monthThreshold were deleted successfully.\n");
        echo "$deletedCount entries that were published before $monthThreshold were deleted successfully.\n";
        return $deletedCount > 0 ? 0 : 1; // Return 0 if any entries were deleted, otherwise return 1
    }
}
