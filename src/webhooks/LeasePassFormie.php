<?php
namespace conversionia\leadflex\webhooks;

use Craft;
use craft\base\Volume;
use verbb\formie\elements\Form;
use verbb\formie\elements\Submission;
use verbb\formie\Formie;
use verbb\formie\integrations\webhooks\Webhook;
use conversionia\leadflex\helpers\SubmissionHelper;

// Volume Types
use craft\base\LocalVolumeInterface;

use conversionia\leadflex\Leadflex;
use conversionia\leadflex\events\ReturnJsonEvent;

// todo: Build postFowarinding from Digital Ocean;
// use vaersaagod\dospaces\Volume as DigitalOceanVolume;

class LeasePassFormie extends Webhook
{
    public static function displayName(): string
    {
        return Craft::t('formie', 'LeasePass ATS');
    }

    public function getDescription(): string
    {
        return Craft::t('formie', 'This is a LeasePass webhook integration. Commonly extended by other ATS webhooks.');
    }

    public function getIconUrl(): string
    {
        return Craft::$app->getAssetManager()->getPublishedUrl("@conversionia/assets/cp/dist/img/webhook.svg", true);
    }

    public function getSettingsHtml(): string
    {
        return Craft::$app->getView()->renderTemplate("formie/integrations/webhooks/webhook/_plugin-settings", [
            'integration' => $this,
        ]);
    }

    public function getFormSettingsHtml($form): string
    {
        return Craft::$app->getView()->renderTemplate("formie/integrations/webhooks/webhook/_form-settings", [
            'integration' => $this,
            'form' => $form,
        ]);
    }

    /**
     * @inheritDoc
     */
    public function generatePayloadValues(Submission $submission): array
    {
        /** @var Form $form */
        $form = $submission->getForm();
        $fields = $form->getCustomFields();
        $fileUploadFields = [];

        // Initialize form data
        $data = [];
        $labels = [];

        $uploads = [];

        // Get every submitted field value
        foreach ($fields as $field) {

            // Get data
            $value = $submission->getFieldValue($field->handle);
            $data[$field->handle] = $field->getValueAsString($value, $submission);

            // Get labels
            $label = $field->getAttributeLabel($field->handle);
            $labels[$field->handle] = $label;

            // Fields with default values
            $useDefaults = [
                'atsCompanyId',
                'companyName',
            ];

            //Collect File Upload Field Types
            if ($field->getType() === 'verbb\formie\fields\formfields\FileUpload') {
                $fileUploadFields[] = $field->handle;
                $upload['handle'] = $field->handle;
                $upload['name'] = $field->name;
                $assets = $submission->getFieldValue($field->handle)->all();
                foreach ($assets as $asset)
                {
                    $postAsset['filename'] = $asset->filename;
                    $postAsset['title'] = $asset->title;
                    $postAsset['kind'] = $asset->kind;
                    $postAsset['data'] = $asset->getDataUrl();
                    $uploads[] = $postAsset;
                }
            }

            // Fallback to default value if necessary
            if (in_array($field->handle, $useDefaults) && !$data[$field->handle]) {
                $data[$field->handle] = $field->defaultValue;
            }
        }

        // Get phone number
        $data['cellPhone'] = ($data['cellPhone'] ?? '');

        // Get driver ID
        $driverId = $data['webPageUrl'].'?driverId='.$submission->id;

        // Get license class
        // $licenseClass = ('Yes' === $data['cdlA'] ? 'CDL-A' : '');

        // Data has already been assigned
        $requiredFields = [
            'companyName','atsCompanyId','referrerValue',
            'firstName','lastName',
            'city','state','zipCode',
            'email','cellPhone','optIn','ssnHidden'
        ];

        // Compile JSON data
        $json = [
            'form' => [
                'id' => $form->id,
                'name' => $form->title,
                'returnUrl' => $form->getRedirectUrl(),
                'CompanyName' => trim($data['companyName']),
                'CompanyId' => trim($data['atsCompanyId']),
                'DriverId' => $driverId,
                'AppReferrer' => trim($data['referrerValue']),
                'GivenName' => trim($data['firstName']),
                'FamilyName' => trim($data['lastName']),
                'Municipality' => trim($data['city']),
                'Region' => trim($data['state']),
                'PostalCode' => trim($data['zipCode']),
                'InternetEmailAddress' => trim($data['email']),
                'PrimaryPhone' => SubmissionHelper::cleanPhone($data['cellPhone']),
                'SSN'=> trim($data['ssnHidden']) ?? '',
                // 'CommercialDriversLicense' => $data['cdlA'],
                // 'LicenseClass' => $licenseClass,
                'OptIn' => ($data['optIn'] ?? null || 'No' ?: 'No' ),
            ],
        ];

        if (!empty($uploads)){
            $json['uploads'] = $uploads;
        }

        $usedFields = array_merge($requiredFields, $fileUploadFields);

        // Loop through form data
        foreach ($data as $handle => $value) {
            // If data point was not used, add to JSON data
            if (!in_array($handle, $usedFields)) {
                $label = ($labels[$handle] ?? $handle);
                $json[$label] = $value;
            }
        }

        if (Leadflex::$plugin->hasEventHandlers(Leadflex::EVENT_BEFORE_RETURN_JSON)) {
            $JSON_EVENT_OBJECT = new ReturnJsonEvent([
                'data' => $data,
                'form' => $form,
                'json' => $json,
                'submission' => $submission,
            ]);
            Leadflex::$plugin->trigger(Leadflex::EVENT_BEFORE_RETURN_JSON, $JSON_EVENT_OBJECT);

            $json = $JSON_EVENT_OBJECT->json;
        }

        // Return JSON data
        return $json;
    }
}
