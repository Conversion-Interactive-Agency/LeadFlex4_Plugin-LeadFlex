<?php
/**
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\services;

use conversionia\leadflex\Leadflex;
use Craft;
use craft\base\Component;
use craft\elements\db\ElementQuery;
use craft\elements\Entry;
use craft\fields\Dropdown;
use craft\fields\PlainText;

use yii\base\Event;

use conversionia\leadflex\assets\site\SiteAsset;

use conversionia\leadflex\twigextensions\BusinessLogicTwigExtensions;
use conversionia\leadflex\twigextensions\FrontendTwigExtensions;
use conversionia\leadflex\twigextensions\TwigFiltersExtensions;
use conversionia\leadflex\helpers\SubmissionHelper;
use conversionia\leadflex\helpers\EntryHelper;
use conversionia\leadflex\helpers\FrontendHelper;
use doublesecretagency\googlemaps\helpers\GoogleMaps;

use conversionia\leadflex\variables\LeadflexVariable;
use craft\web\twig\variables\CraftVariable;

class FrontendService extends Component
{

    private $convirza = [];

    public function registerFrontend()
    {
        $this->registerVariables();
        $this->registerPluginVariable();
        $this->registertAssets();
    }

    public function registerVariables()
    {
        $extensions = [
            BusinessLogicTwigExtensions::class,
            // FrontendTwigExtensions::class,
            TwigFiltersExtensions::class
        ];

        foreach ($extensions as $extension) {
            Craft::$app->view->registerTwigExtension(new $extension);
        }
    }

    public function registerPluginVariable()
    {
        // Register our plugin
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT,
            function (Event $event) {
                /** @var CraftVariable $variable */
                $variable = $event->sender;
                $variable->set('leadflex', LeadflexVariable::class);
            }
        );
    }

    public function registertAssets()
    {
        Craft::$app->view->registerAssetBundle(SiteAsset::class);
    }
    
    public function getConvirza($job): array
    {
        if (!empty($this->convirza)) return $this->convirza;
        $project = Entry::find()->section('project')->one();
        if (EntryHelper::doFieldsExists($job, ['customPhoneTag', 'phone'])) {
            $this->convirza['tag'] = $job->getFieldValue('customPhoneTag') ?? $project->getFieldValue("customPhoneTag");
            $this->convirza['number'] = $job->getFieldValue('phone') ?? $project->getFieldValue("phone");
            $this->convirza['tel'] = "tel:".SubmissionHelper::cleanPhone($this->convirza['number']);
        } else {
            $this->convirza['tag'] = $project->getFieldValue("customPhoneTag");
            $this->convirza['number'] = $project->getFieldValue("phone");
            $this->convirza['tel'] = "tel:".SubmissionHelper::cleanPhone($this->convirza['number']);
        }
        return $this->convirza;
    }

    public function getJobs($filters, $location) : ElementQuery
    {
        $filters = array_filter($filters);

        $query = Entry::find()->section('jobs')
            ->orderBy('makeSticky desc, postDate desc')
            ->with(['defaultJobDescription']);
        // filters will be passed and array of key (field handle) value (field value)
        foreach ($filters as $key => $value) {
            $query->$key($value);
        }

        $proximitySearchRules = $location['city'] && $location['state'] || $location['zip'];
        $isStateOnlyJobSearch = $location['state'] && !$location['city'] && !$location['zip'];

        // Get the IDS that match the search criteria.
        if ($proximitySearchRules) {
            $proximityQuery = clone($query);
            $statewideQuery = clone($query);

            // Use the zipcode above all else
            if (!empty($location['zip']))
            {
                $address = GoogleMaps::lookup($location['zip'])->one();
                $location['state'] = $address->state;
            }
            $stateFullName = FrontendHelper::getStateLongName($location['state']);

            // Get the ids for statewide jobs in the matching state.
            $statewideQuery->statewideJob(1);
            $locationParams = [
                'subfields' => [
                    'state' => $location['state']
                ]
            ];
            $statewideQuery->location($locationParams);
            $statewideIds = $statewideQuery->ids();

            // Lookup the location and get the jobs within the hiring range.
            $term = (!empty($location['city']) ? $location['city'] . ', ' : '')
                . $stateFullName
                . (!empty($location['zip']) ? ', ' . $location['zip'] : '')
                . ' United States';

            $proximityQuery->location([
                'target' => $term,
                'range' => $location['range'],
                'units' => 'miles'
            ]);

            $jobsWithinRange = $proximityQuery->ids();

            $mergedArray = array_merge($statewideIds, $jobsWithinRange);
            $ids = array_unique($mergedArray);

            $query->id($ids);

        } elseif($isStateOnlyJobSearch) {
            // if it's only a location.state - get all the jobs in the state - regardless of city or hiring range.
            $options = [
                'subfields' => [
                    'state' => $location['state']
                ]
            ];
            $query->location($options);
        }

        return $query;
    }

    public function getFilters() : array
    {
        // todo: get the filter handles from a GraphQL based injection from CNext into Sprig component variables
        return Leadflex::$plugin->getSettings()->filterFieldHandles;
    }

    public function buildFilter($field, $value) : string
    {
        // Build unique filters for plain text fields and dropdown fields (with options)
        $filtersClass =  Leadflex::$plugin->getSettings()->filterClass;

        $html = "<label for='{$field->handle}' class='flex items-center'><span>{$field->name}</span></label>";
        // get the object class of $field
        $fieldClass = get_class($field);
        // switch statement for the field class
        switch ($fieldClass) {
            case Dropdown::class:
                $html .= "<select id='{$field->handle}' name=filters[{$field->handle}] class='".$filtersClass."' aria-label='-Select-'>";
                foreach ($field->options as $option) {
                    $isSelected = $value == $option['value'] ? ' selected' : '';
                    $html .= "<option value='{$option['value']}' {$isSelected}>{$option['label']}</option>";
                }
                $html .= "</select>";
                break;
            case PlainText::class:
                $html .= "<input type='text' id='{$field->handle}' name='{$field->handle}' class='".$filtersClass."' aria-label='-Select-'>";
                break;
            default:
                $html = '';
        }

        return $html;
    }
}
