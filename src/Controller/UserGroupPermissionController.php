<?php
namespace Acl\Controller;

use Acl\Controller\AppController;

/**
 * UserGroupPermission Controller
 *
 * @property \Acl\Model\Table\UserGroupPermissionTable $UserGroupPermission
 */
class UserGroupPermissionController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Permission']
        ];
        $this->set('userGroupPermission', $this->paginate($this->UserGroupPermission));
        $this->set('_serialize', ['userGroupPermission']);
    }

    /**
     * View method
     *
     * @param string|null $id User Group Permission id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $userGroupPermission = $this->UserGroupPermission->get($id, [
            'contain' => ['Permission']
        ]);
        $this->set('userGroupPermission', $userGroupPermission);
        $this->set('_serialize', ['userGroupPermission']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $userGroupPermission = $this->UserGroupPermission->newEntity();
        if ($this->request->is('post')) {
            $userGroupPermission = $this->UserGroupPermission->patchEntity($userGroupPermission, $this->request->data);
            if ($this->UserGroupPermission->save($userGroupPermission)) {
                $this->Flash->success(__('The user group permission has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user group permission could not be saved. Please, try again.'));
            }
        }
        $permission = $this->UserGroupPermission->Permission->find('list', ['limit' => 200]);
        $this->set(compact('userGroupPermission', 'permission'));
        $this->set('_serialize', ['userGroupPermission']);
    }

    /**
     * Edit method
     *
     * @param string|null $id User Group Permission id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $userGroupPermission = $this->UserGroupPermission->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $userGroupPermission = $this->UserGroupPermission->patchEntity($userGroupPermission, $this->request->data);
            if ($this->UserGroupPermission->save($userGroupPermission)) {
                $this->Flash->success(__('The user group permission has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user group permission could not be saved. Please, try again.'));
            }
        }
        $permission = $this->UserGroupPermission->Permission->find('list', ['limit' => 200]);
        $this->set(compact('userGroupPermission', 'permission'));
        $this->set('_serialize', ['userGroupPermission']);
    }

    /**
     * Delete method
     *
     * @param string|null $id User Group Permission id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $userGroupPermission = $this->UserGroupPermission->get($id);
        if ($this->UserGroupPermission->delete($userGroupPermission)) {
            $this->Flash->success(__('The user group permission has been deleted.'));
        } else {
            $this->Flash->error(__('The user group permission could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }

}
