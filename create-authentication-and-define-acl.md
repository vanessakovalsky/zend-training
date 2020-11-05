# Créer l'authentification et définir des permissions basées sur des rôles

Cet exercice a pour objects :
* De permettre à un utilisateur de se connecter à notre application
* De vérifier si l'utilisateur est connecté
* D'attribuer un role a un utilisateur
* De vérifier le rôle d'un utilisateur connecté pour lui permettre d'accéder ou non à certaines actions

## Pré-requis : installation d'un module qui gère l'authentification

* Afin de gagner du temps, nous utilisons un module existant qui permet d'avoir directement des utilisteurs et la possibilité de se connecter :
```
composer require laminas-commons/lmc-user
```
* Activer le module en l'ajoutant dans votre fichier config/modules.config.php
``` php
return [
    'modules' => [
        // ...
        'LmcUser',
    ],
    // ...
];
``` 
* Créer la table de données dans votre base à partir du fichier qui est dans /vendor/fgsl/laminas-user/data/schema.* (en fonction de votre base de donnée)
* Dans le fichier config/autoload/global.php, ajouter les lignes suivantes :
``` php
    'service_manager' => [
        'factories' => [
            'Laminas\Db\Adapter\Adapter' => 'Laminas\Db\Adapter\AdapterServiceFactory',
        ],
    ],
``` 
* Cela permet d'injecter l'adaptateur de base de données à notre service manager (nécessaire pour l'authentification)
* Vous pouvez maintenant accéder à http://votre-projet/user et accéder au formulaire de connexion

## Tester si un utilisateur est connecté
* Il est possible de savoir si un utilisateur de trois façons différentes :
* * Dans les vues avec :
``` php
<!-- Test if the User is connected -->
<?php if(!$this->lmcUserIdentity()): ?>
    <!-- display the login form -->
    <?php echo $this->lmcUserLoginWidget(array('redirect'=>'application')); ?>
<?php else: ?>
    <!-- display the 'display name' of the user -->
    <?php echo $this->lmcUserIdentity()->getDisplayname(); ?>
<?php endif?>
```
* * Dans un controlleur :
``` php
<?php
if ($this->lLmcUserAuthentication()->hasIdentity()) {
    //get the email of the user
    echo $this->lmcUserAuthentication()->getIdentity()->getEmail();
    //get the user_id of the user
    echo $this->lmcUserAuthentication()->getIdentity()->getId();
    //get the username of the user
    echo $this->lmcUserAuthentication()->getIdentity()->getUsername();
    //get the display name of the user
    echo $this->lmcUserAuthentication()->getIdentity()->getDisplayname();
}
?>
```
* * Depuis le service Manager :
``` php
<?php
$sm = $app->getServiceManager();
$auth = $sm->get('zfcuser_auth_service');
if ($auth->hasIdentity()) {
    echo $auth->getIdentity()->getEmail();
}
?>
```
* Ajouter dans le controlleur ou les vues les contrôles nécessaires pour accèder à certaines pages en fonctin du cahier des charges.
* Ajouter le formulaire de login dans la barre de menu si l'utilisateur n'est pas connecté ou son nom d'utilisateur s'il est connecté (à faire au niveau du layout.phtml)


## Définition des rôles et des permissions :
* Nous allons utiliser le module lmc-rbac-mvc qui vient s'appuyer sur le composant rbac de laminas, pour l'installer :
``` 
composer require lm-commons/lmc-rbac-mvc:^3.0
```
* Il y a deux façons de définir des permissions :
* * les guards qui bloquent l'accès à certaines routes ou controlleur
* * Un service d'autorisations qui va définir plus finement des permissions.

