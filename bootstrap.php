<?php

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once __DIR__ . '/vendor/autoload.php';

$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: [__DIR__ . '/model'],
    isDevMode: true
);

$connectionParams = [
    'dbname' => 'magazord_db',
    'user' => 'root',
    'password' => 'root',
    'host' => 'db', // Nome do serviço no docker-compose
    'driver' => 'pdo_mysql',
];

$connection = DriverManager::getConnection($connectionParams, $config);
$entityManager = new EntityManager($connection, $config);

return $entityManager;
