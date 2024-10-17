<?php
namespace conversionia\leadflex\controllers;

use conversionia\leadflex\Leadflex;
use Craft;
use craft\errors\InvalidFieldException;
use craft\web\Controller;
use craft\elements\Entry;
use conversionia\leadflex\assets\map\MapAsset;
use conversionia\leadflex\services\EntryService;
use yii\web\Response;
use yii\base\InvalidConfigException;

use conversionia\leadflex\events\ModifySingleLocationEvent;
use conversionia\leadflex\events\ModifyLocationsEvent;

class MapController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    const string EVENT_MODIFY_SINGLE_LOCATION = 'modifySingleLocation';
    const string EVENT_MODIFY_LOCATIONS = 'modifyLocations';

    /**
     * @throws InvalidConfigException
     */
    public function actionIndex() : void
    {
        // register MapAsset
        $view = $this->getView();
        $view->registerAssetBundle(MapAsset::class);

        // Render the 'leadflex/templates/map/index' template and pass the entries to it
        $this->renderTemplate('leadflex/map');
    }

    /**
     * @throws InvalidFieldException
     */
    public function actionLocations() : Response
    {
        // Get the section from the LeadFlex settings
        $section = Leadflex::$plugin->getSettings()->section;
        $entries = Entry::find()->section($section)->cache()->all(); // Fetch 50 entries
        $entriesService = new EntryService();
        $advertiseColors = [
            true => '#15803d',
            false => '#be123c',
        ];

        $locations = [];
        foreach ($entries as $entry) {
            $rel = $entry->defaultJobDescription->one() ?: $entry;
            // Build Merged Entry Data from the default job description and the overrides from the entry
            $job = $entriesService->mergeEntries($entry, $rel);
            $hiringRadiusMeters = ceil($job->hiringRadius * 1609.34);
            $isBeingAdvertised = filter_var($job->advertiseJob->value, FILTER_VALIDATE_BOOLEAN);

            $location = [
                'id' => $job->id,
                'title' => ($job->adHeadline ?: $job->title),
                'hiringRadius' => $hiringRadiusMeters,
                'location' => [
                    'city' => $job->location->city,
                    'state' => $job->location->state,
                    'zip' => $job->location->zip,
                    'coords' => [
                        'lat' => $job->location->lat,
                        'lng' => $job->location->lng,
                    ],
                ],
                'types' => [
                    'driver' => $job->driverType->value ?: "",
                    'trailer' => $job->trailerType->value ?: "",
                    'job' => $job->jobType->value ?: "",
                ],
                'assignedCampaigns' => $job->assignedCampaign->one() ? $job->assignedCampaign->one()->title : "",
                'advertiseJob' => filter_var($job->advertiseJob->value, FILTER_VALIDATE_BOOLEAN),
                'url' => $job->url,
                'cpEditUrl' => $job->cpEditUrl,
                'circle' => [
                    'strokeColor' => $advertiseColors[$isBeingAdvertised],
                    'strokeOpacity' => .8,
                    'strokeWeight' => 2,
                    'fillColor' => $advertiseColors[$isBeingAdvertised],
                    'fillOpacity' => .35,
                ],
            ];

            // Trigger the event
            $event = new ModifySingleLocationEvent([
                'data' => $location,
                'entry' => $job,
            ]);
            $this->trigger(self::EVENT_MODIFY_SINGLE_LOCATION, $event);

            // Use the modified location JSON
            $location = $event->data;

            $locations[] = $location; // Add missing semicolon
        }

        // Trigger the event
        $event = new ModifyLocationsEvent([
            'data' => $locations,
        ]);
        $this->trigger(self::EVENT_MODIFY_LOCATIONS, $event);

        $locations = $event->data;

        return $this->asJson($locations);
    }
}
