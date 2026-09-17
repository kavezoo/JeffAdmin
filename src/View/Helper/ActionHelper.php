<?php
declare(strict_types=1);

namespace JeffAdmin\View\Helper;

use Cake\Datasource\EntityInterface;
use Cake\Utility\Inflector;
use Cake\View\Helper;

/**
 * Lista és űrlap műveleti gombok.
 *
 * Lista (ikon):
 *   <?= $this->Action->view($id) ?>
 *   <?= $this->Action->edit($id) ?>
 *   <?= $this->Action->delete($id, [], ['entity' => $row]) ?>
 *
 * Űrlap fejléc / lábléc:
 *   <?= $this->Action->close($id) ?>
 *   <?= $this->Action->editButton($id) ?>
 *   <?= $this->Action->cancelButton($id) ?>
 *   <?= $this->Action->deleteButton($id, [], ['entity' => $entity]) ?>
 *
 * A view/edit URL-hez automatikusan hozzáadja a ?listPage=… értéket
 * (aktuális lista oldal), hogy visszatéréskor meglegyen a pagináció.
 * A close() / cancelButton() az indexre visz (?last= + ?listPage=).
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \JeffAdmin\View\Helper\IconHelper $Icon
 */
class ActionHelper extends Helper
{
    protected array $helpers = ['Html', 'Form', 'JeffAdmin.Icon'];

    /**
     * Törölhető-e a rekord a *_count mezők alapján (mind nullával / hiányzik).
     */
    public function canDeleteByCounts(?EntityInterface $entity): bool
    {
        if ($entity === null) {
            return true;
        }

        foreach ($entity->toArray() as $field => $value) {
            if (!is_string($field) || !str_ends_with($field, '_count')) {
                continue;
            }
            if ((int)$value > 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * Megtekintés gomb (lista).
     *
     * @param string|int $id Rekord id
     * @param array<string, mixed> $urlOptions Extra URL opciók (pl. controller)
     */
    public function view(string|int $id, array $urlOptions = []): string
    {
        return $this->Html->link(
            $this->Icon->outline('eye'),
            $this->urlWithListPage(['action' => 'view', $id], $urlOptions),
            [
                'class' => 'item',
                'escape' => false,
                'data-bs-toggle' => 'tooltip',
                'data-bs-title' => __('View'),
            ]
        );
    }

    /**
     * Szerkesztés gomb (lista, csak ikon).
     *
     * @param string|int $id Rekord id
     * @param array<string, mixed> $urlOptions Extra URL opciók (pl. controller)
     */
    public function edit(string|int $id, array $urlOptions = []): string
    {
        return $this->Html->link(
            $this->Icon->outline('edit'),
            $this->urlWithListPage(['action' => 'edit', $id], $urlOptions),
            [
                'class' => 'item',
                'escape' => false,
                'data-bs-toggle' => 'tooltip',
                'data-bs-title' => __('Edit'),
            ]
        );
    }

    /**
     * Szerkesztés gomb (view lábléc — ikon + felirat, btn-primary).
     *
     *   <?= $this->Action->editButton($customer->id) ?>
     *
     * @param string|int $id Rekord id
     * @param array<string, mixed> $urlOptions Extra URL opciók (pl. controller)
     */
    public function editButton(string|int $id, array $urlOptions = []): string
    {
        return $this->Html->link(
            $this->Icon->outline('edit') . ' ' . __('Edit'),
            $this->urlWithListPage(['action' => 'edit', $id], $urlOptions),
            [
                'class' => 'btn btn-primary',
                'escape' => false,
            ]
        );
    }

    /**
     * Törlés gomb (lista, ikon) — postLink + SweetAlert confirm.
     *
     * Options:
     * - disabled (bool)
     * - entity (EntityInterface) — *_count > 0 esetén disabled (ha checkCounts nem false)
     * - checkCounts (bool) — alap true; false = ne tiltson *_count miatt
     * - disabledTitle (string) — tooltip felülírás
     *
     * @param string|int $id Rekord id
     * @param array<string, mixed> $urlOptions Extra URL opciók (pl. controller)
     * @param array<string, mixed> $options
     */
    public function delete(string|int $id, array $urlOptions = [], array $options = []): string
    {
        $disabled = $this->isDeleteDisabled($options);
        $disabledTitle = $this->disabledDeleteTitle($options);

        if ($disabled) {
            return $this->Html->tag(
                'span',
                $this->Icon->outline('trash'),
                [
                    'class' => 'item delete is-disabled',
                    'escape' => false,
                    'aria-disabled' => 'true',
                    'tabindex' => '0',
                    'role' => 'button',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $disabledTitle,
                ]
            );
        }

        return $this->Form->postLink(
            $this->Icon->outline('trash', 'text-danger'),
            $urlOptions + ['action' => 'delete', $id],
            $this->deleteConfirmOptions('item delete', $id)
        );
    }

    /**
     * Törlés gomb (view/edit lábléc — ikon + felirat, btn-danger).
     *
     *   <?= $this->Action->deleteButton($customer->id, [], ['entity' => $customer]) ?>
     *
     * Disabled gomb: wrapper span kapja a Bootstrap tooltipet (disabled buttonön nem jelenik meg).
     *
     * @param string|int $id Rekord id
     * @param array<string, mixed> $urlOptions Extra URL opciók (pl. controller)
     * @param array<string, mixed> $options disabled / entity / checkCounts / disabledTitle
     */
    public function deleteButton(string|int $id, array $urlOptions = [], array $options = []): string
    {
        $disabled = $this->isDeleteDisabled($options);
        $disabledTitle = $this->disabledDeleteTitle($options);
        $label = $this->Icon->outline('trash') . ' ' . __('Delete');

        if ($disabled) {
            $button = $this->Html->tag(
                'button',
                $label,
                [
                    'type' => 'button',
                    'class' => 'btn btn-danger',
                    'disabled' => true,
                    'escape' => false,
                    'aria-label' => $disabledTitle,
                    'tabindex' => '-1',
                ]
            );

            return $this->Html->tag(
                'span',
                $button,
                [
                    'class' => 'd-inline-block',
                    'escape' => false,
                    'tabindex' => '0',
                    'data-bs-toggle' => 'tooltip',
                    'data-bs-placement' => 'top',
                    'data-bs-title' => $disabledTitle,
                ]
            );
        }

        return $this->Form->postLink(
            $label,
            $urlOptions + ['action' => 'delete', $id],
            $this->deleteConfirmOptions('btn btn-danger', $id)
        );
    }

    /**
     * Form card fejléc bezárás (X) — vissza az indexre, opcionálisan last + listPage.
     *
     *   <?= $this->Action->close($customer->id) ?>
     *   <?= $this->Action->close() ?>
     *
     * @param string|int|null $id Rekord id (last jelöléshez); null = sima index
     * @param array<string, mixed> $urlOptions Extra URL opciók (pl. controller)
     */
    public function close(string|int|null $id = null, array $urlOptions = []): string
    {
        return $this->Html->link(
            $this->Icon->outline('x'),
            $this->indexReturnUrl($id, $urlOptions),
            [
                'class' => 'm-btn m-btn--ghost form-card-header__close',
                'escape' => false,
                'style' => 'padding: 0 10px;',
                'aria-label' => __('Close'),
                'data-bs-toggle' => 'tooltip',
                'data-bs-title' => __('Close'),
            ]
        );
    }

    /**
     * Mégsem gomb (űrlap lábléc — ikon + felirat, btn-secondary).
     *
     *   <?= $this->Action->cancelButton($customer->id) ?>
     *   <?= $this->Action->cancelButton() ?>
     *
     * @param string|int|null $id Rekord id (last jelöléshez); null = sima index
     * @param array<string, mixed> $urlOptions Extra URL opciók (pl. controller)
     */
    public function cancelButton(string|int|null $id = null, array $urlOptions = []): string
    {
        return $this->Html->link(
            $this->Icon->outline('x') . ' ' . __('Cancel'),
            $this->indexReturnUrl($id, $urlOptions),
            [
                'class' => 'btn btn-secondary',
                'escape' => false,
            ]
        );
    }

    /**
     * @param array<string, mixed> $options
     *   disabled (bool), entity (EntityInterface), checkCounts (bool, alap: true),
     *   disabledTitle (string)
     */
    protected function isDeleteDisabled(array $options): bool
    {
        if (!empty($options['disabled'])) {
            return true;
        }

        if (array_key_exists('checkCounts', $options) && $options['checkCounts'] === false) {
            return false;
        }

        $entity = $options['entity'] ?? null;
        if ($entity instanceof EntityInterface) {
            return !$this->canDeleteByCounts($entity);
        }

        return false;
    }

    /**
     * Tooltip szöveg disabled törléshez (*_count mezők nevével, ha van entity).
     *
     * @param array<string, mixed> $options
     */
    protected function disabledDeleteTitle(array $options): string
    {
        if (!empty($options['disabledTitle']) && is_string($options['disabledTitle'])) {
            return $options['disabledTitle'];
        }

        $entity = $options['entity'] ?? null;
        $labels = [];
        if ($entity instanceof EntityInterface) {
            foreach ($entity->toArray() as $field => $value) {
                if (!is_string($field) || !str_ends_with($field, '_count') || (int)$value <= 0) {
                    continue;
                }
                $labels[] = Inflector::pluralize(Inflector::humanize(substr($field, 0, -6)));
            }
        }

        if ($labels === []) {
            return __('Cannot delete: related records exist');
        }

        return __('Cannot delete: related records exist ({0})', implode(', ', $labels));
    }

    /**
     * @return array<string, mixed>
     */
    protected function deleteConfirmOptions(string $class, string|int $id): array
    {
        return [
            'method' => 'delete',
            'confirm' => __('Are you sure you want to delete # {0}?', $id),
            'class' => $class,
            'escape' => false,
            'data-bs-toggle' => 'tooltip',
            'data-bs-title' => __('Delete'),
            'data-swal-title' => __('Are you sure?'),
            'data-swal-text' => __('This action cannot be undone.'),
            'data-swal-confirm' => __('Yes, delete it!'),
            'data-swal-cancel' => __('Cancel'),
        ];
    }

    /**
     * Index URL last + listPage queryvel.
     *
     * @param string|int|null $id
     * @param array<string, mixed> $urlOptions
     * @return array<string, mixed>
     */
    protected function indexReturnUrl(string|int|null $id, array $urlOptions): array
    {
        $query = array_filter([
            'last' => $id,
            'listPage' => $this->getView()->getRequest()->getQuery('listPage'),
        ], static fn($v) => $v !== null && $v !== '');

        $url = $urlOptions + ['action' => 'index'];
        if ($query !== []) {
            $url['?'] = isset($url['?']) && is_array($url['?'])
                ? $query + $url['?']
                : $query;
        }

        return $url;
    }

    /**
     * @param array<string, mixed> $baseUrl
     * @param array<string, mixed> $urlOptions
     * @return array<string, mixed>
     */
    protected function urlWithListPage(array $baseUrl, array $urlOptions): array
    {
        $url = $urlOptions + $baseUrl;
        $listPage = max(1, (int)$this->getView()->getRequest()->getQuery('page', 1));
        $fromListPage = $this->getView()->getRequest()->getQuery('listPage');
        if ($fromListPage !== null && $fromListPage !== '' && (int)$fromListPage > 0) {
            $listPage = (int)$fromListPage;
        }
        $query = ['listPage' => $listPage];
        if (isset($url['?']) && is_array($url['?'])) {
            $query += $url['?'];
        }
        $url['?'] = $query;

        return $url;
    }
}
