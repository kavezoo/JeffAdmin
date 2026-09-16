# JeffAdmin

JeffAdmin plugin for CakePHP 5 projects — admin UI, helpers, assets, and Bake theme.

Composer: [`kavezoo/jeffadmin`](https://packagist.org/packages/kavezoo/jeffadmin)

## Usage

First, create a CakePHP ~5.4 project, then install the JeffAdmin plugin:

```bash
composer create-project --prefer-dist cakephp/app:~5.4 my_app_name
cd my_app_name
composer require kavezoo/jeffadmin
composer require --dev cakephp/bake
```

Windows: use `php bin/cake.php …` instead of the `bin/cake` shell script.

### 1. Load the Plugin

Add the plugin to `config/plugins.php`:

```php
<?php

return [
    // …
    'JeffAdmin' => [],
];
```

Alternatively, in `src/Application.php`:

```php
public function bootstrap(): void
{
    parent::bootstrap();

    $this->addPlugin('JeffAdmin');
}
```

The plugin bootstrap loads `JeffAdmin.show`, registers helpers (`Icon`, `Action`, `Format`, `Input`), and sets `Bake.theme` to `JeffAdmin` when no other bake theme is configured.

### 2. Configure Bootstrap

Add the following lines to the end of your `config/bootstrap.php` file (session is required; Bake theme is optional if the plugin already set it):

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

You do **not** need to load JeffAdmin helpers in `src/View/AppView.php` — they are registered automatically by the plugin.

### 4. Create Admin Controller Directory & AppController

Create a new directory: `src/Controller/Admin/`

Create `src/Controller/Admin/AppController.php` with the following content:

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

### 5. Define Admin Routes

Add the `Admin` prefix in `config/routes.php`:

```php
$routes->prefix('Admin', function (RouteBuilder $builder) {
    $builder->setRouteClass(DashedRoute::class);
    $builder->connect('/', ['controller' => 'Cities', 'action' => 'index']);
    $builder->fallbacks(DashedRoute::class);
});
```

Adjust the default controller/action to match your app.

### 6. Bake the Admin Files

Bake models, then Admin controllers and templates with the JeffAdmin theme:

```bash
bin/cake bake model all
bin/cake bake controller all --prefix Admin --no-test
bin/cake bake template all --prefix Admin
```

Or a single model:

```bash
bin/cake bake model Cities --no-test
bin/cake bake controller Cities --prefix Admin --force --no-test
bin/cake bake template Cities --prefix Admin --force
```

## Multiple Prefixes (e.g. Member)

If you need additional prefixes (like `Member`), follow the same pattern:

1. Add the prefix to `config/routes.php`:

```php
$routes->prefix('Member', function (RouteBuilder $builder) {
    $builder->setRouteClass(DashedRoute::class);
    $builder->connect('/', ['controller' => 'Clubs', 'action' => 'index']);
    $builder->fallbacks(DashedRoute::class);
});
```

2. Create `src/Controller/Member/AppController.php`:

```php
<?php
declare(strict_types=1);

namespace App\Controller\Member;

use JeffAdmin\Controller\AppController as JeffAdminAppController;

class AppController extends JeffAdminAppController
{
    public function initialize(): void
    {
        parent::initialize();
    }
}
```

3. Bake files for the new prefix:

```bash
bin/cake bake controller all --prefix Member --no-test
bin/cake bake template all --prefix Member
```

## Customizing Layout Elements & Menu

To customize headers, footers, or navigation, copy the default elements from the plugin into your project (e.g. `templates/element/` or a prefix-specific path your layout uses):

```bash
mkdir -p templates/element
cp vendor/kavezoo/jeffadmin/templates/element/header.php templates/element/header.php
cp vendor/kavezoo/jeffadmin/templates/element/header_top.php templates/element/header_top.php
cp vendor/kavezoo/jeffadmin/templates/element/footer.php templates/element/footer.php
cp vendor/kavezoo/jeffadmin/templates/element/nav.php templates/element/nav.php
```

## Display switches (`$show`)

Global defaults: plugin `config/show.php` → `Configure::read('JeffAdmin')`.

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

Enjoy JeffAdmin!
