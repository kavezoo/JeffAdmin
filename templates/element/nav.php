<?php
/**
 * Admin sidebar — templates/Admin/element/nav.php
 *
 * @var \Cake\View\View $this
 * @var string $controller
 * @var string $prefix
 */
$current = (string)($controller ?? $this->request->getParam('controller') ?? '');
$prefixName = (string)($prefix ?? $this->request->getParam('prefix') ?? 'Admin');
?>
<aside class="menu-sidebar" id="main-sidebar">
    <div class="logo">
        <a class="logo-link" href="<?= $this->Url->build('/') ?>" aria-label="JeffAdmin home">
            <span class="logo-mark" aria-hidden="true">J</span>
            <span class="logo-text">JeffAdmin</span>
        </a>
        <button type="button" class="sidebar-close js-sidebar-toggle" aria-label="Close navigation">
            <i class="fa-solid fa-xmark" aria-hidden="true"></i>
        </button>
    </div>
    <div class="menu-sidebar__content js-scrollbar1">
        <nav class="navbar-sidebar">
            <ul class="list-unstyled navbar__list">
                <li<?= $current === 'Customers' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(
                        '<i class="fa-solid fa-users" aria-hidden="true"></i>' . h(__('Customers')),
                        ['prefix' => $prefixName, 'controller' => 'Customers', 'action' => 'index'],
                        ['escape' => false]
                    ) ?>
                </li>
                <li<?= $current === 'Orders' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(
                        '<i class="fa-solid fa-cart-shopping" aria-hidden="true"></i>' . h(__('Orders')),
                        ['prefix' => $prefixName, 'controller' => 'Orders', 'action' => 'index'],
                        ['escape' => false]
                    ) ?>
                </li>
                <li<?= $current === 'Items' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(
                        '<i class="fa-solid fa-box" aria-hidden="true"></i>' . h(__('Items')),
                        ['prefix' => $prefixName, 'controller' => 'Items', 'action' => 'index'],
                        ['escape' => false]
                    ) ?>
                </li>
                <li<?= $current === 'OrdersItems' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(
                        '<i class="fa-solid fa-list" aria-hidden="true"></i>' . h(__('OrdersItems')),
                        ['prefix' => $prefixName, 'controller' => 'OrdersItems', 'action' => 'index'],
                        ['escape' => false]
                    ) ?>
                </li>
                <li<?= $current === 'Cities' ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(
                        '<i class="fa-solid fa-city" aria-hidden="true"></i>' . h(__('Cities')),
                        ['prefix' => $prefixName, 'controller' => 'Cities', 'action' => 'index'],
                        ['escape' => false]
                    ) ?>
                </li>
            </ul>
        </nav>
    </div>
</aside>
