# Créer un premier module

Cet exercice a pour objectifs :
* de créer un premier module
* de configurer le module 

## Création du module et architecture

* Commencer par créer un dossier module à l'intérieur du projet
* Puis créer l'arborescence suivante :
```
/Jeu
    /config
    /src
        /Controller
        /Form
        /Model
    /view
        /jeu
        /jeu
```
* Cette arborescence est l'arborescence standard d'un module dans Zend / Laminas
* Les majuscules au début du nom du module, et au début du nom des dossiers dans src sont également obligatoires dans les conventions de codage de Zend / Laminas et permettent de faciliter l'auto-chargement.
* Afin d'être détecter comme un module, il est également obligatoire de créer un fichier Module.php à la racine du dossier module/Jeu/src. Ce fichier contient le code suivant :
``` php
namespace Jeu;

use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}
```
* Sans ce fichier Zend / Laminas ne reconnait pas votre module. 
* Il reste quelques étapes pour que votre module soit pleinement fonctionnel.

## Autochargement 
* L'autochargement se déclare dans le fichier composer.json à la racine du projet
* Il est nécessaire d'indiquer à Zend / Laminas de charger notre projet, pour cela nous rajoutons notre module dans l'autoload :
``` json
"autoload": {
    "psr-4": {
        "Application\\": "module/Application/src/",
        "Jeu\\": "module/Jeu/src/"
    }
},
```
* Puis pour indiquer à Zend que des nouveaux fichiers sont à charger on utilise la commande :
``` shell
composer dump-autoload
```
* Les fichiers du dossier sont maintenant automatiquement chargé par l'application

## Configuration du module

* Afin de déclarer la configuration du module, on définit un fichier de configuration dans le dossier config qui se nomme module.config.php 
* Ce fichier contient :
``` php
namespace Jeu;

use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\JeuController::class => InvokableFactory::class,
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'album' => __DIR__ . '/../view',
        ],
    ],
];
```
* Ce fichier permet de déclarer les controlleurs à instancier et à ajouter au ServiceManager
* Et le view manager permet d'ajouter l'intégralité des vues du modules dans le gestionnaire de vues (pour éviter de les charger une par une)

## Déclarer notre module 

* La dernière étape est de déclarer notre module au ModuleManager du framework.
* Pour cela modifier le fichier qui se trouve à la racine du projet dans config/modules.config.php et rajouter votre module comme dans l'exemple ci-dessous 
``` php
return [
    'Laminas\Form',
    'Laminas\Db',
    'Laminas\Router',
    'Laminas\Validator',
    'Application',
    'Jeu',          // <-- Add this line
];
```

* N'oubliez pas de vérifier dans le navigateur, que votre page s'affiche toujours et qu'il n'y a pas d'erreurs.

-> Félicitations, vous avez créer et configurer votre premier module.
