<?php
namespace conversionia\leadflex\controllers;

use Craft;
use conversionia\leadflex\Leadflex;
use craft\web\Controller;
use craft\helpers\UrlHelper;

class JsonController extends Controller
{
    protected array|bool|int $allowAnonymous = true;

    public function actionIndex(): void
    {
        $domains = Leadflex::$plugin->getSettings()->leadFlexDomains;
        $jsonUrls = [];

        // Validate domains + assist and url constructions
        foreach ($domains as $domain) {

            // URLs are stripped to domain name in the settings event.
            $domainUrl = 'https://' . $domain['domain'];
            $uri = !empty($domain['uri']) ? $domain['uri'] : 'jobs.json'; // Append 'jobs.json' if uri is empty
            $jsonUrls[] = UrlHelper::siteUrl($domainUrl . '/' . $uri);
        }

        $mergedData = ['jobs' => $this->fetchJobsFromUrls($jsonUrls)];

        Craft::$app->getResponse()->format = \yii\web\Response::FORMAT_JSON;
        Craft::$app->getResponse()->data = $mergedData;
    }

    private function fetchJobsFromUrls(array $jsonUrls): array
    {
        $allJobs = [];
        $timeout = 10; // Set a timeout in seconds
        $context = stream_context_create(['https' => ['timeout' => $timeout]]);

        foreach ($jsonUrls as $jsonUrl) {
            $response = @file_get_contents($jsonUrl, false, $context);
            if ($response !== false) {
                $data = json_decode($response, true);
                if (is_array($data) && isset($data['jobs'])) {
                    $allJobs = array_merge($allJobs, $data['jobs']);
                } else {
                    Craft::warning('Invalid JSON response from: ' . $jsonUrl, __METHOD__);
                }
            } else {
                Craft::error('Failed to fetch JSON from: ' . $jsonUrl, __METHOD__);
            }
        }

        return $allJobs;
    }
}
