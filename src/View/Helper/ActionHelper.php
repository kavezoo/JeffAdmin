<?php
declare(strict_types=1);

namespace JeffAdmin\View\Helper;

use Cake\View\Helper;

/**
 * Lista és űrlap műveleti gombok.
 *
 * Lista (ikon):
 *   <?= $this->Action->view($id) ?>
 *   <?= $this->Action->edit($id) ?>
 *   <?= $this->Action->delete($id) ?>
 *
 * Űrlap fejléc / lábléc:
 *   <?= $this->Action->close($id) ?>
 *   <?= $this->Action->editButton($id) ?>
 *   <?= $this->Action->cancelButton($id) ?>
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
     * Törlés gomb (postLink + SweetAlert confirm).
     *
     * @param string|int $id Rekord id
     * @param array<string, mixed> $urlOptions Extra URL opciók (pl. controller)
     */
    public function delete(string|int $id, array $urlOptions = []): string
    {
        return $this->Form->postLink(
            $this->Icon->outline('trash', 'text-danger'),
            $urlOptions + ['action' => 'delete', $id],
            [
                'method' => 'delete',
                'confirm' => __('Are you sure you want to delete # {0}?', $id),
                'class' => 'item delete',
                'escape' => false,
                'data-bs-toggle' => 'tooltip',
                'data-bs-title' => __('Delete'),
            ]
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
