<?php
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
            $group_or_user_id = explode("-",$this->request->data['group_or_user_id']);
            $userGroupPermission->group_or_user = isset($group_or_user_id[0]) ? $group_or_user_id[0] : null;
            $userGroupPermission->group_or_user_id = isset($group_or_user_id[1]) ? $group_or_user_id[1] : null;

            if ($this->UserGroupPermission->save($userGroupPermission)) {
                $this->Flash->success(__('The user group permission has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else if(isset($userGroupPermission->errors('permission_id')['_isUnique'])) {

                $this->UserGroupPermission->updateAll(
                    ['allow' => $userGroupPermission->allow],
                    [
                        'group_or_user' => $userGroupPermission->group_or_user,
                        'group_or_user_id' => $userGroupPermission->group_or_user_id,
                        'permission_id' => $userGroupPermission->permission_id
                    ]
                );
                $this->Flash->success(__('The user group permission has been saved.'));
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error(__('The user group permission could not be saved. Please, try again.'));
            }
        }
        $Users = TableRegistry::get('Users');
        $users = $Users->find()->select(['id','email'])->toArray();

        $permission = $this->UserGroupPermission->Permission->find()->select(['id','unique_string'])->toArray();
        $this->set(compact('userGroupPermission', 'permission', 'users'));
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

    /**
     * Get all permissions by ajax
     */
    public function getPermission()
    {
        if(!$this->request->is('ajax'))
            return $this->redirect('/');

        $group_or_user_id = isset($this->request->data['group_or_user_id']) ? explode("-",$this->request->data['group_or_user_id']) : '';
        $group_or_user = isset($group_or_user_id[0]) ? $group_or_user_id[0] : null;
        $group_or_user_id = isset($group_or_user_id[1]) ? $group_or_user_id[1] : null;

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
        $this->render('Acl.UserGroupPermission/ajax_response', false);
    }

}
