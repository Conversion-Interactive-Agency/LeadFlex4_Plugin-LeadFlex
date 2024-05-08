<?php
namespace conversionia\leadflex\controllers;

use Craft;
use craft\web\Controller;
use craft\elements\Entry;
use craft\helpers\UrlHelper;

class RoutesController extends Controller
{

    protected array|bool|int $allowAnonymous = true;


    /* Handlding Job Entries with changed urls mapped by config/routes.php instead of Craft's URL table
    * @param int $entryId
    * @return void */

    public function actionJobs(int $entryId)
    {
        $this->requireSiteRequest();

        // Use the ID to look up the entry...
        $entry = Entry::find()
            ->id($entryId)
            ->one();

        $redirectUrl = $entry instanceof Entry ? $entry->url : UrlHelper::url("jobs?closed=true");
        $this->redirect($redirectUrl);
    }
}
