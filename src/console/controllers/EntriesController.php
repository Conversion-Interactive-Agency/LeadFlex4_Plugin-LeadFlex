<?php

namespace conversionia\leadflex\console\controllers;

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
     * @var int|null Number of days to filter the entries by.
     */
    public $days;

    /**
     * Define available options for the console command
     * @param string $actionID
     * @return string[]
     */
    public function options($actionID)
    {
        return array_merge(parent::options($actionID), ['days']);
    }

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
        echo "Deletes job entries older than 60 days. Use 'days' to customize: e.g., --days=30 \n";
        return;
    }

    /**
     * Delete entries older than 60 days
     *
     * @return int
     * @throws \Throwable
     */
    public function actionDeleteOldEntries()
    {
        $days = $this->days ?? 60;
        $today = new \DateTime();
        $today->modify("-{$days} days");
        $dateThreshold = $today->format('Y-m-d');

        // Find entries older than the threshold date
        $status = ['live', 'pending', 'expired', 'disabled'];
        $entries = Entry::find()
            ->section('jobs')
            ->postDate("< $dateThreshold")
            ->status($status)
            ->all();

        // Check if any job entries are found
        if (empty($entries)) {
            echo "No entries older than {$days} days found.\n";
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

        echo "$deletedCount entries that were published before $dateThreshold were deleted successfully.\n";
        return $deletedCount > 0 ? 0 : 1; // Return 0 if any entries were deleted, otherwise return 1
    }
}
