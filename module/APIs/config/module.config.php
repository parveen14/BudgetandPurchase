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
        ),
    ),
    'zf-versioning' => array(
        'uri' => array(
            0 => 'ap-is.rest.user',
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
    ),
    'zf-content-negotiation' => array(
        'controllers' => array(
            'APIs\\V1\\Rest\\User\\Controller' => 'HalJson',
        ),
        'accept_whitelist' => array(
            'APIs\\V1\\Rest\\User\\Controller' => array(
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
        ),
    ),
    'zf-content-validation' => array(),
    'input_filter_specs' => array(),
    'service_manager' => array(
        'factories' => array(
            'APIs\\V1\\Rest\\User\\UserResource' => 'APIs\\V1\\Rest\\User\\UserResourceFactory',
        ),
    ),
);
