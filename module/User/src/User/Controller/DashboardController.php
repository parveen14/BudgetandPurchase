<?php 

namespace User\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use User\Service\UserServiceInterface;
use Zend\View\Model\ViewModel;
use Common\Service\CommonServiceInterface;
use Zend\Session\Container;

class DashboardController extends AbstractActionController 
{
    
    protected $userService;
    protected $authservice;
    protected $commonService;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e) {
        if (! $this->getAuthService ()->hasIdentity ()) {
            return $this->redirect ()->toRoute ( 'login' );
        }
    
        return parent::onDispatch ( $e );
    }
    
    public function __construct(UserServiceInterface $userService,CommonServiceInterface $commonService){
        
        $this->userService=$userService;
        $this->commonService = $commonService;
        
    }
    
    public function getAuthService() {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator ()->get ( 'AuthService' );
        }
    
        return $this->authservice;
    }

    public function indexAction()
    {  
		$userSession = new Container('user');
        $totalRole = $this->commonService->getTotalRecordCount('roles',array('i_ref_company_id'=>$userSession->data->i_company_id));
        $totalGroup = $this->commonService->getTotalRecordCount('groups',array('i_ref_company_id'=>$userSession->data->i_company_id));
        $totalBusinessunit = $this->commonService->getTotalRecordCount('business_units',array('i_ref_company_id'=>$userSession->data->i_company_id));
        $totalDepartment = $this->commonService->getTotalRecordCount('departments',array('i_ref_company_id'=>$userSession->data->i_company_id));
        $totalProjets = $this->commonService->getTotalRecordCount('projects',array('i_ref_company_id'=>$userSession->data->i_company_id));
        
        $this->layout('layout/admin_layout');
        return new ViewModel(array(
            'totalRole' => $totalRole,
            'totalGroup' => $totalGroup,
            'totalBusinessunit' => $totalBusinessunit,
            'totalDepartment' => $totalDepartment,
            'totalProjets' => $totalProjets
        ));
        
    }
}

?>
