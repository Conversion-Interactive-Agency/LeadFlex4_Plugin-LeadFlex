<?php
namespace conversionia\leadflex\controllers;

use conversionia\leadflex\Leadflex;
use Craft;
use craft\errors\InvalidFieldException;
use craft\web\Controller;
use craft\elements\Entry;
use conversionia\leadflex\assets\map\MapAsset;
use conversionia\leadflex\services\EntryService;
use conversionia\leadflex\services\MapService;

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

        // check if a custom template exists as '_leadflex/map.twig', if not use the default 'leadflex/map' template
        $template = '_leadflex/map';
        if (!Craft::$app->getView()->doesTemplateExist($template)) {
            $template = 'leadflex/map';
        }

        // Render the template and pass the entries to it
        $this->renderTemplate($template);
    }

    /**
     * @throws InvalidFieldException
     */
    public function actionLocations() : Response
    {
        // Get the section from the LeadFlex settings
        $section = Leadflex::$plugin->getSettings()->section;

        // Add a cache for the response
        Craft::$app->response->headers->add('Cache-Control', 'public, max-age=3600');

        // Cache key based on section
        $cacheKey = 'leadflex_locations_' . md5($section);
        $cache = Craft::$app->getCache();

        // Try to get the cached response
        $cachedResponse = $cache->get($cacheKey);
        if ($cachedResponse !== false) {
            return $this->asJson($cachedResponse);
        }

        $entries = Entry::find()->section($section)->all();
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
                    'fillOpacity' => .6,
                ],
                'additionalInfo' => [],
            ];

            // Trigger the event
            $event = new ModifySingleLocationEvent([
                'info' => $location,
                'entry' => $job,
            ]);
            $this->trigger(self::EVENT_MODIFY_SINGLE_LOCATION, $event);

            // Use the modified location JSON
            $location = $event->info;

            $locations[] = $location; // Add missing semicolon
        }

        // Trigger the event
        $event = new ModifyLocationsEvent([
            'info' => $locations,
        ]);
        $this->trigger(self::EVENT_MODIFY_LOCATIONS, $event);

        $locations = ['data' => $event->info];

        // Cache the response for future requests
        $cache->set($cacheKey, $locations, 3600); // Cache for 1 hour

        return $this->asJson($locations);
    }


    // add a new action to breakdown the jobs by state, as well as the number of jobs in each state and number of jobs in each state that do not have an overlapping hiring area
    public function actionByState(): Response
    {
        // Get the section from the LeadFlex settings
        $section = Leadflex::$plugin->getSettings()->section;

        // Instantiate the MapService
        $mapService = new MapService();
        $entriesService = new EntryService();

        // Fetch all entries in the section with statewideJob as false
        $entries = Entry::find()
            ->section($section)
            ->statewideJob(false)
            ->all();

        // Initialize state breakdown array
        $stateBreakdown = [];

        // Group jobs by state
        $jobsByState = [];
        foreach ($entries as $entry) {
            $rel = $entry->defaultJobDescription->one() ?: $entry;
            $job = $entriesService->mergeEntries($entry, $rel);

            $state = $job->location->state;
            if (!isset($jobsByState[$state])) {
                $jobsByState[$state] = [];
            }
            $jobsByState[$state][] = $job;
        }

        ksort($jobsByState);

        // Process each state
        foreach ($jobsByState as $state => $jobs) {
            // Get the count of overlapping and non-overlapping jobs
            $overlapResults = $mapService->findJobOverlaps($jobs, $state);

            // Add the state breakdown to the stateBreakdown array
            $stateBreakdown[$state] = [
                'totalJobs' => count($jobs),
                'areOverlapping' => $overlapResults['overlappingCount'],
                'haveNoOverlap' => $overlapResults['nonOverlappingCount'],
            ];
        }

        return $this->asJson($stateBreakdown);
    }
}
