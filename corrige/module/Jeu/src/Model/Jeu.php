<?php
namespace Jeu\Model;

use Laminas\Filter\ToInt;
use Laminas\Filter\StripTags;
use Laminas\Filter\StringTrim;
use Laminas\Validator\StringLength;
use Laminas\InputFilter\InputFilter;
use Laminas\Form\Exception\DomainException;
use Laminas\InputFilter\InputFilterInterface;

class Jeu
{
    public $id;
    public $editor;
    public $title;

    private $inputFilter;

    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->editor = !empty($data['editor']) ? $data['editor'] : null;
        $this->title  = !empty($data['title']) ? $data['title'] : null;
    }

    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'editor' => $this->editor,
            'title'  => $this->title,
        ];
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }


    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'title',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'editor',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
}