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
        $this->paginate = [
            'order' => ['unique_string']
        ];
        
        $this->set('permission', $this->paginate($this->Permission));
        $this->set('_serialize', ['permission']);
    }

    /**
     * Sync method
     *
     * @return void
     */
    public function sync()
    {

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
        
        return $this->redirect(['action' => 'index']);
    }
        
}
