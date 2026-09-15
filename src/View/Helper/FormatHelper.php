<?php
declare(strict_types=1);

namespace JeffAdmin\View\Helper;

use Cake\I18n\I18n;
use Cake\View\Helper;

/**
 * JeffAdmin formázó helper (pénznem, stb.).
 *
 *   <?= $this->Format->money($entity->price) ?>
 *   <?= $this->Format->money($entity->amount, 'EUR') ?>
 */
class FormatHelper extends Helper
{
    /**
     * @var array<string>
     */
    protected array $helpers = ['Number'];

    protected array $_defaultConfig = [
        'defaultCurrency' => 'HUF',
        'huSymbol' => 'Ft.',
    ];

    /**
     * Pénzösszeg lokalizált megjelenítése.
     *
     * Magyar locale + HUF → szám + „Ft.”
     * Egyéb locale / kikényszerített pénznem → NumberHelper::currency (pl. HUF, EUR).
     *
     * @param string|int|float|null $amount Összeg
     * @param string|null $currency ISO pénznemkód (null = defaultCurrency)
     */
    public function money(string|int|float|null $amount, ?string $currency = null): string
    {
        if ($amount === null || $amount === '') {
            return '';
        }

        $code = strtoupper($currency ?: (string)$this->getConfig('defaultCurrency'));
        $locale = strtolower((string)I18n::getLocale());
        $isHu = str_starts_with($locale, 'hu');

        if ($isHu && $code === 'HUF') {
            $formatted = $this->Number->format((float)$amount, [
                'places' => 0,
                'locale' => I18n::getLocale(),
            ]);

            return h($formatted) . ' <span class="currency">' . h((string)$this->getConfig('huSymbol')) . '</span>';
        }

        return $this->Number->currency((float)$amount, $code, [
            'locale' => I18n::getLocale(),
        ]);
    }

    /**
     * Mezőnév alapján tipikus pénzmező-e.
     */
    public static function isCurrencyField(string $field): bool
    {
        $name = strtolower($field);
        $exact = [
            'price', 'amount', 'cost', 'total', 'fee', 'net', 'gross',
            'money', 'sum', 'ar', 'osszeg', 'dij', 'netto', 'brutto',
            'unit_price', 'unitprice', 'net_price', 'gross_price',
        ];
        if (in_array($name, $exact, true)) {
            return true;
        }

        foreach (['price', 'amount', 'cost', 'total', 'fee', 'money', 'osszeg', 'dij'] as $suffix) {
            if (str_ends_with($name, '_' . $suffix)) {
                return true;
            }
        }

        return false;
    }
}
