<?php
namespace Jeu\Model;

class Jeu
{
    public $id;
    public $editor;
    public $title;

    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->editor = !empty($data['editor']) ? $data['editor'] : null;
        $this->title  = !empty($data['title']) ? $data['title'] : null;
    }
}