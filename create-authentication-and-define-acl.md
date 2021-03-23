# Créer l'authentification et définir des permissions basées sur des rôles

Cet exercice a pour objects :
* De permettre à un utilisateur de se connecter à notre application
* De vérifier si l'utilisateur est connecté
* D'attribuer un role a un utilisateur
* De vérifier le rôle d'un utilisateur connecté pour lui permettre d'accéder ou non à certaines actions

## Installation d'un module qui gère l'authentification

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

*  

## Bloque des routes avec les guards
* Les guards permettent de bloquer complétement l'accès à une route en fonction du rôle de l'utilisateur :
[https://github.com/LM-Commons/LmcRbacMvc/raw/master/docs/images/workflow-with-guards.png?raw=true]
* Cela permet de définir des rôles ou des permissions pour accéder à certaines routes. Cela se définit dans le fichier de config (le même dans lequel les rôles ont été déclarés) :
``` php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RoutePermissionsGuard' => [
                'admin*' => ['admin'],
                'post/manage' => ['post.update', 'post.delete']
            ]
        ]
    ]
];
```
* Par défaut les conditions se cumulent (AND), il est possible d'avoir des où également avec :
``` php
use LmcRbacMvc\Guard\GuardInterface;

return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\RoutePermissionsGuard' => [
                'post/manage'   => [
                    'permissions' => ['post.update', 'post.delete'],
                    'condition'   => GuardInterface::CONDITION_OR
                ]
            ]
        ]
    ]
];
```
* Il est également possible de définir des guards sur des controlleurs en utilisant des rôles:
``` php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\ControllerGuard' => [
                [
                    'controller' => 'MyController',
                    'actions'    => ['read', 'edit'],
                    'roles'      => ['guest', 'member']
                ]
            ]
        ]
    ]
];
```
* Ainsi qu'en utilisant des permissions :
``` php
return [
    'lmc_rbac' => [
        'guards' => [
            'LmcRbacMvc\Guard\ControllerPermissionsGuard' => [
                [
                    'controller'  => 'MyController',
                    'permissions' => ['post.update', 'post.delete']
                ]
            ]
        ]
    ]
];
```
* Il est également possible de créer son propre guard, un exemple :
``` php
namespace Application\Guard;

use Laminas\Http\Request as HttpRequest;
use Laminas\Mvc\MvcEvent;
use LmcRbacMvc\Guard\AbstractGuard;

class IpGuard extends AbstractGuard
{
    const EVENT_PRIORITY = 100;

    /**
     * List of IPs to blacklist
     */
    protected $ipAddresses = [];

    /**
     * @param array $ipAddresses
     */
    public function __construct(array $ipAddresses)
    {
        $this->ipAddresses = $ipAddresses;
    }

    /**
     * @param  MvcEvent $event
     * @return bool
     */
    public function isGranted(MvcEvent $event)
    {
        $request = $event->getRequest();

        if (!$request instanceof HttpRequest) {
            return true;
        }

        $clientIp = $_SERVER['REMOTE_ADDR'];

        return !in_array($clientIp, $this->ipAddresses);
    }
}
```
* N'oubliez pas de déclarer votre propre guard dans la config :
``` php
    'zfc_rbac' => [
        'guard_manager' => [
            'factories' => [
                'Application\Guard\IpGuard' => 'Application\Factory\IpGuardFactory'
            ]
        ]
    ]
```

## Réagir à des évènements avec les stratégies 
