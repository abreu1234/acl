<?php
namespace Acl\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\ORM\TableRegistry;
use ReflectionClass;
use ReflectionMethod;

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
        'Permission' => ['index','add','delete','edit','synchronize'],
        'UserGroupPermission' => ['index','add','delete','edit']
    ];

    /**
     * Checks whether the user or group is allowed access
     * @return bool
     */
    public function check()
    {
    	$action = $this->request->param('action');
        $controller = $this->request->param('controller');

        if( isset($this->_authorized[$controller]) && in_array($action, $this->_authorized[$controller]) ) return true;
        
        $user_id = $this->request->session()->read('Auth.User.id');
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
                    'group_or_user_id'  => $user_id,
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
    public function allow(array $_authorized) 
    {
        $this->_authorized = array_merge($this->_authorized, $_authorized);
    }
    
    /**
     * Synchronizes all controllers and existing actions to the database
     */
    public function synchronize() 
    {
        $Permission = TableRegistry::get('Permission');
        $permission_ids = [];
        $files = scandir('../src/Controller/');
        $ignore_list = [
            'controller' => ['.','..','Component','AppController.php'],
            'action' => ['beforeFilter', 'afterFilter', 'initialize']
        ];
                
        foreach($files as $file) {
            
            if(!in_array($file, $ignore_list['controller'])) {
                
                $controller_name = str_replace('Controller', '', explode('.', $file)[0]);
                $class_name = 'App\\Controller\\'.$controller_name.'Controller';
                $class = new ReflectionClass($class_name);
                $all_actions = $class->getMethods(ReflectionMethod::IS_PUBLIC);
                
                foreach($all_actions as $action) {
                    
                    if($action->class == $class_name && !in_array($action->name, $ignore_list['action'])) { 
                        
                        $permission_id = $Permission->find()
                            ->select(['id'])
                            ->where(['action' => $action->name, 'controller' => $controller_name])
                            ->first();
                        
                        if( is_null($permission_id) ) {
                            $new_permission = $Permission->newEntity();
                            $new_permission->action = $action->name;
                            $new_permission->controller = $controller_name;
                            
                            if( $Permission->save($new_permission) ) 
                                array_push($permission_ids, $new_permission->id);                           
                            
                        }else {
                            array_push($permission_ids, $permission_id->id);
                        }
                        
                    }                    
                }                
            }
        }
        
        $Permission->deleteAll(['id NOT IN'=>$permission_ids]);
    }

}
