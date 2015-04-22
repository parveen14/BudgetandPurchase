<?php
namespace Auth\Form;
use Zend\Form\Form;

class ForgotpasswordForm extends Form{
	public function __construct(){
		parent::__construct();
		
		$this->setAttribute('method', 'post');
		$this->setAttribute('id', 'forgotpasswordForm');
		
		
		$this->add(array(
				'name' => 'email_id',
				'attributes' => array(
						'type'  => 'email',
						'placeholder' => 'Email Address',
				         'class' => 'form-control uname'
				),
		));

		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => 'Continue',
						'id' => 'submitbutton',
						'class' => 'btn btn-success btn-block'
				),
		));
	}
}