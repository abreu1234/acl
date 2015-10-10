<?php
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
namespace Acl\Controller;

use Acl\Controller\AppController;
use Cake\ORM\TableRegistry;

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
        $userGroupPermission = $this->UserGroupPermission->newEntity();
        
        $controllers = $this->Acl->getControllers();
        $groups = $users = [];
        
        if( !empty($controllers['user']) ) {
            $Users = TableRegistry::get($controllers['user']);
            $users = $Users->find()->select(['id','email'])->toArray();
        }
        if( !empty($controllers['group']) ) {
            $groups = TableRegistry::get($controllers['group']);
            $groups = $groups->find()->select(['id','name'])->toArray();
        }

        $permission = $this->UserGroupPermission->Permission->find()->select(['id','unique_string'])
            ->order(['unique_string'=>'DESC'])->toArray();
        $this->set(compact('userGroupPermission', 'permission', 'users', 'groups'));
        $this->set('_serialize', ['userGroupPermission']);
    }
    
    /**
     * Add method
     *
     * @return void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        if ($this->request->is('post')) {
            
            $userGroupPermission = $this->UserGroupPermission->findUserGroupPermission(
                    ['id'], 
                    $this->request->data['group_or_user_id'], 
                    $this->request->data['group_or_user'], 
                    $this->request->data['permission_id']
                )->first();
            
            if(!is_null($userGroupPermission)) {
                $this->UserGroupPermission->get($userGroupPermission->id);
                $userGroupPermission->allow = $this->request->data['allow'];
            }else {
                $userGroupPermission = $this->UserGroupPermission->patchEntity($userGroupPermission, $this->request->data);
            }

            if ($this->UserGroupPermission->save($userGroupPermission)) {
                $this->Flash->success(__('The user group permission has been saved.'));
            } else {
                $this->Flash->error(__('The user group permission could not be saved. Please, try again.'));
            }
        }
        return $this->redirect(['action' => 'index']);
    }
    
    /**
     * Add by ajax
     */
    public function addAjax() 
    {
        $this->request->allowMethod(['ajax']);
        $data['group_or_user'] = $this->request->data['group_or_user'];
        $data['group_or_user_id'] = $this->request->data['group_or_user_id'];
        $permissions = json_decode($this->request->data['permissions']);
        
        foreach($permissions as $permission) {
            $data['permission_id'] = $permission->id;
            $data['allow'] = $permission->allow;
            
            $userGroupPermission = $this->UserGroupPermission->findUserGroupPermission(
                    ['id'], $data['group_or_user_id'], $data['group_or_user'], $data['permission_id']
                )->first();
            
            if(!is_null($userGroupPermission)) {
                $this->UserGroupPermission->get($userGroupPermission->id);
                $userGroupPermission->allow = $data['allow'];
            }else {
                $userGroupPermission = $this->UserGroupPermission->newEntity();
                $userGroupPermission = $this->UserGroupPermission->patchEntity($userGroupPermission, $data);
            }
            
            $this->UserGroupPermission->save($userGroupPermission);
        }
        
        $this->Flash->success(__('The user group permission has been updated.'));
        
        $this->set(compact('response'));
        $this->set('_serialize', ['response']);
        $this->render('Acl.Ajax/ajax_response', false);
    }

    /**
     * Get all permissions by ajax
     */
    public function getPermission()
    {
        $this->request->allowMethod(['ajax']);

        $group_or_user = isset($this->request->data['group_or_user']) ? $this->request->data['group_or_user'] : null;
        $group_or_user_id = isset($this->request->data['group_or_user_id']) ? $this->request->data['group_or_user_id'] : null;

        $ugp = $this->UserGroupPermission->find()
            ->select(['permission_id','allow'])
            ->where(
                [
                    'group_or_user_id' => $group_or_user_id,
                    'group_or_user' => $group_or_user
                ])->toArray();

        $response = (!empty($ugp) && !is_null($ugp)) ? $ugp : 'fail';

        $this->set(compact('response'));
        $this->set('_serialize', ['response']);
        $this->render('Acl.Ajax/ajax_response', false);
    }

}
