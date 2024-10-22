<?php
/**
 *
 * CIA tool to build reports
 *
 * @link      conversionia.com
 * @copyright Copyright (c) 2023 Jeff Benusa
 */

namespace conversionia\leadflex\services;

use conversionia\leadflex\assets\site\SiteAsset;

use yii\base\Event;

use Craft;
use craft\base\Component;
use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use craft\web\View;

class MapService extends Component
{
    /**
     * Find jobs that overlap with each other
     * 
     * @param array $jobs
     * @return array
     */
    public function findJobOverlaps($jobs)
    {
        $hasOverlap = array_fill(0, count($jobs), false);

        // Iterate over each job
        for ($i = 0; $i < count($jobs); $i++) {
            for ($j = $i + 1; $j < count($jobs); $j++) {
                $job1 = $jobs[$i];
                $job2 = $jobs[$j];

                // Calculate the distance between the two job locations
                $distance = $this->calculateDistance(
                    $job1['location']['lat'], $job1['location']['lng'],
                    $job2['location']['lat'], $job2['location']['lng']
                );

                // Check if the jobs overlap
                if ($distance <= ($job1['hiringRadius'] + $job2['hiringRadius'])) {
                    $hasOverlap[$i] = true;
                    $hasOverlap[$j] = true;
                }
            }
        }

        // Count overlapping and non-overlapping jobs
        $overlappingCount = 0;
        $nonOverlappingCount = 0;

        foreach ($hasOverlap as $flag) {
            if ($flag) {
                $overlappingCount++;
            } else {
                $nonOverlappingCount++;
            }
        }

        return [
            'overlappingCount' => $overlappingCount,
            'nonOverlappingCount' => $nonOverlappingCount
        ];
    }

    private function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        // Convert latitude and longitude from degrees to radians
        $lat1 = deg2rad($lat1);
        $lng1 = deg2rad($lng1);
        $lat2 = deg2rad($lat2);
        $lng2 = deg2rad($lng2);

        // Haversine formula to calculate the distance
        $dlat = $lat2 - $lat1;
        $dlng = $lng2 - $lng1;

        $a = sin($dlat / 2) * sin($dlat / 2) +
             cos($lat1) * cos($lat2) *
             sin($dlng / 2) * sin($dlng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        // Radius of Earth in miles
        $radiusOfEarth = 3958.8;

        // Calculate the distance in miles
        return $radiusOfEarth * $c;
    }
}
