<?php
namespace Auth\Form;
use Zend\Form\Form;

class AuthenticateForm extends Form{
	public function __construct(){
		parent::__construct();
		$this->setAttribute('method', 'post');
		$this->setAttribute('id', 'loginForm');
		
		$this->add(array(
				'name' => 'vc_email',
				'attributes' => array(
						'type'  => 'text',
						'placeholder' => 'Email',
				        'class' => 'form-control uname'
				),
		));
		
		$this->add(array(
				'name' => 'vc_password',
				'attributes' => array(
						'type'  => 'password',
						'placeholder' => 'Password',
				        'class'=> 'form-control pword'
				),
		));
		
		$this->add(array(
				'type'  => 'Zend\Form\Element\Select',
				'name' => 'type',
				'attributes' => array(
				        'class'=> 'form-control uname'
				),
				'options' => array(
				        'value_options'=>array(
												'Company'=>'Company',
												'Employee'=>'Employee'
											),
				),
		));
		
		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => 'Login',
						'id' => 'submitbutton',
						'class' => 'btn btn-success btn-block'
				),
		));
	}
}
