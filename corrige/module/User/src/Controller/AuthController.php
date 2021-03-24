<?php

declare(strict_types=1);

namespace User\Controller;

use User\Form\SignUpForm;
use User\Model\UserTable;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\AuthenticationService;
use Laminas\Mvc\Controller\AbstractActionController;
use RuntimeException;

class AuthController extends AbstractActionController
{

    private $userTable;

    public function __construct(UserTable $userTable)
    {
        $this->userTable = $userTable;
    }

    public function createAction()
    {
        $auth = new AuthenticationService();
        if ($auth->hasIdentity()){
            return $this->redirect()->toRoute('home');
        };

        $form = new SignUpForm();

        if($this->getRequest()->isPost()){
            // Fill in the form with POST data
            $data = $this->params()->fromPost(); 
                        
            $form->setData($data);
            if ($form->isValid()){
                try {
                    // Get filtered and validated data
                    $data = $form->getData();
                    $this->userTable->save($data);
                    return $this->redirect()->toRoute('login');
                }
                catch(RuntimeException $exception){
                    return $this->redirect()->refresh();
                }

            }
        }

        return new ViewModel([
            'form' => $form,
        ]);
    }

}