<?php
namespace Acl\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;

/**
 * Acl component
 */
class AclComponent extends Component
{

    /**
     * Controllers and actions allowed for all users
     * @var array
     */
    private $_authorized = [
        'Permission' => ['index','add','remove','edit'],
        'UserGroupPermission' => ['index','add','remove','edit']
    ];

    /**
     * Checks whether the user or group is allowed access
     * @return bool
     */
    public function check()
    {
        if( isset($this->_authorized[$controller]) && in_array($action, $this->_authorized[$controller]) ) return true;

        $action = $this->request->param('action');
        $controller = $this->request->param('controller');
        $UserGroupPermission = TableRegistry::get('UserGroupPermission');
        $Permission = TableRegistry::get('Permission');

        $permission_id = $Permission->find()
            ->select(['id'])
            ->where(
                [
                    'action'        => $action,
                    'controller'    => $controller
                ]
            )->first();
        if( is_null($permission_id) ) return false;

        $allow = $UserGroupPermission->find()
            ->select(['allow'])
            ->where(
                [
                    'group_or_user'     => 'user',
                    'group_or_user_id'  => 1,
                    'permission_id'     => $permission_id->id
                ]
            )->first();
        if( is_null($allow) ) return false;

        return $allow->allow;
    }

    /**
     * Allows controllers and actions for all users
     * @param array $_authorized
     */
    public function allow(array $_authorized) {
        $this->_authorized = array_merge($this->_authorized, $_authorized);
    }

}
