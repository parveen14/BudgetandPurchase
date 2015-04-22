<?php

namespace Common\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Common\Service\CommonServiceInterface;
use Auth\Form\AuthenticateForm;

class CommonController extends AbstractActionController {
	protected $commonService;
	protected $authservice;

	public function __construct(CommonServiceInterface $commonService, $authService) {
		$this->commonService = $commonService;
		$this->authservice = $authService;
		
	}
	public function indexAction() {
		$this->layout ( 'layout/login' );
		if ($this->authservice->hasIdentity ()) {
			return $this->redirect ()->toRoute ( 'dashboard' );
		}
		return $this->redirect ()->toRoute ( 'login' );
	}
}