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

class Module implements AutoloaderProviderInterface
{
	
    public function onBootstrap(MvcEvent $e)
    {
        $app = $e->getApplication();
        $sm = $app->getServiceManager();
        $auth = $sm->get('AuthService');
        if ($auth->hasIdentity()) {
            $viewModel = $app
            ->getMvcEvent()
            ->getViewModel();
            $userSession = new Container('user');
            $viewModel->userSession = $userSession;
            
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
        return array(
            'factories' => array(
                'AuthService' => function ($sm) {
                   
							$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
							$dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter, 'users','email_id','password', 'MD5(?)');
							$authService = new AuthenticationService();
							$authService->setAdapter($dbTableAuthAdapter);
							$authService->setStorage($sm->get('Auth\Model\MyAuthStorage'));

							return $authService;
					},
            )
        );
    }
    
}