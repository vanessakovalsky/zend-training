# Créer l'authentification et définir des permissions basées sur des rôles

Cet exercice a pour objects :
* De permettre à un utilisateur de se connecter à notre application
* De vérifier si l'utilisateur est connecté
* D'attribuer un role a un utilisateur
* De vérifier le rôle d'un utilisateur connecté pour lui permettre d'accéder ou non à certaines actions

## Installation des composants nécessaires pour l'authentification

* Pour mettre en place une authentification il est nécessaire d'installer plusieurs composants de laminas : 
```
composer require laminas/laminas-authentication
composer require laminas/laminas-session
composer require laminas/laminas-crypt
```

## Création du module User
* En repartant de ce que l'on a déjà vu il est nécessaire d'effectuer les actions suivantes : 
* * Création d'un nouveau module (User dans le corrige)
* * Mise en place d'un modele de données User et de sa table
* * Création de deux controller : un pour l'enregistrement des utilisateurs et un pour la connexion
* * Création de deux formulaires et de leurs vues : un pour l'enregistrement des utilisateurs et un pour la connexion.

## Chiffrer le mot de passe lors de la création de l'utilisateur

* Lors de la création de l'utilisateur, il est nécessaire de chiffrer le mot de passe avant de l'enregistrer.
* Pour cela nous utilisons Bcrypt qui est l'algorythme par défaut utiliser par laminas-authentication comme suit : 
```
    public function save(array $data){
        $values = [
            'username' => ucfirst($data['username']),
            'email'    => mb_strtolower($data['email']),
            'password' => (new Bcrypt())->create($data['password'])
        ];

        $sqlQuery =  $this->sql->insert()->values($values);
        $sqlStmt = $this->sql->prepareStatementForSqlObject($sqlQuery);

        return $sqlStmt->execute();
    }
```
* Ici on appelle la fonction create sur l'objet Bcrypt pour chiffrer le mot de passe avant de l'enregistrer en BDD.


## Valider la connexion ou la refuser depuis le formulaire de login
* Afin de valider ou non la connexion on utilise la méthode authenticiate du service AuthenticationService comme suit : 
``` php
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
                        return $this->redirect()->toRoute('home');
                } 
        }         
```
* Dans le détail : 
* * On crée un objet qui permet de traiter les credentials 
* Puis on charge l'objet user avec la méthode finedOneByEmail (définie dans le UserTable)
* * On vérifie que le hash du mot de passe saisie correspond au hash du mot de passe enregistré.
* * On appel le service et sa méthode authenticate avec l'objet de traitement que l'on a créé et hydrater.
* * Cette méthode renvoit un résultat SUCCESS ou ERROR qui permet de définir la suite des actions. 

## Tester si un utilisateur est connecté
* Pour savoir si un utilisateur est connecté, on utilise le service AuthenticationService de la façon suivante.
``` php
<?php
$auth = new AuthenticationService();
        if($auth->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }
?>
```
* Cela permet de tester dans les classes PHP mais aussi d'envoyer soit l'objet auth dans les vues soit simplement un booleen pour indiquer si l'utilsiateur est connecté ou non .
* Ajouter dans le controlleur ou les vues les contrôles nécessaires pour accèder à certaines pages en fonction du cahier des charges.
* Ajouter le formulaire de login dans la barre de menu si l'utilisateur n'est pas connecté ou son nom d'utilisateur s'il est connecté (à faire au niveau du layout.phtml)

