<?php
namespace conversionia\leadflex\webhooks;

use Craft;
use verbb\formie\elements\Form;
use verbb\formie\elements\Submission;
use verbb\formie\Formie;
use verbb\formie\integrations\webhooks\Webhook;
use conversionia\leadflex\helpers\SubmissionHelper;

class EbeFormie extends Webhook
{
    public static function displayName(): string
    {
        return Craft::t('formie', 'EBE ATS');
    }

    public function getDescription(): string
    {
        return Craft::t('formie', 'This is a EBE ATS webhook integration.');
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

        // Initialize form data
        $data = [];
        $labels = [];

        // Get every submitted field value
        foreach ($form->getCustomFields() as $field) {

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
                'source',
                'sourceId',
            ];

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

        // Compile JSON data
        $json = [
            'form' => [
                'id' => $form->id,
                'name' => $form->title,
                'returnUrl' => $form->getRedirectUrl(),
                'CompanyName' => trim($data['companyName']),
                'CompanyId' => trim($data['atsCompanyId']),
                'source' => trim($data['source']),
                'sourceId' => trim($data['sourceId']),
                'DriverId' => $driverId,
                'AppReferrer' => trim($data['referrerValue']),
                'GivenName' => trim($data['firstName']),
                'FamilyName' => trim($data['lastName']),
                'Municipality' => trim($data['city']),
                'Region' => trim($data['state']),
                'PostalCode' => trim($data['zipCode']),
                'InternetEmailAddress' => trim($data['email']),
                'PrimaryPhone' => SubmissionHelper::cleanPhone($data['cellPhone']),
                // 'CommercialDriversLicense' => $data['cdlA'],
                // 'LicenseClass' => $licenseClass,
                'OptIn' => ($data['optIn'] ?? null || 'No' ?: 'No' ),
            ],
        ];

        // Data has already been assigned
        $used = [
            'companyName','atsCompanyId','source','sourceId','referrerValue',
            'firstName','lastName',
            'city','state','zipCode',
            'email','cellPhone','optIn',
        ];

        // Loop through form data
        foreach ($data as $handle => $value) {

            // If data point was not used, add to JSON data
            if (!in_array($handle, $used)) {
                $label = ($labels[$handle] ?? $handle);
                $json[$label] = $value;
            }

        }

        // Return JSON data
        return $json;
    }
}
