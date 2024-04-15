# Laravel Renderable
Present an Eloquent model or an associative array in HTML table view.

## Installation
```
composer require wongyip/laravel-rendetable
```

## Usage
```php
use Wongyip\Laravel\Renderable\Renderable;

// Attributes to be rendered, can be an Eloquent model.
$user = [
    'id' => 1999,
    'surname' => 'SOME',
    'last_name' => 'Body',
    'roles' => ['Operator', 'Editor', 'Supervisor'],
    'gender' => 'Male',
    'birthday' => '29th Feb',
    'active' => false
];

// Render all attributes except 'gender' and 'birthday'.
$included = true;
$excluded = ['gender', 'birthday'];

// Custom Labels
$labels = [
    'surname' => 'First Name',
    'active' => 'Status'
];

// Make
$r = Renderable::table($user, $included, $excluded);

// Render as <ul>, expected array value.
$r->typeUL('roles');

// Print 'Active' and 'Blocked' when attribute 'active' is TRUE and FALSE respectively.
$r->typeBool('active', 'Active', 'Blocked');

// Overwrite auto-generated labels.
$r->labels($labels);
    
// To HTML.
echo $r->render();
```

### Output
```html
<div id="renderable-12345678-container" class="renderable-container">
    <table id="renderable-12345678" class="renderable-table table table-bordered table-hover">
        <thead class="thead-light">
            <tr>
                <th>Field</th>
                <th>Value</th>
            </tr>
        </thead>
        <tbody class="renderable-body">
            <tr class="field-id">
                <th class="renderable-label">ID</th>
                <td class="renderable-value">1999</td>
            </tr>
            <tr class="field-surname">
                <th class="renderable-label">First Name</th>
                <td class="renderable-value">SOME</td>
            </tr>
            <tr class="field-last_name">
                <th class="renderable-label">Last Name</th>
                <td class="renderable-value">Body</td>
            </tr>
            <tr class="field-roles">
                <th class="renderable-label">Roles</th>
                <td class="renderable-value">
                    <ul>
                        <li>Operator</li>
                        <li>Editor</li>
                        <li>Supervisor</li>
                    </ul>
                </td>
            </tr>
            <tr class="field-active">
                <th class="renderable-label">Status</th>
                <td class="renderable-value">Blocked</td>
            </tr>
        </tbody>
    </table>
</div>
```

## Output Explained
- The main output is a `<table>` tag wrapped in a container `<div>` tag.
- The `Renderable` object generates its own `Renderable.id` randomly on instantiate (`12345678`), which is changeable with the `Renderable.id()` method.
- The main tag will have an `id` attribute derived from the `Renderable.id`, prefixed with `renderable-` by default, configurable via `/config/renderable.php`) and changeable on run-time by updating the `Renderable.options.idPrefix` property.
- The container tag's ID is further suffixed with `-containter` by default, configurable via `/config/renderable.php`) and changeable on run-time by updating the `Renderable.options.containerIdSuffix` property.
- Field labels and values are rendered base on the setup. 

## Notes
- Output is formatted with [HTML Beautify](https://github.com/wongyip/html-beautify).
- Output is sanitized with [HTML Purifier](https://github.com/ezyang/htmlpurifier).
