<?php
declare(strict_types=1);

namespace JeffAdmin\View\Helper;

use Cake\View\Helper;

/**
 * Layout shell elements with host / prefix override.
 *
 * Resolution order (first existing wins; Cake prefix cascade applies):
 * 1. templates/{Prefix}/element/{name}.php
 * 2. templates/{Prefix}/element/plugin/JeffAdmin/{name}.php
 * 3. templates/plugin/JeffAdmin/{Prefix}/element/{name}.php
 * 4. templates/plugin/JeffAdmin/element/{name}.php
 * 5. plugins/JeffAdmin/templates/… (plugin defaults)
 */
class LayoutHelper extends Helper
{
    /**
     * Shell elements intended for per-prefix host overrides.
     *
     * @var list<string>
     */
    public const SHELL_ELEMENTS = [
        'aside',
        'footer',
        'header',
        'header_top',
        'nav',
    ];

    /**
     * Render a layout shell element, preferring host/prefix overrides.
     *
     * @param array<string, mixed> $data
     * @param array<string, mixed> $options
     */
    public function element(string $name, array $data = [], array $options = []): string
    {
        $name = ltrim(str_replace('\\', '/', $name), '/');
        $view = $this->getView();

        foreach ($this->candidates($name) as $candidate) {
            if ($view->elementExists($candidate)) {
                return $view->element($candidate, $data, $options);
            }
        }

        return $view->element('JeffAdmin.' . $name, $data, $options);
    }

    /**
     * @return list<string>
     */
    protected function candidates(string $name): array
    {
        return [
            $name,
            'plugin/JeffAdmin/' . $name,
            'JeffAdmin.' . $name,
        ];
    }
}
