<?php
/**
 * Inline SVG icon from icons/{style}/{name}.svg (e.g. icons/outline/link-chain.svg).
 */
function icon(string $name, string $class = '', string $style = 'outline'): string
{
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'icons' . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . $name . '.svg';
    if (!is_readable($file)) {
        return '';
    }

    $svg = file_get_contents($file);
    if ($svg === false) {
        return '';
    }

    $svg = preg_replace('/<!--.*?-->\s*/s', '', $svg, 1);
    $attrs = ' aria-hidden="true"';
    if ($class !== '') {
        $attrs .= ' class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '"';
    }

    return (string) preg_replace('/<svg\b/', '<svg' . $attrs, $svg, 1);
}
