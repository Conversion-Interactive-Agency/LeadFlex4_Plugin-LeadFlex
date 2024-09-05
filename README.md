# Leadflex plugin for Craft CMS 3.x

This is a generic Craft CMS plugin

![Screenshot](resources/img/plugin-logo.png)

## Requirements for 4.1

### Adding routes for Job Entries to `config/routes.php`
```
'jobs/<entryId:[0-9]+>' => 'leadflex/route/jobs',
'jobs/<entryId:[0-9]+>/<slug:[^\/]+>' => 'leadflex/route/jobs',
```
### Cookie Consent Banner

- The banner is injected at the end of the body without any template updates.
- Can be disabled with a config setting `"disableConsentBanner" => false`.
- To include a customized banner, create a new template at `_leadflex/consentBanner.twig` in the project template directory.
- To only update the text within the banner, update via the plugin settings `cp/settings/plugins/leadflex`.


