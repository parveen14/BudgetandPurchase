<?php

namespace Common\Factory;

use Common\Controller\CommonController;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class CommonControllerFactory implements FactoryInterface {
	/**
	 * Create service
	 *
	 * @param ServiceLocatorInterface $serviceLocator        	
	 *
	 * @return mixed
	 */
	public function createService(ServiceLocatorInterface $serviceLocator) {
		$realServiceLocator = $serviceLocator->getServiceLocator ();
		$commonService = $realServiceLocator->get ( 'Common\Service\CommonServiceInterface' );
		$authService = $realServiceLocator->get ( 'AuthService' );

		return new CommonController ( $commonService, $authService);
	}
}