<?php
require '../vendor/autoload.php';
use App\Config\Database;
use App\Models\Tag;
use App\Repositories\BaseRepository;
$pdo = Database::getConnection();
BaseRepository::setDB($pdo);



require '../src/Routes/web.php';
use App\Repositories\TagRepository;

$appRepo = new TagRepository();

$applications = $appRepo->getAll();

echo '<pre>';
var_dump($applications);
echo '</pre>';
