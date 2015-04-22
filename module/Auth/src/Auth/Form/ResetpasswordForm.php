<?php
namespace Auth\Form;
use Zend\Form\Form;

class ResetpasswordForm extends Form{
	public function __construct(){
		parent::__construct();
		
		$this->setAttribute('method', 'post');
		$this->setAttribute('id', 'resetpasswordForm');
		
		
		$this->add(array(
				'name' => 'password',
				'attributes' => array(
						'type'  => 'password',
						'placeholder' => 'Password',
						'id' => 'password',
				    'class'=>'form-control pword'
				),
		));
		
		$this->add(array(
		    'name' => 'confirm_password',
		    'attributes' => array(
		        'type'  => 'password',
		        'placeholder' => 'Confirm Password',
		        'class'=>'form-control pword'
		    ),
		));

		$this->add(array(
				'name' => 'submit',
				'attributes' => array(
						'type'  => 'submit',
						'value' => 'Submit',
						'id' => 'submitbutton',
						'class' => 'btn btn-success btn-block'
				),
		));
	}
}