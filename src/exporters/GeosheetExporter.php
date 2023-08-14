<?php

namespace conversionia\leadflex\exporters;

use craft\base\Element;
use craft\elements\Entry;
use craft\base\ElementExporter;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\ArrayHelper;

class GeosheetExporter extends ElementExporter
{
    public static function displayName(): string
    {
        return 'GeoSheet';
    }

    private function getLabel($property, $fallbackProperty)
    {
        return !empty($property->value) ? $property->label : $fallbackProperty->label;
    }

    public function export(ElementQueryInterface $query): array
    {
        $results = [];

        // Eager-load the entries related via the relatedEntries field
        /** @var ElementQuery $query */
        $query->with(['defaultJobDescription', 'assignedCampaign']);

        foreach ($query->each() as $element) {

            // Eager loaded defaultJobDescription
            $fallback = $element->defaultJobDescription[0] ?? '';

            $campaign = !empty($element->assignedCampaign[0])
                ? $element->assignedCampaign[0]
                : '';


            /** @var Element $element */
            $results[] = [
                'ID' => $element->id,
                'Title' => $element->title ?? $fallback->title,
                'Status' => ucfirst($element->status),
                'URL' => $element->getUrl(),
                'Trailer Type' => $this->getLabel($element->trailerType, $fallback->trailerType),
                'Driver Type' => $this->getLabel($element->driverType, $fallback->driverType),
                'Route Type' => $this->getLabel($element->jobType, $fallback->jobType),
                'Job Type' => $this->getLabel($element->jobType, $fallback->jobType),
                'Assigned Campaign' => $campaign,
                'Hiring Radius' => $element->hiringRadius ?? $fallback->hiringRadius,
                'Google Jobs Title' => $element->googleJobsTitle ?? $fallback->googleJobsTitle,
                'Ad Headline' => $element->adHeadline ?? $fallback->adHeadline,
                'Location' => $element->location->formatted,
                'City' => $element->location->city,
                'State' => $element->location->state,
                'Zip' => $element->location->zip,
                'Custom UTM Tag' => $element->customUtmTag ?? '',
                'External Job ID' => $element->externalJobId ?? '',
                'Pay' => $element->pay ?? $fallback->pay,
            ];
        }

        return $results;
    }
}