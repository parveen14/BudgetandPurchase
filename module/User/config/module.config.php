<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(

        	'user' => array(
            		'type'    => 'Literal',
            		'options' => array(
            				'route'    => '/user',
            				'defaults' => array(
            						'__NAMESPACE__' => 'User\Controller',
            						'controller'    => 'User',
            						'action'        => 'index',
            				),
            		),
            		'may_terminate' => true,
            		'child_routes' => array(
            				'default' => array(
            						'type'    => 'Segment',
            						'options' => array(
            								'route'    => '/[:action][/:param1][/:param2][/:param3]',
            								'constraints' => array(
            										'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
            										'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                								    'param1'     => '[a-zA-Z0-9_-]*',
                								    'param2'     => '[a-zA-Z0-9_-]*',
                								    'param3'     => '[a-zA-Z0-9_-]*',
            								),
            								'defaults' => array(
            								),
            						),
            				),
            		),
            ),	
            'department' => array (
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array (
                    'route' => '/user/department',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'department'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array (
                    'process' => array (
                        'type' => 'Segment',
                        'options' => array (
                            'route' => '/[:action][/:slug]',
                            'constraints' => array (
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'slug' => '[a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array ()
                        )
                    )
                )
            ),
            'businessunit' => array (
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array (
                    'route' => '/user/businessunit',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'businessunit'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array (
                    'process' => array (
                        'type' => 'Segment',
                        'options' => array (
                            'route' => '/[:action][/:slug]',
                            'constraints' => array (
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'slug' => '[a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array ()
                        )
                    )
                )
            ),
            'level' => array (
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array (
                    'route' => '/user/level',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'level'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array (
                    'process' => array (
                        'type' => 'Segment',
                        'options' => array (
                            'route' => '/[:action][/:slug]',
                            'constraints' => array (
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'slug' => '[a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array ()
                        )
                    )
                )
            ),
            'group' => array (
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array (
                    'route' => '/user/group',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'group'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array (
                    'process' => array (
                        'type' => 'Segment',
                        'options' => array (
                            'route' => '/[:action][/:slug]',
                            'constraints' => array (
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'slug' => '[a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array ()
                        )
                    )
                )
            ),
            'project' => array (
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array (
                    'route' => '/user/project',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'project'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array (
                    'process' => array (
                        'type' => 'Segment',
                        'options' => array (
                            'route' => '/[:action][/:slug]',
                            'constraints' => array (
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'slug' => '[a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array ()
                        )
                    )
                )
            ),
            
            'location' => array (
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array (
                    'route' => '/user/location',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'location'
                    )
                ),
                'may_terminate' => true,
                'child_routes' => array (
                    'process' => array (
                        'type' => 'Segment',
                        'options' => array (
                            'route' => '/[:action][/:slug]',
                            'constraints' => array (
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'slug' => '[a-zA-Z0-9_-]*'
                            ),
                            'defaults' => array ()
                        )
                    )
                )
            ),
        ),
    ),
	  'service_manager' => array(
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),

    	'factories' => array(
    	    'User\Mapper\UserMapperInterface'   => 'User\Factory\UserMapperFactory',
 			'User\Service\UserServiceInterface' => 'User\Factory\UserServiceFactory',
    		'Zend\Db\Adapter\Adapter'           => 'Zend\Db\Adapter\AdapterServiceFactory'
    	)
    ),
  
    'controllers' => array(
    	'factories' => array(
			'User\Controller\User' => 'User\Factory\UserControllerFactory',
    	),
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(

        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
            ),
        ),
    ),
	
);