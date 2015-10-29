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
namespace Acl\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;
use ReflectionClass;
use ReflectionMethod;
use Cake\Core\App;

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
    private $_authorized = [];

    /**
     * Controllers and actions ignored during synchronization
     *
     * @var Array Prefix => Controller => Action
     */
    private $_sync_ignore_list = [
        '*' => [
            '.','..','Component','AppController.php','empty','Controller.php',
            '*'  => ['beforeFilter','afterFilter','initialize']
        ],
    ];

    private $_plugins = ['Acl'];

    /**
     * Controllers groups and users
     *
     * @var Array
     */
    private $controllers = ['group'=>'','user'=>''];

    private $all_files = [];

    public function initialize( array $config )
    {
        if( isset($config['controllers']) )
            $this->controllers = array_merge($this->controllers, $config['controllers']);
        if( empty($this->controllers['user']) )
            die('Acl: Controller user not set');

        if( isset($config['authorize']) )
            $this->_authorized = array_merge_recursive($this->_authorized, $config['authorize']);

        if( isset($config['ignore']) )
            $this->_sync_ignore_list = array_merge_recursive($this->_sync_ignore_list, $config['ignore']);

        if( isset($config['plugins']) )
            $this->_plugins = array_merge($this->_plugins, $config['plugins']);
    }

    /**
     * Checks whether the user or group is allowed access
     * @return bool
     */
    public function check()
    {
    	$action = $this->request->param('action');
        $controller = $this->request->param('controller');
        $prefix = ($this->request->param('prefix') === false) ? '/' : $this->request->param('prefix');
        $plugin = ($this->request->param('plugin') === false) ? '' : $this->request->param('plugin');

        $plugin_prefix = (empty($plugin)) ? $prefix : $plugin . '.' . $prefix;
        if( isset($this->_authorized[$plugin_prefix][$controller]) &&
            in_array($action, $this->_authorized[$plugin_prefix][$controller]) ) return true;

        $user_id = $this->request->session()->read('Auth.User.id');
        $group_id = -1;
        if( isset($this->controllers['group']) ) {
            $User = TableRegistry::get($this->controllers['user']);
            $group_id = $User->get($user_id)->group_id;
        }

        $UserGroupPermission = TableRegistry::get('UserGroupPermission');
        $Permission = TableRegistry::get('Permission');

        $unique_string = $this->getUniqueString($controller, $action, $prefix, $plugin);
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

                ])
            ->orWhere(
                [
                    'group_or_user'     => 'group',
                    'group_or_user_id'  => $group_id
                ])->andWhere(['permission_id' => $permission_id->id])
            ->order(['allow'=>'DESC'])->first();
        if( is_null($allow) ) return false;

        return $allow->allow;
    }

    /**
     * Synchronizes all controllers and existing actions to the database
     *
     * @param boolean/String $prefix
     * @param boolean/String $plugin
     * @param array $permission_ids
     * @return array $permission_ids
     */
    public function synchronize( $prefix = false, $plugin = false, array $permission_ids = [] )
    {
        $classname = '';
        if( !$plugin ) {
            $path = App::path('Controller/'.$prefix)[0];
        } else if( $plugin && is_string($prefix) ) {
            $path = App::path('Controller/' . $prefix, $plugin)[0];
            $classname = $plugin . '.';
        }

        $type_prefix = ( $prefix === '/' || is_bool($prefix) ) ? '' : '/'.$prefix;
        $Permission = TableRegistry::get('Permission');
        $files = scandir($path);
        $this->all_files = array_merge($this->all_files, $files);
        foreach($files as $file) {

            if(in_array($file, $this->_sync_ignore_list['*']) ||
                ( isset($this->_sync_ignore_list[$prefix]) && in_array($file, $this->_sync_ignore_list[$prefix]) )
            ) continue;

            if( is_dir($path.$file) ) {
                if( $prefix || !$plugin )
                    $permission_ids = $this->synchronize($file, $plugin, $permission_ids);
                else if( $plugin )
                    $permission_ids = $this->synchronize('/', $file, $permission_ids);
                continue;
            }

            $controller_name = str_replace('Controller', '', explode('.', $file)[0]);
            $class_name = App::classname($classname.$controller_name, 'Controller'.$type_prefix, 'Controller');
            if( empty($class_name) ) continue;
            $class = new ReflectionClass($class_name);
            $all_actions = $class->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach($all_actions as $action) {
                $permission_prefix = '';
                if( $plugin )
                    $permission_prefix .= $plugin.'.';
                if( $prefix )
                    $permission_prefix .= $prefix;

                $class = new ReflectionClass($action->class);
                $file_name = explode('/',$class->getFileName());
                $file_name = end($file_name);
                if( in_array($file_name, $this->_sync_ignore_list['*']) || ($action->class != $class_name && !in_array($file_name, $this->all_files)) ||
                    in_array($action->name, $this->_sync_ignore_list['*']['*']) ||
                    ( isset($this->_sync_ignore_list['*'][$controller_name]) && in_array($action->name, $this->_sync_ignore_list['*'][$controller_name]) ) ||
                    ( isset($this->_sync_ignore_list[$permission_prefix]['*']) && in_array($action->name, $this->_sync_ignore_list[$permission_prefix]['*']) ) ||
                    ( isset($this->_sync_ignore_list[$permission_prefix][$controller_name]) && in_array($action->name, $this->_sync_ignore_list[$permission_prefix][$controller_name]) )
                ) continue;

                $unique_string = $this->getUniqueString($controller_name, $action->name, $prefix, $plugin);
                $permission_id = $Permission->find()
                    ->select(['id'])
                    ->where(['unique_string' => $unique_string])
                    ->first();

                if (is_null($permission_id)) {
                    $new_permission = $Permission->newEntity();
                    $new_permission->action = $action->name;
                    $new_permission->controller = $controller_name;
                    $new_permission->prefix = $permission_prefix;
                    $new_permission->unique_string = $unique_string;

                    if ($Permission->save($new_permission))
                        array_push($permission_ids, $new_permission->id);

                } else {
                    array_push($permission_ids, $permission_id->id);
                }
            }
        }

        if( !$plugin && !$prefix ) {
            foreach( $this->_plugins as $plugin )
                $permission_ids = $this->synchronize('/', $plugin, $permission_ids);

            $Permission->deleteAll(['id NOT IN'=>$permission_ids]);
        }

        return $permission_ids;
    }

    /**
     * Create unique string acl
     *
     * @param $controller
     * @param $action
     * @param bool/String $prefix
     * @param bool/String $plugin
     * @return string
     */
    private function getUniqueString( $controller, $action, $prefix = false, $plugin = false ) {
        $unique_string = '';
        if( $plugin && !empty($plugin) )
            $unique_string .= $plugin.'.';
        if( $prefix && $prefix != '/' )
            $unique_string .= $prefix;

        return $unique_string . '/' . $controller . '->' . $action;
    }

    public function getControllers()
    {
        return $this->controllers;
    }

}
