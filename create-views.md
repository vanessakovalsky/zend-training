# Création des vues associées à notre controlleur 

Cet exercice a pour objectifs de :
* Définir un thème à notre application
* Créer des vues pour afficher du contenu
* Associer des vues à notre controlleur

## Définition du thème
* Créer un fichier layout.phtml dans view/layout/ afin de définir le thème général de notre application.
* Celui-ci peut appeler des feuilles de styles ou un framework comme bootstrap par exemple :
```
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <?php echo $this->headTitle('ZF Lazy loading module') ?>
        <?php echo $this->headMeta()->appendName('viewport', 'width=device-width, initial-scale=1.0') ?>
        <?php echo $this->headLink() ?>
        <?php echo $this->headScript() ?>
    </head>
    <body>
        <div class="navbar navbar-fixed-top">
            <div class="navbar-inner">
                <div class="container">
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                    </a>
                    <a class="brand" href="<?php echo $this->url('home') ?>">Home</a>
                </div>
            </div>
        </div>
        <div class="container">
        <?php echo $this->content; ?>
        <hr>
        <footer>
            My footer
        </footer>
        </div>
    </body>

</html>
```

## Création des vues 

* Les vues se créent dans le dossier prévus à cet effet dans notre module dans Jeu/view/jeu/jeu 
* Nous allons créer 4 fichiers correspondants à notre 4 actions :
* * index.phtml
* * add.phtml
* * edit.phtml
* * delete.phtml
* Le format PHTML est un langage de markup proche du HTML à la différence qu'il permet d'injecter des variables. Le framework se charge alors de remplacer nos variables par du HTML et de renvoyer des pages HTML lors des requêtes HTTP de nos utilisateurs.

* Par exemple, le fichier index.phtml pourrait ressembler à celui-ci :
``` 
<?php
// module/Jeu/view/jeu/Jeu/index.phtml:

$title = 'Mes jeux';
$this->headTitle($title);
?>
<h1><?= $this->escapeHtml($title) ?></h1>
<p>
    <a href="<?= $this->url('jeu', ['action' => 'add']) ?>">Ajouter un jeu</a>
</p>

<table class="table">
<tr>
    <th>Title</th>
    <th>Editeur</th>
    <th>&nbsp;</th>
</tr>
<?php foreach ($jeux as $jeu) : ?>
    <tr>
        <td><?= $this->escapeHtml($jeu->title) ?></td>
        <td><?= $this->escapeHtml($jeu->editor) ?></td>
        <td>
            <a href="<?= $this->url('jeu', ['action' => 'edit', 'id' => $jeu->id]) ?>">Modifier</a>
            <a href="<?= $this->url('jeu', ['action' => 'delete', 'id' => $jeu->id]) ?>">Supprimer</a>
        </td>
    </tr>
<?php endforeach; ?>
</table>
```
* Dans cet exemple nous faisons une boucle sur le tableau contenant la liste des jeux et nous affichons avant le tableau un lien pour ajouter un nouveau jeu, et au sein du tableau un lien pour modifier la fiche d'un jeu et un lien pour supprimer le jeu.
* Créer également les templates pour afficher le jeu, celui pour le modifier et celui pour le supprimer.

## Appel de la vue dans le controlleur

* Au niveau du controlleur, nous devons demander au framework d'appeler la vue crée et lui fournir les arguments attendus. Dans notre exemple il s'agit d'une liste de jeux. 
* Modifier la fonction indexAction du JeuControlleur comme suit :
``` php 
    public function indexAction()
    {
        $listeJeux = [
            ['id' => 1, 'title' => 'Les aventuriers du rail', 'editor' => 'Asmodée'],
            ['id' => 2, 'title' => 'Les aventuriers du rail Europe', 'editor' => 'Asmodée'],
            ['id' => 3, 'title' => 'Les aventuriers du rail Monde', 'editor' => 'Asmodée'],
            ['id' => 4, 'title' => 'Les aventuriers du rail Japon/Italie', 'editor' => 'Asmodée'],
        ];
        return new ViewModel([
            'jeux' => $listeJeux,
        ]);
    }
```
* Ici nous déclarons le tableaux de jeux dans la méthodes et nous le passons à la vue.
* Il n'est pas nécessaire de déclarer le nom de la vue appelé puisque dans le module.config.php nous lui avons demander de faire correspondre le nom de l'action au nom de notre vue, il est donc indispensable de bien faire correspondre le nom des fichiers de vues avec le nom des actions. 

-> Félicitations, vous savez créer des vues et leur passer des arguments depuis les controleurs.