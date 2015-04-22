<?php
namespace Auth\Form;
use Zend\Form\Form;

class ActivateuserForm extends Form{
	public function __construct(){
		parent::__construct();
		$this->setAttribute('method', 'post');
		$this->setAttribute('id', 'useractivationForm');
		
		$this->add(array(
				'name' => 'vc_email',
				'attributes' => array(
						'type'  => 'text',
						'placeholder' => 'Email',
				        'class' => 'form-control uname',
				        'readonly'=>'readonly'
				),
		));
		
		$this->add(array(
				'name' => 'vc_fname',
				'attributes' => array(
						'type'  => 'text',
						'placeholder' => 'First Name',
				        'class' => 'form-control uname'
				),
		));
		
		$this->add(array(
				'name' => 'vc_lname',
				'attributes' => array(
						'type'  => 'text',
						'placeholder' => 'Last Name',
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
		    'name' => 'activate_token',
		    'attributes' => array(
		        'type'  => 'hidden'
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
