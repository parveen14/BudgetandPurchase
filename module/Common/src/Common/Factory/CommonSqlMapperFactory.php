<?php

namespace Common\Factory;

use Common\Mapper\CommonSqlMapper;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\Stdlib\Hydrator\ClassMethods;

class CommonSqlMapperFactory implements FactoryInterface {
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator        	
	 *
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		return new CommonSqlMapper ( 
			$serviceLocator->get ( 'Zend\Db\Adapter\Adapter' ), 
			new ClassMethods ( false ),
			$serviceLocator->get ( 'AuthService' )
		);
	}
}