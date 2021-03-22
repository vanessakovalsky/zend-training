<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Jeu\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class JeuController extends AbstractActionController
{
    public function indexAction()
    {
        $listeJeux = [
            ['id' => 1, 'title' => 'Les aventuriers du rail', 'editor' => 'Asmodée'],
            ['id' => 2, 'title' => 'Les aventuriers du rail Europe', 'editor' => 'Asmodée'],
            ['id' => 3, 'title' => 'Les aventuriers du rail Monde', 'editor' => 'Asmodée'],
            ['id' => 4, 'title' => 'Les aventuriers du rail Japon/Italie', 'editor' => 'Asmodée'],
        ];
        $view = new ViewModel([   
            'jeux' => $listeJeux,
        ]);
        return $view;
    }

    public function showAction(){
        $view = new ViewModel([
            'toto' => 'toto',
        ]);
        return $view;
    }

    public function addAction()
    {
        $view = new ViewModel([
        ]);
        $view->setTemplate('jeu/jeu/form');
        return $view;
    }

    public function editAction()
    {
        $params = $this->params()->fromRoute();
        $id = $params['id'];

        $view = new ViewModel([
            'id' => $id,
        ]);
        $view->setTemplate('jeu/jeu/form');
        return $view;
    }

    public function deleteAction()
    {
    }
}
