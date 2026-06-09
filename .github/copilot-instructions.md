---
description: "Workspace conventions and patterns for BadDragon PHP framework development in the Arkafe project."
applyTo: ["**/*.php"]
---

# BadDragon Framework Conventions

This workspace uses the **BadDragon PHP MVC framework**. When working with PHP files in this project, follow these conventions:

## Directory Structure

```
BadDragon/          ← Framework core (read-only usually)
  Engine/           ← Router, Controller classes
  Toolbox/          ← ORM, Validation, Utilities
  
SampleApp/
  App/
    Controller/     ← Business logic: Module/Controller/Method.php
    View/           ← Template files: Module/Controller/Generate/Page.php
    Routes.php      ← GET route definitions
    env.php         ← Environment configuration
```

## Routing Hierarchy

All requests follow: **Module → Controller → Method → Script**

- **GET Routes**: Defined in `App/Routes.php` with format: `"slug" => "/module/controller/method"`
- **POST Routes**: Use `a` parameter: `POST ?a=module-controller-method`
- **Router**: Parses requests and populates `$module`, `$controller`, `$method`, `$uri`

## File Execution Order

When a request is routed, three files load sequentially:

1. **Base Controller** (`App/Controller/Controller.php`) — Session, initialization
2. **Module Controller** (`App/Controller/{Module}/{Controller}.php`) — Module-level setup
3. **Method Script** (`App/Controller/{Module}/{Controller}/{Method}.php`) — Request handler

## Coding Patterns

### Controller (Method Script)
```php
<?php
$data = [];
// Prepare data for the view
if ($_POST) {
    // Handle form submission
    $data['message'] = 'Success';
}
// Always call view() at the end
view($route, 'pageName', $data);
?>
```

### Database Queries
Always use the ORM with **prepared statements**:
```php
$records = $orm->query("SELECT * FROM table WHERE id = ?", [$id]);
```

### Form Validation
Use the `bdDataValidation` class:
```php
$validator = new bdDataValidation();
if ($validator->min_length($_POST['username'], 3)) {
    // Valid
}
```

### Rendering Views
Call `view()` to render templates with automatic variable extraction:
```php
view($route, 'pageName', ['user' => $userData, 'items' => $itemList]);
// In template: $user and $items are automatically available
```

## File Naming

- **Classes**: PascalCase (`Router`, `Controller`, `Validation`)
- **Functions**: lowercase_with_underscores (`view()`, `render_template()`)
- **Files**: Named after primary class or functionality
- **Variables**: camelCase (`$userData`, `$routePath`)

## Common Tasks

### Adding a New Route
1. Define in `App/Routes.php`: `"endpoint" => "/module/controller/method"`
2. Create file: `App/Controller/Module/Controller/Method.php`
3. Prepare data and call `view($route, 'templateName', $data)`

### Adding Validation
1. Use `bdDataValidation` in your method script
2. Methods: `min_length()`, `max_length()`, `regex_match()`, `valid_email()`, etc.

### Database Operations
1. Use `$orm->query()` with prepared statements
2. Always pass parameters as array: `query("SELECT * FROM t WHERE id = ?", [$id])`
3. Supports MySQL and SQLite

## Notes

- The framework uses PSR-4 autoloading for the `BadDragon` namespace
- All configuration comes from `env.php` (set in `Config.php`)
- Debug mode controlled by `$bdDebugMode` in `Config.php`
- The `Common.php` file loads Validation and ORM globally
