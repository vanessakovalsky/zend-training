# Création d'un modèle et utilisation dans les controlleurs

Cet exercice a pour objectif :
* de déclarer un modèle pour lire / enregistrer nos données dans une base de données
* d'utiliser le modèle dans nos controleurs 

## Créer la BDD
* Utiliser PHPmyAdmin ou la ligne de commande mysql pour créer une base de données.
* Voici un fichier pour créer la première table :
``` SQL 
CREATE TABLE jeu (id INTEGER PRIMARY KEY AUTOINCREMENT, editor varchar(100) NOT NULL, title varchar(100) NOT NULL);
INSERT INTO jeu (editor, title) VALUES ('Asmodée', 'Les aventuriers du rail');
INSERT INTO jeu (editor, title) VALUES ('Asmodée', 'Les aventuriers du rail Europe');
INSERT INTO jeu (editor, title) VALUES ('Asmodée', 'Les aventuriers du rail Monde');
INSERT INTO jeu (editor, title) VALUES ('Asmodée', 'Les aventuriers du rail Japon/Italie');
```
* Cela permet d'avoir une table de base et quelques entrées (vous pouvez enrichir cette table et les données avec l'ensemble des données entendues)

## Déclarer le modèle 
* Le modèle est la déclaration du modèle de stockage de vos données, il est donc de votre ressort de savoir ce que vous souhaitez comme modèle de données, et les types de données à stocker dans celui-ci.
* Les fichiers de modèle sont créés dans module/Jeu/src/Model/
* Voici par exemple le modèle correspondant à notre Jeu :
``` php
namespace Jeu\Model;

class Jeu
{
    public $id;
    public $editor;
    public $title;

    public function exchangeArray(array $data)
    {
        $this->id     = !empty($data['id']) ? $data['id'] : null;
        $this->editor = !empty($data['editor']) ? $data['editor'] : null;
        $this->title  = !empty($data['title']) ? $data['title'] : null;
    }
}
```
* La méthode exchangeArray permet d'hydrater un objet depuis un tableau. Ce tableau utilise le TableGateway qui permet de définir des méthodes pour obtenir l'ensemble des objets du modèle, où un seul objet ou d'enregistrer une commande.
* Nous allons déclarer maintenant la TableGateway utilisé par notre modèle :
``` php
namespace Jeu\Model;

use RuntimeException;
use Laminas\Db\TableGateway\TableGatewayInterface;

class JeuTable
{
    private $tableGateway;

    public function __construct(TableGatewayInterface $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll()
    {
        return $this->tableGateway->select();
    }

    public function getJeu($id)
    {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(['id' => $id]);
        $row = $rowset->current();
        if (! $row) {
            throw new RuntimeException(sprintf(
                'Could not find row with identifier %d',
                $id
            ));
        }

        return $row;
    }

    public function saveJeu(Jeu $jeu)
    {
        $data = [
            'editor' => $jeu->editor,
            'title'  => $jeu->title,
        ];

        $id = (int) $jeu->id;

        if ($id === 0) {
            $this->tableGateway->insert($data);
            return;
        }

        try {
            $this->getJeu($id);
        } catch (RuntimeException $e) {
            throw new RuntimeException(sprintf(
                'Cannot update jeu with identifier %d; does not exist',
                $id
            ));
        }

        $this->tableGateway->update($data, ['id' => $id]);
    }

    public function deleteJeu($id)
    {
        $this->tableGateway->delete(['id' => (int) $id]);
    }
}
```
* Ce TableGateway sert donc à définir les principales fonctions que nous allons utiliser pour récupérer nos données, les enregistrer et les supprimer. 

## Injecter notre modèle et sa TableGateway dans le Service Manager
* Afin d'utiliser toujours la même instance de notre JeuTable, nous allons l'injecter dans le ServiceManager
* Cela se fait dans le fichier Module.php qui est à la racine de notre module dans le dossier src, au niveau de la fonction getServiceConfig() qui est automatiquement appelée par le ModuleManager.
``` php
namespace Jeu;

// Add these import statements:
use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    // getConfig() method is here

    // Add this method:
    public function getServiceConfig()
    {
        return [
            'factories' => [
                Model\JeuTable::class => function($container) {
                    $tableGateway = $container->get(Model\JeuTableGateway::class);
                    return new Model\JeuTable($tableGateway);
                },
                Model\JeuTableGateway::class => function ($container) {
                    $dbAdapter = $container->get(AdapterInterface::class);
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Jeu());
                    return new TableGateway('jeu', $dbAdapter, null, $resultSetPrototype);
                },
            ],
        ];
    }
}
```
* Cette méthode renvoit un tableau de factory qui est mergé par le moduleManager avant d'être passé au ServiceManager
* Notre modèle est maintenant déclaré, et injecter, il est donc prêt à être utilisé

## Configuration de la connexion à la BDD
* La configuration de la connexion à notre db se fait dans le fichier config.autoload.php situé dans la dossier config
* Il est nécessaire de définir son adaptateur pour lui donné les bons paramètres de connexions, exemple ci-dessous avec MySQL :
``` php
$adapter = new Laminas\Db\Adapter\Adapter([
    'driver'   => 'Mysqli',
    'database' => 'laminas_db_example',
    'username' => 'developer',
    'password' => 'developer-password',
]);
```
* Voir la documentation pour les autres adaptateurs : https://docs.laminas.dev/laminas-db/adapter/ 

## Utiliser le modèle dans un controlleur
* Pour commencer nous injectons dans le constructeur le service du modèle via sa TableGateway :
``` php
namespace Jeu\Controller;

// Add the following import:
use Jeu\Model\JeuTable;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class JeuController extends AbstractActionController
{
    // Add this property:
    private $table;

    // Add this constructor:
    public function __construct(JeuTable $table)
    {
        $this->table = $table;
    }

    /* ... */
}
``` 
* Cela permet de disposer du modèle dans le controlleur et de l'utiliser.
* Il est nécessaire d'injecter le service à notre controlleur, comme pour le modèle cela se passe dans le fichier Module.php dans le dossier src de notre module :
``` php
namespace Jeu;

use Laminas\Db\Adapter\AdapterInterface;
use Laminas\Db\ResultSet\ResultSet;
use Laminas\Db\TableGateway\TableGateway;
use Laminas\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface
{
    // getConfig() and getServiceConfig() methods are here

    // Add this method:
    public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\JeuController::class => function($container) {
                    return new Controller\JeuController(
                        $container->get(Model\JeuTable::class)
                    );
                },
            ],
        ];
    }
}
```
* Afin de pouvoir utiliser notre controlleur, nous avions ajouter une factory dans la config de notre module dans config/module.config.php, celle-ci n'étant plus utile nous pouvons la supprimer :
``` php
<?php
namespace Jeu;

// Remove this:
use Laminas\ServiceManager\Factory\InvokableFactory;

return [
    // And remove the entire "controllers" section here:
    'controllers' => [
        'factories' => [
            Controller\JeuController::class => InvokableFactory::class,
        ],
    ],

    /* ... */
];
```
* Enfin dans notre controlleur, remplaçons le tableau de données en dur de la fonction index par l'utilisation de nos données en BDD via le modèle :
``` php
// module/Album/src/Controller/AlbumController.php:
// ...
    public function indexAction()
    {
        return new ViewModel([
            'jeux' => $this->table->fetchAll(),
        ]);
    }
// ...
```
* Vous pouvez également utiliser les fonctions getJeu() dans l'affichage d'un jeu ou la fonction delete du modèle.

-> Félicitation vous savez maintenant créer un modèle, le déclarer, l'injecter et l'utiliser dans un controleur.