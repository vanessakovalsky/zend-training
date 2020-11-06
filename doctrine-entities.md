# Créer les entités et relations Doctrine

Cet exercice a pour objectif :
* De créer deux entité doctrine
* De créer une relation entre ses deux entités
* D'utiliser nos entités dans un controlleur

## Installer et appeler Doctrine 

* Pour installer le composant doctrine on utilise composer :
```
composer require doctrine/doctrine-orm-module
```

* On ajoute alors DoctrineModule et DoctrineOrmModule à la liste des modules chargés (dans config/modules.config.php)
``` php
return [
    // Add the Doctrine integration modules.
    'DoctrineModule',
    'DoctrineORMModule',      
    //...
);
```
* La configuration à la base de données se fait dans le fichier config/autoload/global.php ou dans le config/autoload/local.php avec le tableau suivant :
``` php
use Doctrine\DBAL\Driver\PDOMySql\Driver as PDOMySqlDriver;

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driverClass' => PDOMySqlDriver::class,
                'params' => [
                    'host'     => '127.0.0.1',                    
                    'user'     => 'blog',
                    'password' => '<password>',
                    'dbname'   => 'blog',
                ]
            ],            
        ],        
    ],
];
``` 

## Déclarer nos entités Jeux et Collections

* On rajoute dans le dossier module\Jeu\src un dossier Entity.
* Dans ce dossier on crée un fichier appelé Jeu.php qui contient notre entité :
``` php
<?php
namespace Jeu\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="jeu")
 */
class Jeu 
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(name="id")   
   */
  protected $id;

  /** 
   * @ORM\Column(name="title")  
   */
  protected $title;

  /** 
   * @ORM\Column(name="content")  
   */
  protected $editor;  
}
```
* La création de notre entité s'appuie sur les annotations suivante :
* * @ORM\Doctrine : indique qu'on utilise Doctrine
* * @ORM\Table(name="jeu") : indique la table de la bdd à utiliser
* * @ORM\Id : indique qu'il s'agit de la clé primaire de l'entité
* * @ORM\GeneratedValue : indique que la valeur de cette propriété est générée automatiquement
* * @ORM\Column(name="*") : indique le nom de la colonne dans la table de base de donnée qui correspond à cette propriété.
* La liste complète des annotations disponible pour Doctrine est disponible ici : https://doctrine-project.org/projects/doctrine-orm/en/latest/reference/annotations-reference.html 

* Une fois l'ensemble de nos propriétés crées nous rajoutons des getter et des setter pour chaque propriété dans notre entité :
``` php
<?php
namespace Jeu\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * This class represents a single jeu.
 * @ORM\Entity
 * @ORM\Table(name="jeu")
 */
class Jeu 
{
  /**
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(name="id")   
   */
  protected $id;

  /** 
   * @ORM\Column(name="title")  
   */
  protected $title;

  /** 
   * @ORM\Column(name="editor")  
   */
  protected $editor;


  
  // Returns ID of this post.
  public function getId() 
  {
    return $this->id;
  }

  // Sets ID of this post.
  public function setId($id) 
  {
    $this->id = $id;
  }

  // Returns title.
  public function getTitle() 
  {
    return $this->title;
  }

  // Sets title.
  public function setTitle($title) 
  {
    $this->title = $title;
  }

  // Returns editor.
  public function getEditor() 
  {
    return $this->editor;
  }

  // Sets editor.
  public function setEditor($editor) 
  {
    $this->editor = $editor;
  }
    
}
```

* Crée également une entité User pour l'utilisateur et une entité Collection avec les paramètres adéquat (nous rajouterons ensuite les relations entre ces différentes entités);
* Il est nécessaire de créer la table depuis nos entités (au moins si celle-ci n'existe pas), cela peut se faire avec une commande directement :

``` shell
php public/index.php doctrine:schema:update --force
```
* Cette commande va mettre à jour le schéma de la base de données et ajouter les tables manquantes ou modifier celle qui existe pour les faire correspondre à notre entité.

## Définir des relations

* Nos entités ont les relations suivantes :
*  * Collection et jeu ont une relation de type many_to_many (plusieurs collections peuvent contenir plusieurs jeux)
* * Collection et User ont une relation de type one-to-many (chaque utilisateur peut avoir une collection) 

* Les relations se définissent aux travers d'annotation.
* Commençons avec la relations entre collection et jeu, cela donne au niveau de l'entité collection :
``` php
<?php
//...
use Doctrine\Common\Collections\ArrayCollection;

class Collection 
{
  // ...
  
  /**
   * @ORM\ManyToMany(targetEntity="\Jeu\Entity\Jeu", mappedBy="collections")
   */
  protected $jeux;
    
  // Constructor.
  public function __construct() 
  {        
    $this->jeux = new ArrayCollection();        
  }
  
  // Returns posts associated with this tag.
  public function getJeux() 
  {
    return $this->jeux;
  }
    
  // Adds a post into collection of posts related to this tag.
  public function addJeu($jeu) 
  {
    $this->jeux[] = $jeu;        
  }
}
```
* * On définit sur notre propriétés jeux que c'est uen relation de type ManyToMany, qui a pour entité lié Jeu, et qui s'appuie sur le champ collections (que l'on va rajouté dans l'entité Jeu juste après)
* * On définit alors dans le constructure un objet ArrayCollection, qui permet d'avoir un tableau d'objet (ici de jeu), une fonction getJeux qui renvoit les jeux d'une collection et une fonction addJeu qui permet d'ajouter un objet jeu à notre tableau d'objets.

* On ajoute sur l'entité jeu la propriété collections correspondante :
``` php
<?php
//...
use Application\Entity\Tag;

//...
class Jeu 
{
  //...
    
  /**
   * @ORM\ManyToMany(targetEntity="\Application\Entity\Collection", inversedBy="jeux")
   */
  protected $collections;
    
  // Constructor.
  public function __construct() 
  { 
    //...  
    $this->collections = new ArrayCollection();        
  }

  // Returns collections for this post.
  public function getCollections() 
  {
    return $this->collections;
  }      
    
  // Adds a new tag to this post.
  public function addCollection($collection) 
  {
    $this->collections[] = $collection;        
  }
    
  // Removes association between this jeu and the given collection.
  public function removeCollectionAssociation($collection) 
  {
    $this->collections->removeElement($collection);
  }
}
```
* Ici aussi on utilie les annotations pour définir la relation.

* Ajouter également la relation entre collection et user sur nos entités.

* N'oubliez pas de mettre à jour le schéma de base après ces ajouts de propriétés avec la commande vue précédemment.

## Utiliser nos entités dans un controlleur

* Nous allons maintenant utiliser nos entités à la place de notre modèle dans le controleur JeuController.
* Pour cela il faut injecter au constructeur l'entityManager de doctrine qui va nous permettre d'interagir avec nos entités .
* Dans le fichier Module on remplace la fonction getControllerConfig() par la suivante : 
``` php
   public function getControllerConfig()
    {
        return [
            'factories' => [
                Controller\JeuController::class => function($container) {
                    return new Controller\JeuController(
                        $container->get('doctrine.entitymanager.orm_default'),
                        $container->get(Service\PermissionService::class),
                    );
                },
            ],
        ];
    }
```

* Au niveau du controlleur on modifie également le constructeur :
``` php
    public function __construct($entityManager, PermissionService $permissions)
    {
        $this->em = $entityManager;
        $this->permission = $permissions;
    }
```

* Au niveau des fonctions, commençons avec la fonction fetchAll qui devient :
``` php
    public function indexAction(){


        return new ViewModel([
            'jeux' => $this->em->getRepository(Jeu::class)->findAll(),
        ]);

    }
```
* On utilise ici le repository par défaut de Doctrine qui nous permet d'accèder à des fonctions de requêtes sur n'importe quel entité.
* La méthode findAll renvoit l'ensemble des résultats pour l'entité choisie.

* Pour l'ajout on remplace la fonction addAction par : 
``` php
$role = 'membre';
        if($this->permission->isAllowed($role, null, 'publish')){
            //Les deux première lignes permette d'instancier le formulaire et de définir la valeur du bouton de soumission
        $form = new JeuForm();
        $form->get('submit')->setValue('Add');

        //nous récupérons la requête, si celle-ci n'utilise pas la méthode POST (envoi de données), nous renvoyons le formulaire vide.
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return ['form' => $form];
        }

        //Si des données ont été envoyer, nous créeons un nouvel objet Jeu, puis utilisons les filtres définis dans le modèles auquel nous soumettons les données reçues

        $jeu = new Jeu();
        $form->setData($request->getPost());

        // Nous vérifions si le données envoyées sont valide, si ce n'est pas le cas, nous renvoyons le formulaire

        if (!$form->isValid()) {
            return ['form' => $form];
        }

        $post = new Jeu();
        $post->setTitle($data['title']);
        $post->setEditor($data['editor']);        

        // Add the entity to entity manager.
        $this->em->persist($post);

        // Apply changes to database.
        $this->em->flush();
        // Finalement on redirige vers la liste des jeux
        return $this->redirect()->toRoute('jeu');
    }
```

* Pour la fonction getJeu vous pouvez utiliser la méthode find associer à l'EntityRepository (comme pour la m&thode findALl())
* Pour la suppression, vous pouvez utiliser la méthode remove (comme la méthode persist mais avec un id seulement);

-> Félicitations vous savez maintenant créer des entités et des relations et les appeler dans votre controleur.