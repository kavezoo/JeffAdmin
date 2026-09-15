<?php
/**
 * Oldalsáv navigáció.
 *
 * @var \App\View\AppView $this
 * @var string $controller
 * @var string $prefix
 */
$current = (string)($controller ?? $this->getRequest()->getParam('controller') ?? '');
$prefixName = ($prefix ?? '') !== '' ? (string)$prefix : 'Admin';

$tables = ['Customers', 'Orders', 'Items', 'OrdersItems'];
$tablesOpen = in_array($current, $tables, true);

$indexUrl = fn(string $name): array => [
    'prefix' => $prefixName,
    'controller' => $name,
    'action' => 'index',
];
?>
<nav class="navbar-sidebar">
    <ul class="list-unstyled navbar__list">
        <li class="has-sub<?= $tablesOpen ? ' active' : '' ?>">
            <a class="js-arrow<?= $tablesOpen ? ' open' : '' ?>" href="#">
                <i class="fa-solid fa-table" aria-hidden="true"></i><?= __('Tables') ?>
            </a>
            <ul class="list-unstyled navbar__sub-list js-sub-list"<?= $tablesOpen ? ' style="display: block;"' : '' ?>>
                <li<?= $current === 'Customers' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(__('Customers'), $indexUrl('Customers')) ?>
                </li>
                <li<?= $current === 'Orders' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(__('Orders'), $indexUrl('Orders')) ?>
                </li>
                <li<?= $current === 'Items' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(__('Items'), $indexUrl('Items')) ?>
                </li>
                <li<?= $current === 'OrdersItems' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(__('OrdersItems'), $indexUrl('OrdersItems')) ?>
                </li>
            </ul>
        </li>
        <li<?= $current === 'Cities' ? ' class="active"' : '' ?>>
            <?= $this->Html->link(
                '<i class="fa-solid fa-city" aria-hidden="true"></i>' . h(__('Cities')),
                $indexUrl('Cities'),
                ['escape' => false]
            ) ?>
        </li>
    </ul>
</nav>
