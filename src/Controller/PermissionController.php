<?php
namespace Acl\Controller;

use Acl\Controller\AppController;

/**
 * Permission Controller
 *
 * @property \Acl\Model\Table\PermissionTable $Permission
 */
class PermissionController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->set('permission', $this->paginate($this->Permission));
        $this->set('_serialize', ['permission']);
    }

    /**
     * View method
     *
     * @param string|null $id Permission id.
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function view($id = null)
    {
        $permission = $this->Permission->get($id, [
            'contain' => []
        ]);
        $this->set('permission', $permission);
        $this->set('_serialize', ['permission']);
    }

    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $permission = $this->Permission->newEntity();
        if ($this->request->is('post')) {
            $permission = $this->Permission->patchEntity($permission, $this->request->data);
            if ($this->Permission->save($permission)) {
                $this->Flash->success(__('The permission has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The permission could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('permission'));
        $this->set('_serialize', ['permission']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Permission id.
     * @return void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $permission = $this->Permission->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $permission = $this->Permission->patchEntity($permission, $this->request->data);
            if ($this->Permission->save($permission)) {
                $this->Flash->success(__('The permission has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The permission could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('permission'));
        $this->set('_serialize', ['permission']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Permission id.
     * @return void Redirects to index.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $permission = $this->Permission->get($id);
        if ($this->Permission->delete($permission)) {
            $this->Flash->success(__('The permission has been deleted.'));
        } else {
            $this->Flash->error(__('The permission could not be deleted. Please, try again.'));
        }
        return $this->redirect(['action' => 'index']);
    }
    
    /**
     * Synchronizes all controllers and existing actions to the database
     * 
     * @return void Redirects to add.
     */
    public function synchronize() 
    {
        $this->request->allowMethod(['post', 'synchronize']);
                
        $this->Acl->synchronize();
        $this->Flash->success(__('Synchronized successfully!'));
        
        return $this->redirect(['action' => 'add']);
    }
        
}
