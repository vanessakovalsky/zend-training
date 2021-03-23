<?php
namespace Jeu\Form;

use DomainException;
use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Submit;
use Laminas\InputFilter\InputFilterInterface;

class JeuForm extends Form
{
    public function __construct()
    {
        // We will ignore the name provided to the constructor
        parent::__construct('jeu');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);

        $this->add([
            'name' => 'title',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->add([
            'name' => 'editor',
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