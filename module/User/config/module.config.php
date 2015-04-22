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

            'dashboard' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/dashboard',
                    'defaults' => array(
                        '__NAMESPACE__' => 'User\Controller',
                        'controller'    => 'Dashboard',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
            ),
            
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
                'type' => 'segment',
                'options' => array (
                    'route' => '/user/department/[:action][/:slug]',
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
                'type' => 'segment',
                'options' => array (
                    'route' => '/user/businessunit/[:action][/:slug]',
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
            'role' => array (
                'type' => 'segment',
                'options' => array (
                    'route' => '/user/role/[:action][/:slug]',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'role'
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
                'type' => 'segment',
                'options' => array (
                    'route' => '/user/level/[:action][/:slug]',
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
                'type' => 'segment',
                'options' => array (
                    'route' => '/user/group/[:action][/:slug]',
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
                'type' => 'segment',
                'options' => array (
                    'route' => '/user/project/[:action][/:slug]',
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
                'type' => 'Segment',
                'options' => array (
                    'route' => '/user/location/[:action][/:slug]',
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
            'costcenter' => array (
                'type' => 'Segment',
                'options' => array (
                    'route' => '/user/costcenter/[:action][/:slug]',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'costcenter'
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
            'ccgroup' => array (
                'type' => 'Segment',
                'options' => array (
                    'route' => '/user/ccgroup[/:action][/:slug]',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'costcentergroup'
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
            'wbs' => array (
                'type' => 'Segment',
                'options' => array (
                    'route' => '/user/wbs/[:action][/:slug]',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'wbs'
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
			'getwbs' => array (
                'type' => 'Segment',
                'options' => array (
                    'route' => '/user/getwbs[/:slug]',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'getwbs'
                    )
                ),
                
            ),
			'purchaserequest' => array (
                'type' => 'Segment',
                'options' => array (
                    'route' => '/user/purchaserequest/[:action][/:slug]',
                    'defaults' => array (
                        '__NAMESPACE__' => 'User\Controller',
                        'controller' => 'User',
                        'action' => 'purchaserequest'
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
    	    'User\Controller\Dashboard' => 'User\Factory\DashboardControllerFactory',
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
