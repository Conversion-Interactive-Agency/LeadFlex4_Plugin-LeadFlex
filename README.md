# Leadflex plugin for Craft CMS 3.x

This is a generic Craft CMS plugin

![Screenshot](resources/img/plugin-logo.png)

## Requirements for 4.1

Adding routes for Job Entries to `config/routes.php`
```
'jobs/<entryId:[0-9]+>' => ['template' => 'jobs/entry'],
'jobs/<entryId:[0-9]+>/<slug:[^\/]+>' => ['template' => 'jobs/entry'],
```

Adding handlers into `templates/jobs/entry`
```
{% if entryId is defined %}
    {% set entry = craft.entries.id(entryId).one() %}
{% endif %}

{% if entry is null %}
    {% redirect "jobs?closed=true" %}
{% endif %}
```


