<?php

/**
 * @see       https://github.com/laminas/laminas-mvc-skeleton for the canonical source repository
 * @copyright https://github.com/laminas/laminas-mvc-skeleton/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-mvc-skeleton/blob/master/LICENSE.md New BSD License
 */

declare(strict_types=1);

namespace User\Controller;

use Laminas\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Laminas\Uri\Uri;
use User\Form\LoginForm;
use User\Model\UserTable;
use Laminas\Db\Adapter\Adapter;
use Laminas\View\Model\ViewModel;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Authentication\AuthenticationService;

class LoginController extends AbstractActionController
{
    private $adapter; 

    private $userTable;

    // Add this constructor:
    public function __construct(Adapter $adapater, UserTable $userTable)
    {
        $this->adapter = $adapater;
        $this->userTable = $userTable;
    }

    public function loginAction(){
        $auth = new AuthenticationService();
        if($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }
        // Retrieve the redirect URL (if passed). We will redirect the user to this
        // URL after successfull login.
        $redirectUrl = (string)$this->params()->fromQuery('redirectUrl', '');
        if (strlen($redirectUrl)>2048) {
            throw new \Exception("Too long redirectUrl argument passed");
        }

        // Create login form
        $form = new LoginForm();
         // Check if user has submitted the form
         if ($this->getRequest()->isPost()) {
            
            // Fill in the form with POST data
            $data = $this->params()->fromPost();            
            
            $form->setData($data);
            
            // Validate form
            if($form->isValid()) {
                
                // Get filtered and validated data
                $data = $form->getData();
                
                // Perform login attempt.
                $authAdapter = new CredentialTreatmentAdapter($this->adapter);
                $authAdapter->setTableName($this->userTable->getTable())
                            ->setIdentityColumn('email')
                            ->setCredentialColumn('password')
                            ->getDbSelect();
                
                $authAdapter->setIdentity($data['email']);

                $hash = new Bcrypt();
                $info = $this->userTable->findOneByEmail($data['email']);

                if($hash->verify($data['password'], $info->getPassword())){
                    $authAdapter->setCredential($info->getPassword());
                }
                else {
                    $authAdapter->setCredential('');
                }
                
                $result = $auth->authenticate($authAdapter);

                // Check result.
                if ($result->getCode()==Result::SUCCESS) {
                    
                    // Get redirect URL.
                    $redirectUrl = $this->params()->fromPost('redirect_url', '');
                    
                    if (!empty($redirectUrl)) {
                        // The below check is to prevent possible redirect attack 
                        // (if someone tries to redirect user to another domain).
                        $uri = new Uri($redirectUrl);
                        if (!$uri->isValid() || $uri->getHost()!=null)
                            throw new \Exception('Incorrect redirect URL: ' . $redirectUrl);
                    }


                    // If redirect URL is provided, redirect the user to that URL;
                    // otherwise redirect to Home page.
                    if(empty($redirectUrl)) {
                        return $this->redirect()->toRoute('home');
                    } else {
                        $this->redirect()->toUrl($redirectUrl);
                    }
                } 
        }         
        $view = new ViewModel([
            'form' => $form,
            //'redirectUrl' => $redirectUrl
        ]);

        $view->setTemplate('user/user/login');

        return $view; 
    }
    
    /**
     * The "logout" action performs logout operation.
     */
    public function logoutAction() 
    {        
        $auth = new AuthenticationService();
        $auth->clearIdentity();
        
        return $this->redirect()->toRoute('user');
    }

}