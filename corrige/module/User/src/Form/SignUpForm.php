<?php

namespace User\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;

class SignUpForm extends Form
{
    public function __construct()
    {
        // We will ignore the name provided to the constructor
        parent::__construct('new_account');

        $this->add([
            'name' => 'username',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Username',
            ],
        ]);

        $this->add([
            'name' => 'email',
            'type' => Element\Email::class,
            'options' => [
                'label' => 'Email',
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => Element\Password::class,
            'options' => [
                'label' => 'Password',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}