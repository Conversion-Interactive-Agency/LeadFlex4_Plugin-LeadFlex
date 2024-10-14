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
    public function options($id)
    {
        return array_merge(parent::options($id), ['id']);

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
        echo 'Pass an option for an entries controller';
        return;
    }

    /**
     * Disable an entry by ID
     *
     * @param int $id The entry ID
     * @return mixed
     */
    public function actionDisableEntry($id)
    {
        // Fetch the entry by ID
        $entry = Entry::find()->id($id)->one();

        if (!$entry) {
            echo "Entry not found: $id\n";
            return false;
        }

        // Disable the entry by setting its status
        $entry->enabled = false;

        // Save the entry
        if (Craft::$app->getElements()->saveElement($entry)) {
            echo "Entry successfully disabled: $id\n";
        } else {
            echo "Failed to disable entry: $id\n";
        }

        return $id;
    }

    /**
     * Retrieve the title of an entry by ID
     *
     * @param int $id The entry ID
     * @return mixed
     */
    public function actionGetEntryTitle($id)
    {
        // Fetch the entry by ID
        $entry = Entry::find()->id($id)->one();

        if (!$entry) {
            echo "Entry not found: $id\n";
            return false;
        }

        // Print the entry's title
        echo "Entry title: " . $entry->title . "\n";

        return $entry->title;
    }

    public function actionPostDate($id){
        $entry = Entry::find()->id($id)->one();

        if (!$entry) {
            echo "Entry not found: $id\n";
            return false;
        }

        echo "Entry postdate: " . $entry->postDate->format('Y-m-d H:i:s') . "\n";
        return 0;
    }

    public function actionDeleteEntry($id)
    {
        // Fetch the entry by ID
        $entry = Entry::find()->id($id)->one();

        // Check if the entry exists
        if (!$entry) {
            echo "Entry not found: $id\n";
            return 1; // Indicate failure if the entry is not found
        }

        // Delete the entry
        if (Craft::$app->getElements()->deleteElement($entry)) {
            echo "Entry successfully deleted: $id\n";
            return 0; // Return 0 for success
        } else {
            echo "Failed to delete entry: $id\n";
            return 2; // Indicate failure if the delete operation failed
        }
    }

    public function actionEnableEntry($id)
    {
        // Fetch the entry by ID
        $entry = Entry::find()->id($id)->status('disabled')->one();

        // Check if the entry exists
        if (!$entry) {
            echo "Entry not found: $id\n";
            return 1; // Indicate failure if the entry is not found
        }

        // Enable the entry by setting its status
        $entry->enabled = true;

        // Save the entry
        if (Craft::$app->getElements()->saveElement($entry)) {
            echo "Entry successfully enabled: $id\n";
            return 0; // Return 0 for success
        } else {
            echo "Failed to enable entry: $id\n";
            return 2; // Indicate failure if the save operation failed
        }
    }


}
