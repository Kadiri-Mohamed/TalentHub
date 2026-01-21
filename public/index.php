<?php
require '../vendor/autoload.php';
require '../src/Routes/web.php';

use App\Config\Database;
use App\Repositories\BaseRepository;


$pdo = Database::getConnection();
BaseRepository::setDB($pdo);

