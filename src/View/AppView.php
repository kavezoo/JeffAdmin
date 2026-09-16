<?php
declare(strict_types=1);

namespace JeffAdmin\View;

use Cake\View\View;

/**
 * JeffAdmin View — plugin helper-ek (pl. Icon).
 *
 * A host AppView-t nem kell módosítani: a JeffAdminPlugin bootstrap
 * a Controller.initialize eseményen automatikusan betölti az Icon helper-t.
 * Ez az osztály akkor kell, ha a view class explicit JeffAdmin.AppView.
 *
 * @property \JeffAdmin\View\Helper\IconHelper $Icon
 */
class AppView extends View
{
    /**
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();

        //$this->loadHelper('JeffAdmin.Icon');
    }
}
