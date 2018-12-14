<?php
require_once __DIR__ . '/../vendor/autoload.php'; // Autoload files using Composer autoload

$classbd = new \babirondo\classbd\db();
$Postgres = $classbd->conecta("Postgres", "172.18.0.7", "authentication", "postgres", "postgres");
echo "<BR> Postgres ".$Postgres->conectado;

$Mongo = $classbd->conecta("Mongo", "172.18.0.7", "authentication", "postgres", "postgres", 27017);
echo "<BR> Mongo ".$Mongo->conectado;
