<?php
return array(
    'router' => array(
        'routes' => array(
           
        )
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
            'Common\Mapper\CommonSqlMapperInterface' => 'Common\Factory\CommonSqlMapperFactory',
            'Common\Service\CommonServiceInterface' => 'Common\Factory\CommonServiceFactory',
        )
    ),
    'controllers' => array(
        'factories' => array(
        	'Common\Controller\Common' => 'Common\Factory\CommonControllerFactory'
        ),
    ),
    'view_manager' => array (
        'template_path_stack' => array (
            __DIR__ . '/../view'
        ),
        'strategies' => array (
            'ViewJsonStrategy'
        )
    ),
);