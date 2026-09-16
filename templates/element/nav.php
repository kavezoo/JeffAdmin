<?php
/**
 * Oldalsáv navigáció — tételek: Configure::read('JeffAdmin.nav').
 *
 * Felülírás más projektben: templates/plugin/JeffAdmin/element/nav.php
 * vagy Configure::write('JeffAdmin.nav', […]) a host bootstrapjában.
 *
 * @var \Cake\View\View $this
 * @var string $controller
 * @var string $prefix
 */
use Cake\Core\Configure;

$current = (string)($controller ?? $this->getRequest()->getParam('controller') ?? '');
$prefixName = ($prefix ?? '') !== '' ? (string)$prefix : (string)($this->getRequest()->getParam('prefix') ?? 'Admin');
$items = Configure::read('JeffAdmin.nav') ?? [];
if (!is_array($items)) {
    $items = [];
}

$indexUrl = function (string $name, ?string $action = null, ?string $itemPrefix = null) use ($prefixName): array {
    return [
        'prefix' => $itemPrefix !== null && $itemPrefix !== '' ? $itemPrefix : $prefixName,
        'controller' => $name,
        'action' => $action !== null && $action !== '' ? $action : 'index',
    ];
};
?>
<nav class="navbar-sidebar">
    <ul class="list-unstyled navbar__list">
        <?php foreach ($items as $entry): ?>
            <?php
            if (!is_array($entry) || empty($entry['type'])) {
                continue;
            }
            $type = (string)$entry['type'];
            ?>
            <?php if ($type === 'group'): ?>
                <?php
                $children = $entry['items'] ?? [];
                if (!is_array($children)) {
                    $children = [];
                }
                $childControllers = [];
                foreach ($children as $child) {
                    if (is_array($child) && !empty($child['controller'])) {
                        $childControllers[] = (string)$child['controller'];
                    }
                }
                $groupOpen = in_array($current, $childControllers, true);
                $icon = (string)($entry['icon'] ?? 'fa-solid fa-folder');
                $label = (string)($entry['label'] ?? 'Menu');
                ?>
                <li class="has-sub<?= $groupOpen ? ' active' : '' ?>">
                    <a class="js-arrow<?= $groupOpen ? ' open' : '' ?>" href="#">
                        <i class="<?= h($icon) ?>" aria-hidden="true"></i><?= h(__($label)) ?>
                    </a>
                    <ul class="list-unstyled navbar__sub-list js-sub-list"<?= $groupOpen ? ' style="display: block;"' : '' ?>>
                        <?php foreach ($children as $child): ?>
                            <?php
                            if (!is_array($child) || empty($child['controller'])) {
                                continue;
                            }
                            $childController = (string)$child['controller'];
                            $childLabel = (string)($child['label'] ?? $childController);
                            $childAction = isset($child['action']) ? (string)$child['action'] : 'index';
                            $childPrefix = isset($child['prefix']) ? (string)$child['prefix'] : null;
                            ?>
                            <li<?= $current === $childController ? ' class="active"' : '' ?>>
                                <?= $this->Html->link(
                                    __($childLabel),
                                    $indexUrl($childController, $childAction, $childPrefix)
                                ) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
            <?php elseif ($type === 'link'): ?>
                <?php
                if (empty($entry['controller'])) {
                    continue;
                }
                $linkController = (string)$entry['controller'];
                $linkLabel = (string)($entry['label'] ?? $linkController);
                $linkAction = isset($entry['action']) ? (string)$entry['action'] : 'index';
                $linkPrefix = isset($entry['prefix']) ? (string)$entry['prefix'] : null;
                $linkIcon = (string)($entry['icon'] ?? '');
                $linkHtml = ($linkIcon !== '' ? '<i class="' . h($linkIcon) . '" aria-hidden="true"></i>' : '')
                    . h(__($linkLabel));
                ?>
                <li<?= $current === $linkController ? ' class="active"' : '' ?>>
                    <?= $this->Html->link(
                        $linkHtml,
                        $indexUrl($linkController, $linkAction, $linkPrefix),
                        ['escape' => false]
                    ) ?>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</nav>
