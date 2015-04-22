<?php 


namespace User\Factory;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use User\Mapper\UserMapper;
use Zend\Stdlib\Hydrator\ClassMethods;
use User\Model\User;

class UserMapperFactory implements FactoryInterface {
    
    public function createService(ServiceLocatorInterface $serviceLocator){
        return new UserMapper(
                $serviceLocator->get('Zend\Db\Adapter\Adapter'),
                new ClassMethods(false),
                new User(),
                $serviceLocator->get('Common\Service\CommonServiceInterface')
            );
    }
}


?>
