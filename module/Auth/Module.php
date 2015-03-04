<?php

namespace Auth;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;

use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;

class Module implements AutoloaderProviderInterface
{
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
            // if we're in a namespace deeper than one level we need to fix the \ in the path
                    __NAMESPACE__ => __DIR__ . '/src/' . str_replace('\\', '/' , __NAMESPACE__),
                ),
            ),
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getServiceConfig()
    {
        return array(
		
            'factories'=>array(

					'Auth\Model\MyAuthStorage' => function ($sm) {
						return new \Auth\Model\MyAuthStorage('users');
					},
					'Auth\Model\Register' => function ($sm){
						return new \Auth\Model\Register();
					},
					'Auth\Model\Forgotpassword' => function ($sm){
						return new \Auth\Model\Forgotpassword();
					},
					'AuthService' => function ($sm) {
								$dbAdapter      = $sm->get('Zend\Db\Adapter\Adapter');
								$dbTableAuthAdapter  = new DbTableAuthAdapter($dbAdapter, 'users','email_id','password', 'MD5(?)');
								$authService = new AuthenticationService();
								$authService->setAdapter($dbTableAuthAdapter);
								$authService->setStorage($sm->get('Auth\Model\MyAuthStorage'));
					
								return $authService;
					},
            ),
        );
    }
}
