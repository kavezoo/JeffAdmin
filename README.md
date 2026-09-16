# JeffAdmin

CakePHP 5 admin UI plugin: layout, helpers, assets, Bake theme.

## Telepítés (más projektben)

```bash
composer require jeff/jeff-admin
composer require --dev cakephp/bake
```

Path / VCS repository esetén a host `composer.json`-ban:

```json
{
  "repositories": [
    { "type": "path", "url": "../JeffAdmin", "options": { "symlink": true } }
  ],
  "require": {
    "jeff/jeff-admin": "*"
  }
}
```

## Aktiválás

`config/plugins.php`:

```php
'JeffAdmin' => [],
```

A plugin bootstrap:

- betölti a `JeffAdmin.show` megjelenítési configot,
- helperöket regisztrál (`Icon`, `Action`, `Format`, `Input`),
- beállítja a `Bake.theme = JeffAdmin` értéket, ha még nincs más theme.

## Admin prefix

Vékony host controller (layout a pluginból jön):

```php
namespace App\Controller\Admin;

use JeffAdmin\Controller\AppController as JeffAdminAppController;

class AppController extends JeffAdminAppController
{
}
```

## Bake

```text
php bin/cake.php bake controller ModelName --prefix Admin --force --no-test
php bin/cake.php bake template ModelName --prefix Admin --force
```

A generált index / add / edit / view a plugin `templates/bake/` sablonjaiból készül (Related tables dropdown, Settings fül, InputHelper, stb.). A CSS/JS a plugin `webroot/` alatt van (`JeffAdmin./css/main`, …) — a layout automatikusan betölti.

(Added WebHook)