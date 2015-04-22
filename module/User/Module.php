<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace User;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Router\RouteMatch;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Zend\Session\Container;
use Common\Constants\Constants;

class Module implements AutoloaderProviderInterface
{
	
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $auth = $sm->get('AuthService');
       
        if ($auth->hasIdentity()) { 
            $viewModel = $app->getMvcEvent()->getViewModel();
            $userSession = new Container('user');
           // echo $userSession->type; die;
            $viewModel->userSession = $userSession;
            if(Constants::EMPLOYEE==$userSession->type) {
             //   $this->initAcl($e);
             //   $e->getApplication()->getEventManager()-> attach('route', array($this, 'checkAcl'));
            } 
        }

    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
        	'Zend\Loader\ClassMapAutoloader' => array(
        			__DIR__ . '/autoload_classmap.php',
        	),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
//         return array(
//             'factories' => array(
//                 'AuthService' => function ($sm) {
                   
// 							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
// 							$dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter, 'users','email_id','password', 'MD5(?)');
// 							$authService = new AuthenticationService();
// 							$authService->setAdapter($dbTableAuthAdapter);
// 							$authService->setStorage($sm->get('Auth\Model\MyAuthStorage'));

// 							return $authService;
// 					},
//             )
//         );
    }
    
    public function initAcl(MvcEvent $e) {
    
        $acl = new \Zend\Permissions\Acl\Acl();
        $roles =  array(
                'guest'=> array(
                    'user',
                    'level',
                ),
                'admin'=> array(
                    'admin',
                ),
            );
        $allResources = array();
        foreach ($roles as $role => $resources) {
    
            $role = new \Zend\Permissions\Acl\Role\GenericRole($role);
            $acl -> addRole($role);
    
            $allResources = array_merge($resources, $allResources);
    
            //adding resources
            foreach ($allResources as $resource) {
                // Edit 4
                if(!$acl ->hasResource($resource))
                    $acl -> addResource(new \Zend\Permissions\Acl\Resource\GenericResource($resource));
            }
            //adding restrictions
            foreach ($allResources as $resource) {
                $acl -> allow($role, $resource);
            }
        }
        //testing
        //var_dump($acl->isAllowed('admin','home'));
        //true
    
        //setting to view
        $e -> getViewModel() -> acl = $acl;
    
    }
    
    public function checkAcl(MvcEvent $e) {
        $route = $e -> getRouteMatch() -> getMatchedRouteName();
        //$routeName = $e->getRouteMatch()->getMatchedRouteName();
        //$routeParams = $e->getRouteMatch()->getParams();
       // $customResource = $routeName . $routeParams['controller'] . $routeParams['action'];
       // echo $route; die;
        //you set your role
        $userRole = 'guest';

   if (!$e->getViewModel()->acl->hasResource($route) || !$e->getViewModel()->acl->isAllowed($userRole, $route)) {
           
            $response = $e->getResponse();

            //location to page or what ever
            $response->getHeaders()->addHeaderLine('Location', $e -> getRequest() -> getBaseUrl() . '/index');
            $response->setStatusCode(404);
            $response->sendHeaders();
            $e->stopPropagation();
            
        }
    }
    
    public function getDbRoles(MvcEvent $e){
        // I take it that your adapter is already configured
        $dbAdapter = $e->getApplication()->getServiceManager()->get('Zend\Db\Adapter\Adapter');
        $results = $dbAdapter->query('SELECT * FROM acl');
        // making the roles array
        $roles = array();
        foreach($results as $result){
            $roles[$result['user_role']][] = $result['resource'];
        }
        return $roles;
    }
    
}
