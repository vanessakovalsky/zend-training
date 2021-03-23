# Créer un formulaire et valider les données

Cet exercice a pour objectif :
* De créer un formulaire d'ajout de jeu
* De valider les données
* D'enregistrer les données reçues en base de données

## Installer les composants laminas :
* Il est nécessaire d'avoir l'extension intl de php installer et activer. Vous pouvez le vérifier en faisant php -m 
* Il est nécessaire d'installer les composants laminas utile au formulaire :
```
composer require laminas/laminas-form
composer require laminas/laminas-i18n
```

## Création du formulaire

* Pour créer notre formulaire, nous utilisons le composant Laminas\Form 
* Nous créons un nouveau dossier Form dans le dossier module\Jeu\src\
* Puis nous créons un fichier JeuForm.php avec le contenu suivant :

``` php
<?php
namespace Jeu\Form;

use Laminas\Form\Form;
use Laminas\Form\Element;
use Laminas\Form\Element\Text;
use Laminas\Form\Element\Submit;

class JeuForm extends Form
{
    public function __construct()
    {
        // We will ignore the name provided to the constructor
        parent::__construct('jeu');

        $this->add([
            'name' => 'id',
            'type' => 'hidden',
        ]);

        $this->add([
            'name' => 'title',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Title',
            ],
        ]);
        $this->add([
            'name' => 'editor',
            'type' => Element\Text::class,
            'options' => [
                'label' => 'Editor',
            ],
        ]);
        $this->add([
            'name' => 'submit',
            'type' => Element\Submit::class,
            'attributes' => [
                'value' => 'Go',
                'id'    => 'submitbutton',
            ],
        ]);
    }
}
```
* Il est nécessaire d'ajouter chaque champ du formulaire avec son type et ses attributs.
* Vous pouvez trouvez l'ensemble des types dans la documentation : https://docs.laminas.dev/laminas-form/element/intro/

## Ajouter les valideurs à notre modèle 

* Nous avons besoin d'ajouter des valideurs de données à notre formulaire.
Pour cela nous utilisons laminas-inputfilter directement au niveau du modèle.
* Nous allons ajouter dans notre modèle deux fonctions :
* * setInputFilter : renvoit une exception en cas d'erreur de validation
* * getInputFilter : définit les filtres à appliquer pour chaque champs
* Nous rajoutons dans le module\Jeu\src\Model\Jeu.php les deux fonctions :
``` php
 public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new DomainException(sprintf(
            '%s does not allow injection of an alternate input filter',
            __CLASS__
        ));
    }

    public function getInputFilter()
    {
        if ($this->inputFilter) {
            return $this->inputFilter;
        }

        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'required' => true,
            'filters' => [
                ['name' => ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'title',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'editor',
            'required' => true,
            'filters' => [
                ['name' => StripTags::class],
                ['name' => StringTrim::class],
            ],
            'validators' => [
                [
                    'name' => StringLength::class,
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
            ],
        ]);

        $this->inputFilter = $inputFilter;
        return $this->inputFilter;
    }
```
* La liste des filtres disponible est ici: https://docs.laminas.dev/laminas-filter/intro/ 
* La liste des valideurs disponible est ici : https://docs.laminas.dev/laminas-validator/intro/ 

## Appeler le formulaire dans un controleur 
* Depuis le controleur JeuController, nous allons appeler notre formulaire et effectuer son traitement :
``` php
    public function addAction()
    {
        //Les deux première lignes permette d'instancier le formulaire et de définir la valeur du bouton de soumission
        $form = new JeuForm();
        $form->get('submit')->setValue('Add');

        //nous récupérons la requête, si celle-ci n'utilise pas la méthode POST (envoi de données), nous renvoyons le formulaire vide.
        $request = $this->getRequest();

        if (! $request->isPost()) {
            return ['form' => $form];
        }

        //Si des données ont été envoyer, nous créeons un nouvel objet Jeu, puis utilisons les filtres définis dans le modèles auquel nous soumettons les données reçues

        $jeu = new Jeu();
        $form->setInputFilter($jeu->getInputFilter());
        $form->setData($request->getPost());

        // Nous vérifions si le données envoyées sont valide, si ce n'est pas le cas, nous renvoyons le formulaire

        if (! $form->isValid()) {
            return ['form' => $form];
        }

        // Si les données sont valide, nous hydratons l'objet jeu avec la fonction exchangeArray et utilisons la fonction saveJeu du Depôt (JeuTable)
        $jeu->exchangeArray($form->getData());
        $this->table->saveJeu($jeu);
        // Finalement on redirige vers la liste des jeux
        return $this->redirect()->toRoute('jeu');
    }
```
* Nous pouvons maintenant ajouter notre formulaire dans la vue correspondante.

## Affichage du formulaire d'ajout
* Pour afficher le formulaire d'ajout, nous modifions le fichier module/Jeu/view/jeu/jeu/add.phtml pour ajouter notre formulaire avec les éléments bootstrap d'affichage :
``` php
<?php
$title = 'Ajouter un nouveau jeu';
$this->headTitle($title);
?>
<h1><?= $this->escapeHtml($title) ?></h1>
<?php
// This provides a default CSS class and placeholder text for the title element:
$jeu = $form->get('title');
$jeu->setAttribute('class', 'form-control');
$jeu->setAttribute('placeholder', 'Titre du jeu');

// This provides a default CSS class and placeholder text for the artist element:
$editor = $form->get('editor');
$editor->setAttribute('class', 'form-control');
$editor->setAttribute('placeholder', 'Editeur');

// This provides CSS classes for the submit button:
$submit = $form->get('submit');
$submit->setAttribute('class', 'btn btn-primary');

$form->setAttribute('action', $this->url('jeu', ['action' => 'add']));
$form->prepare();

echo $this->form()->openTag($form);
?>
<?php // Wrap the elements in divs marked as form groups, and render the
      // label, element, and errors separately within ?>
<div class="form-group">
    <?= $this->formLabel($jeu) ?>
    <?= $this->formElement($jeu) ?>
    <?= $this->formElementErrors()->render($jeu, ['class' => 'help-block']) ?>
</div>

<div class="form-group">
    <?= $this->formLabel($editor) ?>
    <?= $this->formElement($editor) ?>
    <?= $this->formElementErrors()->render($editor, ['class' => 'help-block']) ?>
</div>

<?php
echo $this->formSubmit($submit);
echo $this->formHidden($form->get('id'));
echo $this->form()->closeTag();
```
* Nous récupérons les différents champs avec la fonction get('nomduchamp') sur l'objet $form
* Puis nous définissons des attributs comme des classes CSS ou des placeholder
* Une fois les éléments récupérés, nous affichons les éléments avec différentes fonctions comme formLabel, formElement ou formElementErrors
* Le balises de formulaire en HTML sont créés avec les fonction form()->openTag() et form()->closeTag() 
* N'oubliez pas d'ajouter le bouton de soummission avec la fonction formSubmit. 
* Ainsi que d'ajouter si nécessaire les champs cachés (ici 'id')
* Les différentes fonctions accessibles dans une vue sont listées ici : https://docs.laminas.dev/laminas-form/helper/abstract-helper/ 

## Créer un formulaire de modiciation d'un objet 
* Pour la moficiation, nous allons également appeler notre formulaire dans la methode editAction du controlleur JeuController.
* Avant d'appeler le formulaire, nous chargeons l'objet jeu à partir de son id à partir de notre méthode getJeu($id).
* Une fois le form crée, nous lui passons les données du modèle avec la fonction bind(). Cette méthode agit dans les deux sens :
* * lors de l'instanciation du formulaire, on associe l'objet du modèle au champs de formulaire
* * après la validation du formulaire, cela permet de mettre à jour l'objet du modèle
* Pour fonctionner la méthode bind a besoin d'une méthode supplémentaire dans le modèle Jeu : getArrayCopy() qui permet de retourner une copie de l'objet qui est passé au formulaire et permet avec la fonction exchangeArray de rehydrater automatiquement notre objet lors du traitement du formulaire :
``` php
    public function getArrayCopy()
    {
        return [
            'id'     => $this->id,
            'editor' => $this->editor,
            'title'  => $this->title,
        ];
    }
```
* Voici le code de la fonction de modification du Controller :
``` php
 public function editAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);

        if (0 === $id) {
            return $this->redirect()->toRoute('jeu', ['action' => 'add']);
        }

        // Retrieve the jeu with the specified id. Doing so raises
        // an exception if the jeu is not found, which should result
        // in redirecting to the landing page.
        try {
            $jeu = $this->table->getJeu($id);
        } catch (\Exception $e) {
            return $this->redirect()->toRoute('jeu', ['action' => 'index']);
        }

        $form = new JeuForm();
        $form->bind($jeu);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        $viewData = ['id' => $id, 'form' => $form];

        if (! $request->isPost()) {
            return $viewData;
        }

        $form->setInputFilter($jeu->getInputFilter());
        $form->setData($request->getPost());

        if (! $form->isValid()) {
            return $viewData;
        }

        $this->table->saveJeu($jeu);

        // Redirect to jeux list
        return $this->redirect()->toRoute('jeu', ['action' => 'index']);
    }
```
* La vue est la même que celle de l'ajout à l'exception du Titre de la page qui est à adapté, et de l'action du formulaire qui doit appeler l'action edit et passer l'id en paramètre :
``` php
$form->setAttribute('action', $this->url('jeu', [
    'action' => 'edit',
    'id'     => $id,
]));
```

## Créer un formulaire simple depuis une vue pour la suppression
* Cette dernière partie va nous permettre de créer un formulaire de confirmation de suppression simple directement depuis une vue sans passer par le composant form.
* On commencer par ajouter le traitement du formulaire à notre deleteAction de notre controlleur :
``` php
  public function deleteAction()
    {
        $id = (int) $this->params()->fromRoute('id', 0);
        if (!$id) {
            return $this->redirect()->toRoute('jeu');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost('del', 'No');

            if ($del == 'Yes') {
                $id = (int) $request->getPost('id');
                $this->table->deleteJeu($id);
            }

            // Redirect to list of jeux
            return $this->redirect()->toRoute('jeu');
        }

        return [
            'id'    => $id,
            'jeu'   => $this->table->getJeu($id),
        ];
```
* Dans cette méthode on teste si on a reçu des données avec la méthode isPost(), puis en fonction des données reçu on supprime en appelant la fonction deleteJeu de notre JeuTable. 
* Le formulaire se déclare simplement sous forme d'un formulaire HTML dans la vue delete.phtlm:
``` php
<?php
// module/Jeu/view/jeu/jeu/delete.phtml:

$title = 'Delete jeu';
$url   = $this->url('jeu', ['action' => 'delete', 'id' => $id]);

$this->headTitle($title);
?>
<h1><?= $this->escapeHtml($title) ?></h1>

<p>
    Are you sure that you want to delete
    "<?= $this->escapeHtml($jeu->title) ?>" by
    "<?= $this->escapeHtml($jeu->artist) ?>"?
</p>

<form action="<?= $url ?>" method="post">
<div class="form-group">
    <input type="hidden" name="id" value="<?= (int) $jeu->id ?>" />
    <input type="submit" class="btn btn-danger" name="del" value="Yes" />
    <input type="submit" class="btn btn-success" name="del" value="No" />
</div>
</form>
```
* Ici on appelle aucune fonction spécifique seulement du HTML classique avec l'appel à notre route.

-> Félicitations, vous savez maintenant créer et traiter des formulaires, avec ou sans le composant Form.
