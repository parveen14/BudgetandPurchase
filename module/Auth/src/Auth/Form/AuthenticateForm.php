<?php
namespace Auth\Form;
use Zend\Form\Form;

class AuthenticateForm extends Form{
	public function __construct(){
		parent::__construct();
		$this->setAttribute('method', 'post');
		$this->setAttribute('id', 'loginForm');
		
		$this->add(array(
				'name' => 'email_id',
				'attributes' => array(
						'type'  => 'text',
						'placeholder' => 'Email',
				        'class' => 'form-control uname'
				),
		));
		
		$this->add(array(
				'name' => 'password',
				'attributes' => array(
						'type'  => 'password',
						'placeholder' => 'Password',
				        'class'=> 'form-control pword'
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