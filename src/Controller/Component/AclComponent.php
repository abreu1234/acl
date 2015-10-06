<?php
namespace Acl\Controller\Component;

use Cake\Controller\Component;
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
     * 
     * @var array Prefix => Controller => Action
     */
    private $_authorized = [
        '' => [
            'Permission' => ['index','sync','synchronize'],
            'UserGroupPermission' => ['index','add','delete','edit']
        ]
    ];
    
    /**
     * Controllers and actions ignored during synchronization
     * 
     * @var Array Prefix => Controller => Action
     */
    private $_sync_ignore_list = [
        '*' => [
            '.','..','Component','AppController.php',
            '*'  => ['beforeFilter', 'afterFilter', 'initialize']
        ]
    ];

    public function initialize( array $config )
    {
        if( isset($config['authorize']) )
            $this->_authorized = array_merge_recursive($this->_authorized, $config['authorize']);
        
        if( isset($config['ignore']) )
            $this->_sync_ignore_list = array_merge_recursive($this->_sync_ignore_list, $config['ignore']);
    }

    /**
     * Checks whether the user or group is allowed access
     * @return bool
     */
    public function check()
    {
    	$action = $this->request->param('action');
        $controller = $this->request->param('controller');
        $prefix = ($this->request->param('prefix') != false ) ? $this->request->param('prefix') : '';
        
        if( isset($this->_authorized[$prefix][$controller]) &&
            in_array($action, $this->_authorized[$prefix][$controller]) ) return true;
        
        $user_id = $this->request->session()->read('Auth.User.id');
        $UserGroupPermission = TableRegistry::get('UserGroupPermission');
        $Permission = TableRegistry::get('Permission');

        $unique_string = $prefix . '/' . $controller . '->' . $action;
        $permission_id = $Permission->find()
            ->select(['id'])
            ->where(['unique_string' => $unique_string])->first();
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
     * Synchronizes all controllers and existing actions to the database
     * @param String $prefix
     */
    public function synchronize($prefix = '')
    {
        $controllers_path = '../src/Controller/'.$prefix;
        $class_path = 'App\\Controller\\';
        $class_path .= empty($prefix) ? '' : $prefix.'\\';
        $Permission = TableRegistry::get('Permission');
        $permission_ids = [];
        $files = scandir($controllers_path);
        
        foreach($files as $file) {

            if(in_array($file, $this->_sync_ignore_list['*']) ||
                ( isset($this->_sync_ignore_list[$prefix]) && in_array($file, $this->_sync_ignore_list[$prefix]) )
            ) continue;

            if( is_dir($controllers_path.$file) && empty($prefix) ) {
                $this->synchronize($file);
                continue;
            }

            $controller_name = str_replace('Controller', '', explode('.', $file)[0]);
            $class_name = $class_path.$controller_name.'Controller';
            $class = new ReflectionClass($class_name);
            $all_actions = $class->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach($all_actions as $action) {

                if($action->class != $class_name || in_array($action->name, $this->_sync_ignore_list['*']['*']) ||
                    ( isset($this->_sync_ignore_list['*'][$controller_name]) && in_array($action->name, $this->_sync_ignore_list['*'][$controller_name]) ) ||                      
                    ( isset($this->_sync_ignore_list[$prefix]['*']) && in_array($action->name, $this->_sync_ignore_list[$prefix]['*']) ) ||                      
                    ( isset($this->_sync_ignore_list[$prefix][$controller_name]) && in_array($action->name, $this->_sync_ignore_list[$prefix][$controller_name]) )
                ) continue;

                $unique_string = $prefix . '/' . $controller_name . '->' . $action->name;
                $permission_id = $Permission->find()
                    ->select(['id'])
                    ->where(['unique_string' => $unique_string])
                    ->first();

                if (is_null($permission_id)) {
                    $new_permission = $Permission->newEntity();
                    $new_permission->action = $action->name;
                    $new_permission->controller = $controller_name;
                    $new_permission->prefix = $prefix;
                    $new_permission->unique_string = $unique_string;

                    if ($Permission->save($new_permission))
                        array_push($permission_ids, $new_permission->id);

                } else {
                    array_push($permission_ids, $permission_id->id);
                }

            }
        }
        
        $Permission->deleteAll(['id NOT IN'=>$permission_ids, 'prefix'=>$prefix]);
    }

}
