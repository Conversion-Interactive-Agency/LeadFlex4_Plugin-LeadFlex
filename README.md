# Leadflex plugin for Craft CMS 3.x

This is a generic Craft CMS plugin

![Screenshot](resources/img/plugin-logo.png)

## Requirements for 4.1

### Adding routes for Job Entries to `config/routes.php`
```
'jobs/<entryId:[0-9]+>' => 'leadflex/route/jobs',
'jobs/<entryId:[0-9]+>/<slug:[^\/]+>' => 'leadflex/route/jobs',
```

### Handlers moved into Controllers/RouteController.php.


