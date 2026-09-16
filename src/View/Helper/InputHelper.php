<?php
declare(strict_types=1);

namespace JeffAdmin\View\Helper;

use Cake\I18n\I18n;
use Cake\View\Helper;
use DateTimeInterface;

/**
 * JeffAdmin űrlapmezők — container nélküli Form control + flatpickr dátum/idő.
 *
 * Általános mező (nincs inputContainer wrapper):
 *   <?= $this->Input->control('name', ['class' => 'form-control']) ?>
 *
 * Flatpickr (vendor) — type=text + data-fp, POST = submit formátum:
 *   <?= $this->Input->date('date') ?>
 *   <?= $this->Input->time('time') ?>
 *   <?= $this->Input->datetime('datetime', ['disabled' => true]) ?>
 *
 * Lista / megjelenítés:
 *   <?= $this->Input->formatDate($entity->date) ?>
 *   <?= $this->Input->formatTime($entity->time) ?>
 *   <?= $this->Input->formatDatetime($entity->datetime) ?>
 *
 * @property \Cake\View\Helper\FormHelper $Form
 */
class InputHelper extends Helper
{
    protected array $helpers = ['Form'];

    protected array $_defaultConfig = [
        'locale' => null,
        /** POST / hidden flatpickr érték (locale-független). */
        'submitFormats' => [
            'date' => 'Y-m-d',
            'time' => 'H:i',
            'datetime' => 'Y-m-d H:i:s',
        ],
        /** Megjelenítési formátum locale szerint. */
        'displayFormats' => [
            'hu' => [
                'date' => 'Y.m.d.',
                'time' => 'H:i',
                'datetime' => 'Y.m.d. H:i',
            ],
            'en' => [
                'date' => 'Y-m-d',
                'time' => 'H:i',
                'datetime' => 'Y-m-d H:i',
            ],
        ],
    ];

    /**
     * Form->control inputContainer nélkül (JeffAdmin soros layout).
     *
     * @param array<string, mixed> $options
     */
    public function control(string $field, array $options = []): string
    {
        $options += ['label' => false];

        return $this->Form->control($field, $this->withBareContainer($options));
    }

    /**
     * Dátum mező (flatpickr data-fp="date").
     *
     * @param array<string, mixed> $options
     */
    public function date(string $field, array $options = []): string
    {
        return $this->temporal($field, 'date', $options);
    }

    /**
     * Idő mező (flatpickr data-fp="time").
     *
     * @param array<string, mixed> $options
     */
    public function time(string $field, array $options = []): string
    {
        return $this->temporal($field, 'time', $options);
    }

    /**
     * Dátum+idő mező (flatpickr data-fp="datetime").
     *
     * @param array<string, mixed> $options
     */
    public function datetime(string $field, array $options = []): string
    {
        return $this->temporal($field, 'datetime', $options);
    }

    /**
     * Dátum megjelenítése (lista / view szöveg).
     */
    public function formatDate(mixed $value): string
    {
        return $this->formatTemporal($value, 'date');
    }

    /**
     * Idő megjelenítése.
     */
    public function formatTime(mixed $value): string
    {
        return $this->formatTemporal($value, 'time');
    }

    /**
     * Dátum+idő megjelenítése.
     */
    public function formatDatetime(mixed $value): string
    {
        return $this->formatTemporal($value, 'datetime');
    }

    /**
     * @param array<string, mixed> $options
     */
    protected function temporal(string $field, string $kind, array $options): string
    {
        $options += [
            'label' => false,
            'type' => 'text',
            'class' => 'form-control',
        ];
        $options['data-fp'] = $kind;

        if (!array_key_exists('value', $options)) {
            $raw = $this->Form->context()->val($field);
            if ($raw !== null && $raw !== '') {
                $formatted = $this->toSubmitValue($raw, $kind);
                if ($formatted !== null) {
                    $options['value'] = $formatted;
                }
            }
        } elseif ($options['value'] !== null && $options['value'] !== '') {
            $formatted = $this->toSubmitValue($options['value'], $kind);
            if ($formatted !== null) {
                $options['value'] = $formatted;
            }
        }

        return $this->control($field, $options);
    }

    /**
     * @param array<string, mixed> $options
     * @return array<string, mixed>
     */
    protected function withBareContainer(array $options): array
    {
        $bare = ['inputContainer' => '{{content}}'];
        if (isset($options['templates']) && is_array($options['templates'])) {
            $options['templates'] += $bare;
        } else {
            $options['templates'] = $bare;
        }

        return $options;
    }

    protected function formatTemporal(mixed $value, string $kind): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $dt = $this->toDateTimeInterface($value);
        if ($dt === null) {
            return h((string)$value);
        }

        $locale = $this->resolveLocale();
        $formats = $this->getConfig('displayFormats');
        $pack = $formats[$locale] ?? $formats['en'] ?? [];
        $pattern = (string)($pack[$kind] ?? $this->getConfig('submitFormats.' . $kind));

        return h($dt->format($pattern));
    }

    protected function toSubmitValue(mixed $value, string $kind): ?string
    {
        $dt = $this->toDateTimeInterface($value);
        if ($dt === null) {
            $str = trim((string)$value);

            return $str === '' ? null : $str;
        }

        $pattern = (string)$this->getConfig('submitFormats.' . $kind);

        return $dt->format($pattern);
    }

    protected function toDateTimeInterface(mixed $value): ?DateTimeInterface
    {
        if ($value instanceof DateTimeInterface) {
            return $value;
        }
        if (!is_string($value) && !is_numeric($value)) {
            return null;
        }

        $str = trim((string)$value);
        if ($str === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($str);
        } catch (\Exception) {
            return null;
        }
    }

    protected function resolveLocale(): string
    {
        $configured = $this->getConfig('locale');
        if (is_string($configured) && $configured !== '') {
            return strtolower(substr($configured, 0, 2));
        }

        return strtolower(substr((string)I18n::getLocale(), 0, 2));
    }
}
