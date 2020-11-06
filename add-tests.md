# Ajouter des tests unitaires à notre application

Cet exercice a pour objectif :
* de créer des tests unitaires pour le controlleur JeuController
* De créer des tests unitaires pourl le modèle Jeu
* De configurer et de lancer les tests de notre module

## Création des tests du controleur
* A l'interieur du dossier module on ajoute un dossier test et dans ce dossier un dossier Controller
* Puis nous créons un fichier qui va executer les tests de notre controlleur :
``` php
<?php

namespace JeuTest\Controller;

use Jeu\Controller\JeuController;
use Laminas\Stdlib\ArrayUtils;
use Laminas\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class JeuControllerTest extends AbstractHttpControllerTestCase
{
    protected $traceError = false;

    protected function setUp() : void
    {
        $configOverrides = [];
        $this->setApplicationConfig(ArrayUtils::merge(
        // Grabbing the full application configuration:
        include __DIR__ . '/../../../../config/application.config.php',
        $configOverrides
        ));
        parent::setUp();
    }
    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/jeu');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('Jeu');
        $this->assertControllerName(JeuController::class);
        $this->assertControllerClass('JeuController');
    }
}
```
* La fonction setUp permet d'initialiser l'application en lui donnant le fichier de configuration
* Les fonction de tests commencent toute par le mot clé test et reprenne à mininimum le nom de la fonction en camel case.
* La fonction testIndexActionCanBeAccessed() effectue les actions suivantes :
* *  dispatch, permet d'atteindre la route donnée
* * assertResponseStatusCode, vérifie le code de réponse de la page
* * assertModuleName, vérifie le nom du module
* * assertControllerName, vérifie le nom du controleur
* * asserControllerClass, vérifie la classe appelée
* Chaque fonction assert permet de vérifier un point précis. La liste des asserts disponible pour Laminas est ici : https://docs.laminas.dev/laminas-test/assertions/ et celle accessible à travers PHPUnit est ici : https://phpunit.readthedocs.io/fr/latest/assertions.html 

## Utiliser le service manager dans les tests

* Notre controlleur utilise le service manager, mais celui-ci n'est pas instancier.
* Pour cela nous allons définir une nouvelle fonction pour l'instancier et créer un mock de l'objet JeuTable 
``` php
protected function configureServiceManager(ServiceManager $services)
{
    $services->setAllowOverride(true);
    $services->setService('config',
    $this->updateConfig($services->get('config')));
    $services->setService(JeuTable::class,
    $this->mockJeuTable()->reveal());
    $services->setAllowOverride(false);
}
protected function updateConfig($config)
{
    $config['db'] = [];
    return $config;
}
protected function mockAlbumTable()
{
    $this->jeuTable = $this->prophesize(JeuTable::class);
    return $this->jeuTable;
}
```
* Il reste à ajouter l'appel à la fonction configureServiceManager dans la fonction setUp de notre controller de test.

## Tester l'envoi de données depuis le formulaire
* Pour vérifier l'envoi de données et leur enregistrement on peut rajouter une fonction de POST qui va créer un objet avec des données puis l'envoyer à la route d'ajout et enfin vérifier que la redirection fonctionne  :
``` php
public function testAddActionRedirectsAfterValidPost()
{
    $this->albumTable
    ->saveAlbum(Argument::type(Jeu::class))
    ->shouldBeCalled();
    $postData = [
    'title' => 'Spendor',
    'editor' => 'Space Cowboys',
    'id' => '',
    ];
    $this->dispatch('/jeu/add', 'POST', $postData);
    $this->assertResponseStatusCode(302);
    $this->assertRedirectTo('/jeu');
}
```
* Vous pouvez maintenant ajouter les tests pour la suppression par exemple, en appelant la suppression du jeu que vous venez de créer, et en vérifiant qu'il n'est plus dans la liste :) 

## Tester notre modèle de données
* On rajoute un dossier Model dans le dossier test de notre module
* Puis on crée un fichier JeuTest.php qui contient : 
``` php
<?php
namespace JeuTest\Model;
use Jeu\Model\Jeu;
use PHPUnit\Framework\TestCase;

class AlbumTest extends TestCase
{
    public function testInitialJeuValuesAreNull()
    {
        $jeu = new Jeu();
        $this->assertNull($jeu->editor, '"editor" should be null by default');
        $this->assertNull($jeu->id, '"id" should be null by default');
        $this->assertNull($jeu->title, '"title" should be null by default');
    }

    public function testExchangeArraySetsPropertiesCorrectly()
    {
        $jeu = new Jeu();
        $data = [
        'editor' => 'Space Cowboys',
        'id' => 123,
        'title' => 'Tea For 2'
        ];
        $jeu->exchangeArray($data);
        $this->assertSame(
        $data['editor'],
        $jeu->editor,
        '"editor" was not set correctly'
        );
        $this->assertSame(
        $data['id'],
        $jeu->id,
        '"id" was not set correctly'
        );
        $this->assertSame(
        $data['title'],
        $jeu->title,
        '"title" was not set correctly'
        );
    }

    public function testExchangeArraySetsPropertiesToNullIfKeysAreNotPresent()
    {
        $jeu = new Jeu();
        $jeu->exchangeArray([
        'editor' => 'Space Cowboys',
        'id' => 123,
        'title' => 'Tea For 2'
        ]);
        $jeu->exchangeArray([]);
        $this->assertNull($jeu->editor, '"artist" should default to null');
        $this->assertNull($jeu->id, '"id" should default to null');
        $this->assertNull($jeu->title, '"title" should default to null');
    }

    public function testGetArrayCopyReturnsAnArrayWithPropertyValues()
    {
        $jeu = new Jeu();
        $data = [
        'editor' => 'some artist',
        'id' => 123,
        'title' => 'some title'
        ];
        
        $jeu->exchangeArray($data);
        $copyArray = $jeu->getArrayCopy();
        $this->assertSame($data['editor'], $copyArray['editor'], '"editor" was not set correctly');
        $this->assertSame($data['id'], $copyArray['id'], '"id" was not set correctly');
        $this->assertSame($data['title'], $copyArray['title'], '"title" was not set correctly');

    }
    public function testInputFiltersAreSetCorrectly()
    {
        $jeu = new Jeu();
        $inputFilter = $jeu->getInputFilter();
        $this->assertSame(3, $inputFilter->count());
        $this->assertTrue($inputFilter->has('editor'));
        $this->assertTrue($inputFilter->has('id'));
        $this->assertTrue($inputFilter->has('title'));
    }
}
```
* Les tests du modèles sont les suivants :
* * le premier test, vérifie que lors de la création d'un obket ses propriétés sont bien à null.
* * Le second vérifie que lors de l'hydratation de l'objet avec exchange array, les propriétés de l'objet sont bien remplies avec les données passer à la méthode
* * Le troisième vérifie que si aucune données n'est passé à exchangeArray, les proprietés sont bien à null
* * Le quatrième vérifie que la méthode copyArray renvoit bien un tableau avec les valeurs de l'objet copié
* * Le dernier vérifie si les filtres d'entrées sont bien récupérés.

* Nous pouvons également tester le dépôt (Table) en rajoutant un fichier JeuTableTest.php dans le dossier test/Model
``` php
<?php
namespace JeuTest\Model;
use Jeu\Model\JeuTable;
use Jeu\Model\Jeu;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Laminas\Db\ResultSet\ResultSetInterface;
use Laminas\Db\TableGateway\TableGatewayInterface;
class JeuTableTest extends TestCase
{
    protected function setUp() : void
    {
    $this->tableGateway = $this->prophesize(TableGatewayInterface::class);
    $this->jeuTable = new JeuTable($this->tableGateway->reveal());
    }
    public function testFetchAllReturnsAllJeux()
    {
    $resultSet = $this->prophesize(ResultSetInterface::class)->reveal();
    $this->tableGateway->select()->willReturn($resultSet);
    $this->assertSame($resultSet, $this->jeuTable->fetchAll());
    }
    public function testCanDeleteAnJeuByItsId()
    {
    $this->tableGateway->delete(['id' => 123])->shouldBeCalled();
    $this->jeuTable->deleteJeu(123);
    }
    public function testSaveJeuWillUpdateExistingAlbumsIfTheyAlreadyHaveAnId()
    {
        $jeuData = [
        'id' => 123,
        'editor' => 'Space Cowboys',
        'title' => 'Splendor',
        ];
        $jeu = new Jeu();
        $jeu->exchangeArray($jeuData);
        $resultSet = $this->prophesize(ResultSetInterface::class);
        $resultSet->current()->willReturn($jeu);
        $this->tableGateway
        ->select(['id' => 123])
        ->willReturn($resultSet->reveal());
        $this->tableGateway
        ->update(
        array_filter($jeuData, function ($key) {
        return in_array($key, ['editor', 'title']);
        }, ARRAY_FILTER_USE_KEY),
        ['id' => 123]
        )->shouldBeCalled();
    }
    
}
```
* 
## Configurer ses tests et les lancer

* Comme nous avons déclarer un namespace JeuTest, il faut le déclarer à l'autoload du composer.json
``` json
"autoload-dev": {
    "psr-4": {
        "ApplicationTest\\": "module/Application/test/",
        "JeuTest\\": "module/Jeu/test/"
    }
}
```
* N'oubliez pas de lancer la commande composer dump-autoload pour recharger les fichiers
* Ensuite nous rajoutons nos tests en les déclarant comme une suite de test, dans le fichier à la racine phpunit.xml.dist 
``` xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit colors="true">
    <testsuites>
            <testsuite name="Laminas MVC Application
        Test Suite">
            <directory>./module/Application/test</directory>
        </testsuite>
            <testsuite name="Jeu">
            <directory>./module/Jeu/test</directory>
        </testsuite>
    </testsuites>
</phpunit>
```
* Enfin pour lancer nos tests nous utilisons la commande :
``` shell
./vendor/bin/phpunit --testsuite Jeu
```

->  Félicitations vous savez écrire des tests, les configurer et les lancer