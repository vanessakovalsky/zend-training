# Mettre en cache les données sur notre application

Cet exercice a pour objectif : 
* de créer son cache
* de mettre des données en cache
* de récupérer des données à partir du cache

## Installer le composant de cache

* Il est nécessaire d'installer le composant de Cache pour Laminas :
```
composer require laminas/laminas-cache
```

## Définir son cache
* Il est nécessaire de définir le cache sue l'on souhaite utiliser en définissant un adapter.
* De nombreux adaptateurs sont disponibles : https://docs.laminas.dev/laminas-cache/storage/adapter/ 
* Une fois le choix effectué, il faut l'indiqué à Laminas, pour cela le moyen le plus simple de le faire est de passé par la StorageFactory : 
``` php
$cache = StorageFactory::factory([
    'adapter' => [
    'name' => 'apc',
    'options' => ['ttl' => 3600],
    ],
    'plugins' => [
    'exception_handler' => ['throw_exceptions' => false],
    ],
]);
```

## Mettre en cache des données et les récupérer 
* Il est possible d'utiliser le standard PSR-16 via l'objet SimpleCacheDecorator pour mettre en cache des données sous forme clé=>valeur. Voici un exemple : 
``` php
use Laminas\Cache\StorageFactory;
use Laminas\Cache\Psr\SimpleCache\SimpleCacheDecorator;

$storage = StorageFactory::factory([
    'adapter' => [
        'name'    => 'apc',
        'options' => [],
    ],
]);

$cache = new SimpleCacheDecorator($storage);
// Use has() to determine whether to fetch the value or calculate it:
$value = $cache->has('someKey') ? $cache->get('someKey') : calculateValue();
if (! $cache->has('someKey')) {
    $cache->set('someKey', $value);
}

// Or use a default value:
$value = $cache->get('someKey', $defaultValue);
```
* Laminas propose également plusieurs pattern pour mettre en cache différents éléments : https://docs.laminas.dev/laminas-cache/pattern/intro/ 
* Cela se traduit si l'on utilise le PAtternFactory par des cas comme celui-ci :
``` php
$filter       = new Laminas\Filter\RealPath();
$cachedFilter = Laminas\Cache\PatternFactory::factory('object', [
    'object'     => $filter,
    'object_key' => 'RealpathFilter',
    'storage'    => 'apc',

    // The realpath filter doesn't output anything
    // so the output don't need to be caught and cached
    'cache_output' => false,
]);

$path = $cachedFilter->call("filter", ['/www/var/path/../../mypath']);
``` 

## Execution de Benchmarks sur le code PHP

* En plus de la mise en place du cache, Laminas-cache propose des scripts pour testser les performances à l'aide du framework phpbench
* Voir la documentation ici : https://github.com/phpbench/phpbench 