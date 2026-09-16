# JeffAdmin

**Version:** 1.0.0

**First stable release.** JeffAdmin **1.0.0** is the initial production-ready release of the plugin.

JeffAdmin is a CakePHP 5 admin UI plugin: layout, assets, view helpers, display switches, and a Bake theme that generates list, form, and view screens in one consistent look.

Composer: [`kavezoo/jeffadmin`](https://packagist.org/packages/kavezoo/jeffadmin)

## Credits & inspiration

JeffAdmin’s visual foundation comes from **[CoolAdmin](https://colorlib.com/polygon/cooladmin/index.html)** — the HTML admin template whose structure, sidebar, and card-based screens shaped the plugin’s CSS and layout.

**[PikeAdmin](https://github.com/cnizzardini/cakephp-pike)** provided the idea of turning that kind of admin experience into a **CakePHP-native** package: Bake-driven CRUD, helpers, and a reusable plugin instead of a one-off theme copy.

JeffAdmin builds on both: CoolAdmin for how it looks, PikeAdmin for how a CakePHP admin plugin should be packaged and generated — then extends the result with Tom Select, SweetAlert delete flows, configurable `$show` switches, related-record tabs, and more.

## Requirements

- PHP 8.1+
- CakePHP 5.x
- [`cakephp/bake`](https://packagist.org/packages/cakephp/bake) (dev) — only if you bake Admin controllers/templates with the JeffAdmin theme

## Installation

Create a CakePHP ~5.4 app (or use an existing one), then require the plugin:

```bash
composer create-project --prefer-dist cakephp/app:~5.4 my_app_name
cd my_app_name
composer require kavezoo/jeffadmin:^1.0
composer require --dev cakephp/bake
```

Or, without a version constraint (latest stable):

```bash
composer require kavezoo/jeffadmin
composer require --dev cakephp/bake
```

On Windows, use `php bin/cake.php …` instead of the `bin/cake` shell script.

## Setup

### 1. Load the plugin

In `config/plugins.php`:

```php
<?php

return [
    // …
    'JeffAdmin' => [],
];
```

Or in `src/Application.php`:

```php
public function bootstrap(): void
{
    parent::bootstrap();

    $this->addPlugin('JeffAdmin');
}
```

The plugin bootstrap:

- loads `JeffAdmin.show` and `JeffAdmin.nav`
- registers helpers: `Icon`, `Action`, `Format`, `Input`, `Layout`
- sets `Bake.theme` to `JeffAdmin` when no other bake theme is configured

### 2. Session (host bootstrap)

Add session config at the end of `config/bootstrap.php` (Bake theme write is optional if the plugin already set it):

```php
use Cake\Core\Configure;

Configure::write('Bake.theme', 'JeffAdmin');

Configure::write('Session', [
    'defaults' => 'php',
    'cookie' => 'NameOfCookie',
    'timeout' => 4320, // 3 days
]);
```

### 3. Helpers

You do **not** need to load JeffAdmin helpers in `src/View/AppView.php` — the plugin registers them automatically.

### 4. Admin AppController

Create `src/Controller/Admin/AppController.php`:

```php
<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use JeffAdmin\Controller\AppController as JeffAdminAppController;

class AppController extends JeffAdminAppController
{
    public function initialize(): void
    {
        parent::initialize();
    }
}
```

The layout (`JeffAdmin.default`) comes from the plugin AppController.

### 5. Admin routes

In `config/routes.php`:

```php
$routes->prefix('Admin', function (RouteBuilder $builder) {
    $builder->setRouteClass(DashedRoute::class);
    $builder->connect('/', ['controller' => 'Cities', 'action' => 'index']);
    $builder->fallbacks(DashedRoute::class);
});
```

Point the default controller/action at your own models.

### 6. Bake Admin files

```bash
bin/cake bake model all
bin/cake bake controller all --prefix Admin --no-test
bin/cake bake template all --prefix Admin
```

Single model:

```bash
bin/cake bake model Cities --no-test
bin/cake bake controller Cities --prefix Admin --force --no-test
bin/cake bake template Cities --prefix Admin --force
```

## Multiple prefixes (e.g. Member)

1. Add the prefix in `config/routes.php`:

```php
$routes->prefix('Member', function (RouteBuilder $builder) {
    $builder->setRouteClass(DashedRoute::class);
    $builder->connect('/', ['controller' => 'Clubs', 'action' => 'index']);
    $builder->fallbacks(DashedRoute::class);
});
```

2. Create `src/Controller/Member/AppController.php` the same way as Admin (extend `JeffAdmin\Controller\AppController`).

3. Bake:

```bash
bin/cake bake controller all --prefix Member --no-test
bin/cake bake template all --prefix Member
```

## Sidebar menu (`JeffAdmin.nav`)

The plugin ships an **empty** sidebar. Define items in the host (e.g. `config/bootstrap.php`):

```php
use Cake\Core\Configure;

Configure::write('JeffAdmin.nav', [
    [
        'type' => 'group',
        'label' => 'Tables',
        'icon' => 'fa-solid fa-table',
        'items' => [
            ['controller' => 'Articles', 'label' => 'Articles'],
            ['controller' => 'Tags', 'label' => 'Tags'],
        ],
    ],
    [
        'type' => 'link',
        'controller' => 'Dashboard',
        'label' => 'Dashboard',
        'icon' => 'fa-solid fa-gauge',
    ],
]);
```

Labels go through `__()`. Optional per-item keys: `action` (default `index`), `prefix`.

## Overriding layout elements

The layout loads shell elements through `Layout->element()` (prefix-first).  
If a host file exists for the current request prefix, it is used instead of the plugin default.

Checked names: `aside`, `footer`, `header`, `header_top`, `nav`.

Resolution order for prefix `Admin` (example: `nav`):

```text
templates/Admin/element/nav.php
templates/Admin/element/plugin/JeffAdmin/nav.php
templates/plugin/JeffAdmin/Admin/element/nav.php
templates/plugin/JeffAdmin/element/nav.php
plugins/JeffAdmin/templates/element/nav.php   (plugin default)
```

For another prefix (e.g. `Member`), start from `templates/Member/element/…` the same way.

Copy a starting point from `vendor/kavezoo/jeffadmin/templates/element/` if needed.

UI assets (CSS/JS, Tom Select, SweetAlert, layout) live **only** in the plugin. Do not copy `webroot` into the host — other apps get them via Composer.

## Display switches (`$show`)

Global defaults: plugin `config/show.php` → `Configure::read('JeffAdmin')`.

Notable defaults:

- `index.rowId` is `false` (id column off unless you enable it)
- `password` / `passwords` / `passwd` fields are skipped by the Bake theme (forms, index, view, related tables, and header search)

Per-template override in baked files:

```php
use Cake\Core\Configure;

$show = Configure::read('JeffAdmin');
$showLocal = [];

$showLocal['edit'] = [
//    'relatedTables' => false,
//    'deleteButton'  => false,
];

$show = array_merge($show['edit'] ?? [], $showLocal['edit']);
```

## Updating

```bash
composer update kavezoo/jeffadmin
```

If `composer.json` pins an exact version, widen the constraint (e.g. `^1.0`) first, then update.

## License

MIT — see [LICENSE](LICENSE).

Enjoy JeffAdmin!

<p style="text-align: right; font-style: italic;">Jeff Shoemaker</p>
