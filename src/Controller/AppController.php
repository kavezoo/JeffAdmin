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

        // View / edit megnyitásakor megjegyezzük az utoljára érintett rekordot.
        $action = (string)$this->request->getParam('action');
        if (in_array($action, ['view', 'edit'], true)) {
            $pass = $this->request->getParam('pass');
            $id = is_array($pass) && $pass !== [] ? $pass[0] : null;
            $this->rememberLastRecord($id);
        }
    }

    public function beforeRender(EventInterface $event): void
    {
        parent::beforeRender($event);

        if ((string)$this->request->getParam('action') === 'index') {
            $this->set('lastRecordId', $this->getLastRecordId());
        }
    }

    /**
     * Utoljára érintett rekord id mentése (controllerenként, sessionben).
     */
    protected function rememberLastRecord(string|int|null $id): void
    {
        if ($id === null || $id === '') {
            return;
        }

        $this->getRequest()->getSession()->write($this->lastRecordSessionKey(), (string)$id);
    }

    /**
     * Utoljára érintett rekord törlése a sessionből (pl. sikeres delete után).
     */
    protected function forgetLastRecord(string|int|null $id = null): void
    {
        $key = $this->lastRecordSessionKey();
        $session = $this->getRequest()->getSession();

        if ($id === null || (string)$session->read($key) === (string)$id) {
            $session->delete($key);
        }
    }

    protected function getLastRecordId(): ?string
    {
        $id = $this->getRequest()->getSession()->read($this->lastRecordSessionKey());

        return $id !== null && $id !== '' ? (string)$id : null;
    }

    protected function lastRecordSessionKey(): string
    {
        return 'JeffAdmin.lastId.' . $this->getName();
    }
}
