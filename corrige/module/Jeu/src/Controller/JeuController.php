<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace Jeu\Controller;

use Jeu\Model\Jeu;
use Jeu\Form\JeuForm;
use Jeu\Model\JeuTable;
use Laminas\View\Model\ViewModel;
use Laminas\Mvc\Controller\AbstractActionController;

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
        //Les deux première lignes permette d'instancier le formulaire et de définir la valeur du bouton de soumission
        $form = new JeuForm();
        $form->get('submit')->setValue('Add');

        //nous récupérons la requête, si celle-ci n'utilise pas la méthode POST (envoi de données), nous renvoyons le formulaire vide.
        $request = $this->getRequest();

        if (! $request->isPost()) {
            $view = new ViewModel([
                'form' => $form
            ]);
            $view->setTemplate('jeu/jeu/form');
            return $view;
        }

        //Si des données ont été envoyer, nous créeons un nouvel objet Jeu, puis utilisons les filtres définis dans le modèles auquel nous soumettons les données reçues

        $jeu = new Jeu();
        //$form->setInputFilter($jeu->getInputFilter());
        $form->setData($request->getPost());

        // Nous vérifions si le données envoyées sont valide, si ce n'est pas le cas, nous renvoyons le formulaire

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        // Si les données sont valide, nous hydratons l'objet jeu avec la fonction exchangeArray et utilisons la fonction saveJeu du Depôt (JeuTable)
        $jeu->exchangeArray($form->getData());
        $this->table->saveJeu($jeu);
        // Finalement on redirige vers la liste des jeux
        return $this->redirect()->toRoute('jeu');

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
