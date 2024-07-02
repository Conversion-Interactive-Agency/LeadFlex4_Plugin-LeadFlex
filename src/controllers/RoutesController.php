<?php
namespace conversionia\leadflex\controllers;

use Craft;
use craft\web\Controller;
use craft\elements\Entry;
use craft\helpers\UrlHelper;

class RoutesController extends Controller
{

    protected $allowAnonymous = true;


    /* Handling Job Entries with changed urls mapped by config/routes.php instead of Craft's URL table
    * @param int $entryId
    * @return void */

    public function actionJobs(int $entryId, string $slug = '') : void
    {
        $this->requireSiteRequest();

        // Use the ID to look up the entry...
        $entry = Entry::find()
            ->id($entryId)
            ->one();

        $redirectUrl = $entry instanceof Entry ? $entry->url : UrlHelper::url("jobs?closed=true");
        $this->redirect($redirectUrl, 301);
    }

    public function actionOldJobs(string $slug = '') : void
    {
        $this->requireSiteRequest();

        // Use the ID to look up the entry...
        $entry = Entry::find()
            ->oldSlug($slug)
            ->one();

        if (empty($entry)) {
            preg_match("/([0-9]+)$/", $slug, $matches);

            $entry = Entry::find()
                ->id($matches[1])
                ->one();
        }

        $redirectUrl = $entry instanceof Entry ? $entry->url : UrlHelper::url("jobs?closed=true");
        $this->redirect($redirectUrl, 301);
    }
}
