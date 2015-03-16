<?php
namespace APIs;

use ZF\Apigility\Provider\ApigilityProviderInterface;

class Module implements ApigilityProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'ZF\Apigility\Autoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }
	
	public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'APIs\V1\Rest\User\UserMapper' =>  function ($sm) {
                    $adapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $commonService = $sm->get('Common\Service\CommonServiceInterface');
                    return new \APIs\V1\Rest\User\UserMapper($adapter, $commonService);
                },
                'APIs\V1\Rest\User\UserResource' => function ($sm) {
                    $mapper = $sm->get('APIs\V1\Rest\User\UserMapper');
                    return new \APIs\V1\Rest\User\UserResource($mapper);
                },
            ),
        );
    }
}