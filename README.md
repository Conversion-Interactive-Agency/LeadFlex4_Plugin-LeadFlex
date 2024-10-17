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

## Map Overlapping Tool

### New Route: `/leadflex/map/`

- **Purpose**: This route is designed to display a map interface to users.
- **Implementation**: The route is set by the plugin's Routes service, which maps the URL to the `MapController`.
- **Template**: The map is rendered using the `map/index.twig` template located at `plugins/leadflex/src/templates/map/index.twig`.

### Map Controller

- **File**: `plugins/leadflex/src/controllers/MapController.php`
- **Functionality**: 
  - Registers the `MapAssetBundle` to include necessary JavaScript and CSS for the map.
  - Renders the `map/index` template.
  - Triggers a custom event to allow dynamic modification of the Location JSON data.

### Asset Bundle

- **File**: `plugins/leadflex/src/assetbundles/MapAssetBundle.php`
- **Purpose**: Includes the `map.js` file which contains the logic for map interactions.

## Custom Event: ModifyLocationJsonEvent

### Purpose

The `ModifyLocationJsonEvent` allows developers to dynamically add or modify data in the Location JSON used by the map.

### Event Class

- **File**: `plugins/leadflex/src/events/ModifyLocationJsonEvent.php`
- **Properties**:
  - `locationJson`: An array representing the location data that can be modified by event listeners.

### Triggering the Event

- **Location**: The event is triggered in the `MapController` when processing the Location JSON.
- **Code Example**:
  ```php
  $event = new ModifyLocationJsonEvent([
      'locationJson' => $locationJson,
  ]);
  $this->trigger(self::EVENT_MODIFY_LOCATION_JSON, $event);
  ```

### Listening to the Event

- **Implementation**: Listeners can be registered in the plugin's `init` method to modify the `locationJson`.
- **Example**:
  ```php
  Event::on(
      MapController::class,
      MapController::EVENT_MODIFY_LOCATION_JSON,
      function (ModifyLocationJsonEvent $event) {
          $event->locationJson['additionalData'] = 'New Data';
      }
  );
  ```

## JavaScript Enhancements

### File: `map.js`

- **Path**: `/Users/jeffreybenusa/Sites/LF-drive4marten/plugins/leadflex/src/assets/map/src/js/map.js`
- **Functionality**: 
  - Initializes the map and handles user interactions.
  TODO: add modal confirmataion with optional reason to for removing a job from advertisment.

## Conclusion

These updates enhance the LeadFlex plugin by providing a flexible map interface and allowing for dynamic data manipulation through custom events. For further customization or questions, please refer to the code comments or contact the development team.


