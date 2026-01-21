<?php
require '../vendor/autoload.php';
use App\Config\Database;
use App\Repositories\BaseRepository;
$pdo = Database::getConnection();
BaseRepository::setDB($pdo);



require '../src/Routes/web.php';

