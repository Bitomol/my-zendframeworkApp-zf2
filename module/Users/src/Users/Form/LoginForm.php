<?php
// filename : module/Users/src/Users/Form/RegisterForm.php
namespace Users\Form;

use Zend\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('Login');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype','multipart/form-data');

        
        $this->add(array(
            'name' => 'email',
            'attributes' => array(
                'type'  => 'email',
				'required' => 'required',
				'placeholder' => 'Email'
            ),
            'options' => array(
                'label' => 'Email',
            ),
        )); 
        
	$this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type'  => 'password',
				'required' => 'required', 
				'placeholder' => 'Passwort'
            ),
            'options' => array(
                'label' => 'Passwort',
            ),
        )); 


        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Login'
            ),
        )); 
    }
}
