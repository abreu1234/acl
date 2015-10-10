<?php
namespace Acl\Controller;
/**
 * Copyright (c) Rafael Abreu
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://www.rafaelabreu.eti.br CakePHP(tm) Project
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
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
            'order' => ['unique_string'=>'DESC']
        ];
        
        $this->set('permission', $this->paginate($this->Permission));
        $this->set('_serialize', ['permission']);
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
