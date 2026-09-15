<?php
declare(strict_types=1);

namespace JeffAdmin\View\Helper;

use Cake\Core\Plugin;
use Cake\View\Helper;
use Cake\View\StringTemplateTrait;

/**
 * Inline SVG ikon helper (icons/{type}/{name}.svg).
 *
 * Ugyanaz a viselkedés, mint a régi PHP icon() függvény: a class és az
 * attribútumok az <svg> elemre kerülnek, nem wrapper span-re.
 *
 *   $this->Icon->icon('mail', 'record-link__icon')
 *   $this->Icon->icon('mail', 'record-link__icon', 'filled')
 *   $this->Icon->outline('mail', 'record-link__icon')
 *   $this->Icon->filled('mail', ['class' => 'record-link__icon', 'title' => 'Email'])
 *   $this->Icon->visible($entity->visible)
 */
class IconHelper extends Helper
{
    use StringTemplateTrait;

    protected array $_defaultConfig = [
        'basePath' => 'icons',
        'defaultType' => 'outline',
    ];

    /**
     * Belső memóriagyorsítótár, hogy ugyanazt a fájlt
     * egy oldalbetöltésen belül csak egyszer olvassa be.
     *
     * @var array<string, string>
     */
    protected array $_svgCache = [];

    /**
     * Régi PHP icon($name, $class, $style) megfelelője.
     *
     * @param string $name Az SVG fájl neve (.svg nélkül)
     * @param array|string $options CSS class, vagy opciók tömbje
     * @param string $style 'outline'|'filled' — csak ha a 2. paraméter string class
     */
    public function icon(string $name, array|string $options = [], string $style = ''): string
    {
        $options = $this->_normalizeOptions($options);
        if ($style !== '') {
            $options['type'] = $style;
        }

        return $this->render($name, $options);
    }

    /**
     * $this->Icon('mail', 'record-link__icon') — mint a régi icon() függvény.
     *
     * @param array|string $options
     */
    public function __invoke(string $name, array|string $options = [], string $style = ''): string
    {
        return $this->icon($name, $options, $style);
    }

    /**
     * Ikon renderelése a megadott mappából.
     *
     * @param string $name Az SVG fájl neve (.svg nélkül, pl. 'mail', 'link-chain')
     * @param array|string $options CSS class string, vagy: 'type', 'class', plusz HTML attribútumok
     */
    public function render(string $name, array|string $options = []): string
    {
        $options = $this->_normalizeOptions($options);

        $type = $options['type'] ?? $this->getConfig('defaultType');
        unset($options['type']);

        $extraClass = $options['class'] ?? '';
        unset($options['class']);

        $class = trim(sprintf('icon icon-%s icon-%s %s', $name, $type, $extraClass));

        if (!array_key_exists('aria-hidden', $options)) {
            $options['aria-hidden'] = 'true';
        }

        $basePath = rtrim((string)$this->getConfig('basePath'), '/');

        // 1) Fő app webroot/icons/{type}/{name}.svg
        $filePath = WWW_ROOT . $basePath . DS . $type . DS . $name . '.svg';

        // 2) Ha ott nincs, a JeffAdmin plugin webroot/icons
        if (!file_exists($filePath)) {
            $filePath = Plugin::path('JeffAdmin') . 'webroot' . DS . 'icons' . DS . $type . DS . $name . '.svg';
        }

        $svgContent = $this->_getSvgContent($filePath);

        if (!$svgContent) {
            return sprintf('<!-- Icon "%s" not found in /%s/%s/ -->', h($name), h($basePath), h($type));
        }

        $options['class'] = $class;
        $attrs = $this->templater()->formatAttributes($options);

        return (string)preg_replace('/<svg\b/', '<svg' . $attrs, $svgContent, 1);
    }

    /**
     * @param array|string $options CSS class string, vagy opciók tömbje
     */
    public function outline(string $name, array|string $options = []): string
    {
        $options = $this->_normalizeOptions($options);
        $options['type'] = 'outline';

        return $this->render($name, $options);
    }

    /**
     * @param array|string $options CSS class string, vagy opciók tömbje
     */
    public function filled(string $name, array|string $options = []): string
    {
        $options = $this->_normalizeOptions($options);
        $options['type'] = 'filled';

        return $this->render($name, $options);
    }

    /**
     * Boolean visible mező listanézethez: eye / eye-off ikon tooltippel.
     *
     *   <?= $this->Icon->visible($entity->visible) ?>
     *
     * @param bool|int|string|null $visible Igazságos érték (1/0, true/false)
     * @param array<string, mixed> $options Extra SVG attribútumok / class felülírás
     */
    public function visible(bool|int|string|null $visible = false, array $options = []): string
    {
        $isVisible = (bool)$visible;
        $label = $isVisible ? __('Visible') : __('Not visible');
        $class = trim(
            'boolean-icon ' . ($isVisible ? 'boolean-icon--yes' : 'boolean-icon--no')
            . ' ' . ($options['class'] ?? '')
        );
        unset($options['class']);

        return $this->outline($isVisible ? 'eye' : 'eye-off', array_merge([
            'class' => $class,
            'data-bs-toggle' => 'tooltip',
            'data-bs-title' => $label,
            'aria-label' => $label,
        ], $options));
    }

    /**
     * String második paramétert CSS classként kezeli.
     *
     * @param array|string $options
     * @return array<string, mixed>
     */
    protected function _normalizeOptions(array|string $options): array
    {
        if (is_string($options)) {
            return $options === '' ? [] : ['class' => $options];
        }

        return $options;
    }

    protected function _getSvgContent(string $filePath): ?string
    {
        if (isset($this->_svgCache[$filePath])) {
            return $this->_svgCache[$filePath];
        }

        if (!is_readable($filePath)) {
            return null;
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        $content = preg_replace('/<\?xml.*?\?>/i', '', $content);
        $content = preg_replace('/<!--.*?-->/s', '', $content);
        // Egy sorba: a böngésző forrásnézetében ne legyen balra húzott tördelés.
        $content = preg_replace('/\s+/', ' ', trim((string)$content));
        $content = preg_replace('/> </', '><', $content);

        $this->_svgCache[$filePath] = $content;

        return $this->_svgCache[$filePath];
    }
}
