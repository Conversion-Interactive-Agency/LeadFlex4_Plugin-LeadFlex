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
        echo "Deletes job entries older than 6 Months. This is adjustable via Leadflex plugin settins \n";
        return 1;
    }

    /**
     * Delete job entries older than 60 days
     *
     * @return int
     * @throws \Throwable
     */
    public function actionDeleteDisabledJobsAfterDate()
    {
        $numberOfMonths = Leadflex::$plugin->getSettings()->deleteDisabledJobsAfter;
        $today = new \DateTime();
        $today->modify("-{$numberOfMonths} month");
        $monthThreshold = $today->format('Y-m-d');

        //Create query for jobs that are disabled and lastUpdated from $monthThreshold date
        $entries = Entry::find()
            ->section('jobs')
            ->status('disabled')
            ->dateUpdated("< $monthThreshold")
            ->all();

        // Check to see if action is enabled
        $isJobDeletionEnabled = Leadflex::$plugin->getSettings()->isJobDeletionEnabled;
        if (!$isJobDeletionEnabled) {
            echo "Job deletion feature disabled. Enable option in the Leadflex plugin Settings \n";
            return 1;
        }

        // Check if any job entries are found
        if (empty($entries)) {
            echo "No entries older than {$numberOfMonths} months found.\n";
            return 0;
        }

        // Initialize a counter for deleted entries
        $deletedCount = 0;

        // Iterate over entries and delete them
        foreach ($entries as $entry) {
            if (Craft::$app->getElements()->deleteElement($entry)) {
                echo "Entry successfully deleted: " . $entry->id . "\n";
                $deletedCount++;
            } else {
                echo "Failed to delete entry: " . $entry->id . "\n";
            }
        }

        echo "$deletedCount entries that were published before $monthThreshold were deleted successfully.\n";
        return $deletedCount > 0 ? 0 : 1; // Return 0 if any entries were deleted, otherwise return 1
    }
}
