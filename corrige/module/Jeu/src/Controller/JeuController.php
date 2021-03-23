<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Jeu\Controller;

use Laminas\View\Model\ViewModel;
use Laminas\Mvc\Controller\AbstractActionController;
use Jeu\Model\JeuTable;

class JeuController extends AbstractActionController
{
    // Add this property:
    private $table;

    // Add this constructor:
    public function __construct(JeuTable $table)
    {
        $this->table = $table;
    }


    public function indexAction()
    {
        $view = new ViewModel([   
            'jeux' => $this->table->fetchAll(),
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
