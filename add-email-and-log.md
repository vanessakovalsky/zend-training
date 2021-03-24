# Envoyer des emails et enregistrer des logs

Cet exercice a pour objectif :
* d'envoyer un email au propriétaire du jeu
* d'enregistrer des logs supplémentaires pour savoir ce qui est fait dans l'application


## Installation des composants 

* Pour envoyer des mails ,Laminas passe par le composant laminas-mail, pour l'installer :
```
composer require laminas/laminas-mail
```
* Pour la gestion des logs, Laminas utilise laminas-log qui s'installe de la façon suivante :
```
composer require laminas/laminas-log
```

## Envoyer un mail 

* Pour rédiger un mail, laminas-mail propose un objet Message qui contient l'ensemble des méthodes nécessaire à la définition des éléments du mail
``` php
use Laminas\Mail;

$mail = new Mail\Message();
$mail->setBody('This is the text of the email.');
$mail->setFrom('Freeaqingme@example.org', "Sender's name");
$mail->addTo('Matthew@example.com', 'Name of recipient');
$mail->setSubject('TestSubject');
```
* Pour envoyer le mail on utilise un Transporteur qui définit l'outil utilisé pour envoyé le mail par exemple avec Sendmail : 
``` php
$transport = new Mail\Transport\Sendmail();
$transport->send($mail);
```
* Il esg également possible de définir son propre transport via SMTP de la façon suivante : 
``` php
use Laminas\Mail\Transport\Smtp as SmtpTransport;
use Laminas\Mail\Transport\SmtpOptions;

// Setup SMTP transport
$transport = new SmtpTransport();
$options   = new SmtpOptions([
    'name' => 'localhost.localdomain',
    'host' => '127.0.0.1',
    'port' => 25,
]);
$transport->setOptions($options);
```
* Il est également possible d'envoyer des pièces jointes, voir la documentation : https://docs.laminas.dev/laminas-mail/message/attachments/  

## Ajout de log et enregistrement en BDD

* Le composant laminas-log permet d'écrire des logs à différents endroits via la fonction log et ses dérivé correspondants aux différents niveaux de log : 
``` php
$logger->log(Laminas\Log\Logger::INFO, 'Informational message');
$logger->info('Informational message');

$logger->log(Laminas\Log\Logger::EMERG, 'Emergency message');
$logger->emerg('Emergency message');
``` 
* Il est possible de définir un canal de log différents ou supplémentaire en utilisant l'objet Writer fournit par le composant, exemple ici avec un enregistrement en BDD :
``` php
$dbconfig = [
    // Sqlite Configuration
    'driver' => 'Pdo',
    'dsn' => 'sqlite:' . __DIR__ . '/tmp/sqlite.db',
];
$db = new Laminas\Db\Adapter\Adapter($dbconfig);

$writer = new Laminas\Log\Writer\Db($db, 'log_table_name');
$logger = new Laminas\Log\Logger();
$logger->addWriter($writer);

$logger->info('Informational message');
``` 
* D'autres canaux sont disponibles : https://docs.laminas.dev/laminas-log/writers/ et il est possible de définir son propre canal en définissant son propre Writer

-> Ajouter l'envoi d'un mail à un utilisateur quand la fiche de l'un de ses jeux (qu'il a dans sa collection) est éditée
-> Enregistrer en BDD des logs pour savoir par qui et quand ont été créé / modifiés / supprimés les jeux et les utilisateurs. 