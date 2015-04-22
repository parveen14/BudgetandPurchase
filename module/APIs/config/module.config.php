<?php
return array(
    'router' => array(
        'routes' => array(
            'ap-is.rest.user' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/user[/:user_id]',
                    'defaults' => array(
                        'controller' => 'APIs\\V1\\Rest\\User\\Controller',
                    ),
                ),
            ),
            'ap-is.rest.getall' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/getall[/:type]',
                    'defaults' => array(
                        'controller' => 'APIs\\V1\\Rest\\Getall\\Controller',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '[/:param2][/:param3]',
                            'constraints' => array(
                                'param2' => '[a-zA-Z0-9_-]*',
                                'param3' => '[a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(),
                        ),
                    ),
                ),
            ),
            'ap-is.rest.purchaserequest' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/api/purchaserequest[/:purchaserequest_id]',
                    'defaults' => array(
                        'controller' => 'APIs\\V1\\Rest\\Purchaserequest\\Controller',
                    ),
                ),
            ),
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'ap-is.rest.user',
            1 => 'ap-is.rest.getall',
            2 => 'ap-is.rest.purchaserequest',
        ),
    ),
    'zf-rest' => array(
        'APIs\\V1\\Rest\\User\\Controller' => array(
            'listener' => 'APIs\\V1\\Rest\\User\\UserResource',
            'route_name' => 'ap-is.rest.user',
            'route_identifier_name' => 'user_id',
            'collection_name' => 'user',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
                2 => 'PUT',
                3 => 'PATCH',
                4 => 'DELETE',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'APIs\\V1\\Rest\\User\\UserEntity',
            'collection_class' => 'APIs\\V1\\Rest\\User\\UserCollection',
            'service_name' => 'User',
        ),
        'APIs\\V1\\Rest\\Getall\\Controller' => array(
            'listener' => 'APIs\\V1\\Rest\\Getall\\GetallResource',
            'route_name' => 'ap-is.rest.getall',
            'route_identifier_name' => 'type',
            'collection_name' => 'getall',
            'entity_http_methods' => array(
                0 => 'GET',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'APIs\\V1\\Rest\\Getall\\GetallEntity',
            'collection_class' => 'APIs\\V1\\Rest\\Getall\\GetallCollection',
            'service_name' => 'getall',
        ),
        'APIs\\V1\\Rest\\Purchaserequest\\Controller' => array(
            'listener' => 'APIs\\V1\\Rest\\Purchaserequest\\PurchaserequestResource',
            'route_name' => 'ap-is.rest.purchaserequest',
            'route_identifier_name' => 'purchaserequest_id',
            'collection_name' => 'purchaserequest',
            'entity_http_methods' => array(
                0 => 'GET',
                1 => 'PATCH',
                2 => 'PUT',
                3 => 'DELETE',
            ),
            'collection_http_methods' => array(
                0 => 'GET',
                1 => 'POST',
                2 => 'PUT',
            ),
            'collection_query_whitelist' => array(),
            'page_size' => 25,
            'page_size_param' => null,
            'entity_class' => 'APIs\\V1\\Rest\\Purchaserequest\\PurchaserequestEntity',
            'collection_class' => 'APIs\\V1\\Rest\\Purchaserequest\\PurchaserequestCollection',
            'service_name' => 'purchaserequest',
        ),
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'APIs\\V1\\Rest\\User\\Controller' => 'HalJson',
            'APIs\\V1\\Rest\\Getall\\Controller' => 'HalJson',
            'APIs\\V1\\Rest\\Purchaserequest\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'APIs\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.ap-is.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'APIs\\V1\\Rest\\Getall\\Controller' => array(
                0 => 'application/vnd.ap-is.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
            'APIs\\V1\\Rest\\Purchaserequest\\Controller' => array(
                0 => 'application/vnd.ap-is.v1+json',
                1 => 'application/hal+json',
                2 => 'application/json',
            ),
        ),
        'content_type_whitelist' => array(
            'APIs\\V1\\Rest\\User\\Controller' => array(
                0 => 'application/vnd.ap-is.v1+json',
                1 => 'application/json',
            ),
            'APIs\\V1\\Rest\\Getall\\Controller' => array(
                0 => 'application/vnd.ap-is.v1+json',
                1 => 'application/json',
            ),
            'APIs\\V1\\Rest\\Purchaserequest\\Controller' => array(
                0 => 'application/vnd.ap-is.v1+json',
                1 => 'application/json',
            ),
        ),
    ),
    'zf-hal' => array(
        'metadata_map' => array(
            'APIs\\V1\\Rest\\User\\UserEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'ap-is.rest.user',
                'route_identifier_name' => 'user_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'APIs\\V1\\Rest\\User\\UserCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'ap-is.rest.user',
                'route_identifier_name' => 'user_id',
                'is_collection' => true,
            ),
            'APIs\\V1\\Rest\\Getall\\GetallEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'ap-is.rest.getall',
                'route_identifier_name' => 'type',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'APIs\\V1\\Rest\\Getall\\GetallCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'ap-is.rest.getall',
                'route_identifier_name' => 'type',
                'is_collection' => true,
            ),
            'APIs\\V1\\Rest\\Purchaserequest\\PurchaserequestEntity' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'ap-is.rest.purchaserequest',
                'route_identifier_name' => 'purchaserequest_id',
                'hydrator' => 'Zend\\Stdlib\\Hydrator\\ArraySerializable',
            ),
            'APIs\\V1\\Rest\\Purchaserequest\\PurchaserequestCollection' => array(
                'entity_identifier_name' => 'id',
                'route_name' => 'ap-is.rest.purchaserequest',
                'route_identifier_name' => 'purchaserequest_id',
                'is_collection' => true,
            ),
        ),
    ),
    'zf-content-validation' => array(
        'APIs\\V1\\Rest\\Getall\\Controller' => array(
            'input_filter' => 'APIs\\V1\\Rest\\Getall\\Validator',
        ),
    ),
    'input_filter_specs' => array(
        'APIs\\V1\\Rest\\Getall\\Validator' => array(
            0 => array(
                'name' => 'type',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
            1 => array(
                'name' => 'i_ref_company_id',
                'required' => true,
                'filters' => array(),
                'validators' => array(),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'APIs\\V1\\Rest\\User\\UserResource' => 'APIs\\V1\\Rest\\User\\UserResourceFactory',
            'APIs\\V1\\Rest\\Getall\\GetallResource' => 'APIs\\V1\\Rest\\Getall\\GetallResourceFactory',
            'APIs\\V1\\Rest\\Purchaserequest\\PurchaserequestResource' => 'APIs\\V1\\Rest\\Purchaserequest\\PurchaserequestResourceFactory',
        ),
    ),
);
