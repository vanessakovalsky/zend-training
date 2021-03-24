<?php
namespace User\Form;

use DomainException;
use Laminas\Form\Form;
use Laminas\Form\Element;

class LoginForm extends Form
{
    public function __construct()
    {
        // We will ignore the name provided to the constructor
        parent::__construct('jeu');


        $this->add([
            'name' => 'email',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->add([
            'name' => 'password',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Editor',
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