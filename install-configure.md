# Installation et creation d'une application Zend

Cet exercice a pour objectifs :
* installer l'environnement Zend Framework
* créer une première application 

## Pré-requis
Pour réaliser cet exercice vous avez besoin : 
* d'un serveur web comprenant : PHP , une BDD SQL et un serveur Web comme Apache ou Nginx
* d'un éditeur de code comme Visual Studio Code ou PHPStorm


## Installation de composer 

* Récupérer composer en fonction de votre OS : https://getcomposer.org/download/
* Ajouter Composer dans le path : https://getcomposer.org/doc/00-intro.md
* Vérifier le fonctionnement avec la commande :
```
composer --version
```

## Création de la première application 

* On peut maintenant lancer le téléchargement de laminas avec la commande :
``` 
composer create-project -s dev laminas/laminas-mvc-skeleton path/to/install
```
* Le terminal vous pose alors une question :
```
Do you want a minimal install (no optional packages)? Y/n
```
* Pour des questions de failié répondre Y ce uqi permet d'installer de nombreuses options par défaut
* Dans le cas ou vous répondrez No d'autres questions vous sont alors posées pour installer un à un différents composants.
* Quel est l'arborescence de base de la version installée ? 
* Rendez vous sur localhost/nomdudossier et vous devriez avoir une image comme celle-ci :
[https://docs.laminas.dev/tutorials/images/user-guide.skeleton-application.hello-world.png]

## Configurer Apache

* Créer un fichier /etc/apache2/sites-enabled/laminas-mvc-tutorial et ajouter le contenu suivant :
```
<VirtualHost *:80>
    ServerName laminas-mvc-tutorial.localhost
    DocumentRoot /path/to/laminas-mvc-tutorial/public
    SetEnv APPLICATION_ENV "development"
    <Directory /path/to/laminas-mvc-tutorial/public>
        DirectoryIndex index.php
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
* Ajouter dans votre fichier host la ligne suivante (le fichier se trouve sur Linux dans /etc/hosts et sur Windows dans c:\windows\system32\drivers\etc\hosts ) :
```
127.0.0.1 laminas-mvc-tutorial.localhost localhost
```
* Rédémarrer Apache
* Ouvrir un navigateur et aller à http://laminas-mvc-tutorial.localhost/ 
* La même page que précédement doit alors être affichée mais avec l'url définie

## Activation du mode développement 

### Afficher les erreurs
* Ajouter l'affichage des erreurs en remplaçant le contenu du fichier index.php à la racine de votre projet par le contenu suivant :
``` php
use Laminas\Mvc\Application;
use Laminas\Stdlib\ArrayUtils;

/**
 * Display all errors when APPLICATION_ENV is development.
 */
if ($_SERVER['APPLICATION_ENV'] === 'development') {
    error_reporting(E_ALL);
    ini_set("display_errors", '1');
}

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server') {
    $path = realpath(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
    if (__FILE__ !== $path && is_file($path)) {
        return false;
    }
    unset($path);
}

// Composer autoloading
include __DIR__ . '/../vendor/autoload.php';

if (! class_exists(Application::class)) {
    throw new RuntimeException(
        "Unable to load application.\n"
        . "- Type `composer install` if you are developing locally.\n"
        . "- Type `vagrant ssh -c 'composer install'` if you are using Vagrant.\n"
        . "- Type `docker-compose run laminas composer install` if you are using Docker.\n"
    );
}

// Retrieve configuration
$appConfig = require __DIR__ . '/../config/application.config.php';
if (file_exists(__DIR__ . '/../config/development.config.php')) {
    $appConfig = ArrayUtils::merge($appConfig, require __DIR__ . '/../config/development.config.php');
}

// Run the application!
Application::init($appConfig)->run();
```

* Cela permet d'afficher l'ensemble des erreurs et sera utile au développement.

### Activer le mode développement 

* Nous allons maintenant activer le mode développement.
* Pour cela copier le fichier config/development.config.php.dist dans config/development.config.php
* Ainsi que le fichier config/autoload/development.local.php.dist dans config/autoload/development.local.php
* Cela permet de donner les options de configurations nécessaire au développement avec Zend/Laminas
* Puis activer le mode développement avec la commande :
``` shell
composer development-enable
```

-> Félicitations vous avez installer et configurer votre projet Zend / Laminas, et vous allez pouvoir commencer à développer.