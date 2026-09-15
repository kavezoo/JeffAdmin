<?php
declare(strict_types=1);

namespace JeffAdmin\Controller;

use App\Controller\AppController as BaseController;
use Cake\Event\EventInterface;

class AppController extends BaseController
{
    public $session = '';
    public $prefix = '';
    public $controller = '';
    public $action = '';

    public function initialize(): void
    {
        parent::initialize();

        $this->session = $this->getRequest()->getSession() ?? null;
        $this->prefix = $this->request->getParam('prefix') ?? '';
        $this->controller = $this->request->getParam('controller') ?? '';
        $this->action = $this->request->getParam('action') ?? '';

        $this->set('session', $this->session);
        $this->set('prefix', $this->prefix);
        $this->set('controller', $this->controller);
        $this->set('action', $this->action);

        // Beállítja a /jeff_admin/templates/layout/default.php fájlt alapértelmezettnek
        $this->viewBuilder()->setLayout('KvAdmin.default');
    }

    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $action = (string)$this->request->getParam('action');

        // View / edit megnyitásakor: id + lista oldal (listPage / referer / session).
        if (in_array($action, ['view', 'edit'], true)) {
            $pass = $this->request->getParam('pass');
            $id = is_array($pass) && $pass !== [] ? $pass[0] : null;
            $this->rememberLastRecord($id);
        }

        if ($action !== 'index') {
            return;
        }

        // Indexre visszatérés Mégsem/Bezárás után: ?last=id (&listPage=N)
        $last = $this->request->getQuery('last');
        if ($last !== null && $last !== '') {
            $listPage = $this->request->getQuery('listPage');
            $this->rememberLastRecord(
                $last,
                $listPage !== null && $listPage !== '' ? (int)$listPage : null
            );
        }

        // Aktuális listaállapot mentése (page / sort / search), ha a queryben van.
        $this->syncListStateFromRequest();

        // Hiányzó page/sort/search visszaállítása a sessionből.
        $restore = $this->buildListStateRestoreQuery();
        if ($restore !== []) {
            $query = array_merge($this->request->getQueryParams(), $restore);
            unset($query['last'], $query['listPage']);
            $event->setResult($this->redirect([
                'action' => 'index',
                '?' => $query,
            ]));
        }
    }

    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);

        $state = $this->getLastRecordState();
        $this->set('listSearch', $state['search'] ?? '');
        $this->set('listSort', $state['sort'] ?? null);
        $this->set('listDirection', $state['direction'] ?? 'asc');

        if ((string)$this->request->getParam('action') === 'index') {
            $this->set('lastRecordId', $this->getLastRecordId());
            $this->set('lastRecordPage', $this->getLastRecordPage());
        }
    }

    /**
     * Utoljára érintett rekord mentése sessionbe: id + lista oldal (+ megőrzött sort/search).
     * Kulcs: JeffAdmin.lastId.{Prefix.}{Controller}
     * Az érték addig megmarad, amíg új id-t nem írunk — soha nem töröljük.
     */
    protected function rememberLastRecord(string|int|null $id, ?int $page = null): void
    {
        if ($id === null || $id === '') {
            return;
        }

        $state = $this->defaultListState($this->getLastRecordState());
        $state['id'] = (string)$id;
        $state['page'] = $this->resolveListPage($page);

        $this->getRequest()->getSession()->write(
            $this->lastRecordSessionKey(),
            $state
        );
    }

    /**
     * Index query → session (page, sort, direction, search). Meglévő id megmarad.
     */
    protected function syncListStateFromRequest(): void
    {
        $state = $this->defaultListState($this->getLastRecordState());
        $query = $this->request->getQueryParams();
        $changed = false;

        if (array_key_exists('page', $query) && (int)$query['page'] > 0) {
            $state['page'] = (int)$query['page'];
            $changed = true;
        }

        if (!empty($query['sort'])) {
            $state['sort'] = (string)$query['sort'];
            $dir = strtolower((string)($query['direction'] ?? 'asc'));
            $state['direction'] = $dir === 'desc' ? 'desc' : 'asc';
            $changed = true;
        }

        if (array_key_exists('search', $query)) {
            $state['search'] = trim((string)$query['search']);
            $changed = true;
        }

        if (!$changed && $state['id'] === null && $state['search'] === '' && $state['sort'] === null) {
            return;
        }

        $this->getRequest()->getSession()->write(
            $this->lastRecordSessionKey(),
            $state
        );
    }

    /**
     * Sessionből visszaállítandó query paraméterek (csak ami hiányzik a requestből).
     *
     * @return array<string, mixed>
     */
    protected function buildListStateRestoreQuery(): array
    {
        $state = $this->getLastRecordState();
        if ($state === null) {
            return [];
        }

        $restore = [];

        if ($this->request->getQuery('page') === null && (int)($state['page'] ?? 1) > 1) {
            $restore['page'] = (int)$state['page'];
        }

        if ($this->request->getQuery('sort') === null && !empty($state['sort'])) {
            $restore['sort'] = (string)$state['sort'];
            $restore['direction'] = (($state['direction'] ?? 'asc') === 'desc') ? 'desc' : 'asc';
        }

        if ($this->request->getQuery('search') === null && ($state['search'] ?? '') !== '') {
            $restore['search'] = (string)$state['search'];
        }

        return $restore;
    }

    /**
     * @param array{id: ?string, page: int, sort: ?string, direction: string, search: string}|null $state
     * @return array{id: ?string, page: int, sort: ?string, direction: string, search: string}
     */
    protected function defaultListState(?array $state): array
    {
        return [
            'id' => $state['id'] ?? null,
            'page' => max(1, (int)($state['page'] ?? 1)),
            'sort' => $state['sort'] ?? null,
            'direction' => (($state['direction'] ?? 'asc') === 'desc') ? 'desc' : 'asc',
            'search' => (string)($state['search'] ?? ''),
        ];
    }

    /**
     * Utoljára érintett rekord id (index kiemeléshez).
     */
    protected function getLastRecordId(): ?string
    {
        $state = $this->getLastRecordState();

        return $state['id'] ?? null;
    }

    /**
     * Utoljára érintett rekord lista-oldala.
     */
    protected function getLastRecordPage(): ?int
    {
        $state = $this->getLastRecordState();
        if ($state === null) {
            return null;
        }

        $page = (int)($state['page'] ?? 1);

        return $page > 0 ? $page : 1;
    }

    /**
     * @return array{id: ?string, page: int, sort: ?string, direction: string, search: string}|null
     */
    protected function getLastRecordState(): ?array
    {
        $stored = $this->getRequest()->getSession()->read($this->lastRecordSessionKey());
        if ($stored === null || $stored === '') {
            return null;
        }

        // Régi formátum: csak id string (vagy path.with.id)
        if (!is_array($stored)) {
            $id = $this->normalizeLastRecordId($stored);
            if ($id === null) {
                return null;
            }

            return $this->defaultListState(['id' => $id, 'page' => 1, 'sort' => null, 'direction' => 'asc', 'search' => '']);
        }

        $id = isset($stored['id']) ? $this->normalizeLastRecordId($stored['id']) : null;

        return $this->defaultListState([
            'id' => $id,
            'page' => (int)($stored['page'] ?? 1),
            'sort' => isset($stored['sort']) && $stored['sort'] !== '' ? (string)$stored['sort'] : null,
            'direction' => (string)($stored['direction'] ?? 'asc'),
            'search' => (string)($stored['search'] ?? ''),
        ]);
    }

    /**
     * Lista oldal meghatározása: explicit → ?listPage → referer ?page → session → 1.
     */
    protected function resolveListPage(?int $page = null): int
    {
        if ($page !== null && $page > 0) {
            return $page;
        }

        $fromQuery = $this->request->getQuery('listPage');
        if ($fromQuery !== null && $fromQuery !== '' && (int)$fromQuery > 0) {
            return (int)$fromQuery;
        }

        $referer = $this->request->getEnv('HTTP_REFERER');
        if (is_string($referer) && $referer !== '') {
            $queryString = parse_url($referer, PHP_URL_QUERY);
            if (is_string($queryString) && $queryString !== '') {
                parse_str($queryString, $params);
                if (!empty($params['page']) && (int)$params['page'] > 0) {
                    return (int)$params['page'];
                }
            }
        }

        $existing = $this->getLastRecordPage();
        if ($existing !== null && $existing > 0) {
            return $existing;
        }

        return 1;
    }

    /**
     * Csak az id — régi path formátum (Admin.Customers.view.12) esetén az utolsó szegmens.
     */
    protected function normalizeLastRecordId(mixed $stored): ?string
    {
        $value = trim((string)$stored);
        if ($value === '') {
            return null;
        }

        if (!str_contains($value, '.')) {
            return $value;
        }

        $parts = explode('.', $value);
        $id = end($parts);

        return $id !== false && $id !== '' ? (string)$id : null;
    }

    /**
     * Session kulcs prefix+controller szerint (Admin és nem-Admin ne ütközzön).
     */
    protected function lastRecordSessionKey(): string
    {
        $controller = $this->controller !== '' ? (string)$this->controller : $this->getName();
        $prefix = ($this->prefix !== '' && $this->prefix !== null)
            ? (string)$this->prefix . '.'
            : '';

        return 'JeffAdmin.lastId.' . $prefix . $controller;
    }
}
