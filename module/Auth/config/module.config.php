<?php

return array(
    'controllers' => array(
        'invokables' => array(
            'Auth\Controller\Auth' => 'Auth\Controller\AuthController',
            'Auth\Controller\Success' => 'Auth\Controller\SuccessController'
        ),
    ),
    'router' => array(
        'routes' => array(

            'login' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/authenticate',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'login',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'authenticate' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        		
			'register' => array(
					'type'    => 'Literal',
					'options' => array(
							'route'    => '/authenticate/register',
							'defaults' => array(
									'__NAMESPACE__' => 'Auth\Controller',
									'controller'    => 'Auth',
									'action'        => 'register',
							),
					),
					'may_terminate' => true,
					'child_routes' => array(
							'process' => array(
									'type'    => 'Segment',
									'options' => array(
											'route'    => '/[:action]',
											'constraints' => array(
													'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
													'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
											),
											'defaults' => array(
											),
									),
							),
					),
			),
            'forgotpassword' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/authenticate/forgotpassword',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'forgotpassword',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
			'activate' => array(
					'type'    => 'Literal',
					'options' => array(
							'route'    => '/authenticate/activate',
							'defaults' => array(
									'__NAMESPACE__' => 'Auth\Controller',
									'controller'    => 'Auth',
									'action'        => 'activatecompany',
							),
					),
					'may_terminate' => true,
					'child_routes' => array(
							'process' => array(
									'type'    => 'Segment',
									'options' => array(
											'route'    => '/[:activate_token]',
											'constraints' => array(
													'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
													'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
													
											),
											'defaults' => array(
											),
									),
							),
					),
			),
            'activateuser' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/authenticate/activate/user',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'activateuser',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:activate_token]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                                	
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'resetpassword' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/authenticate/resetpassword',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Auth\Controller',
                        'controller'    => 'Auth',
                        'action'        => 'resetpassword',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '[/:password_token]',
                            'constraints' => array(
									'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
									'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Auth' => __DIR__ . '/../view',
        ),
    ),
);
