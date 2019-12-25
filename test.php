<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ERROR);

$hostname = 'localhost';
$username = 'root';
$password = 'root';
$database = 'dbname';
$port = 3306;

define('DB_SERVER', $hostname);
define('DB_USERNAME', $username);
define('DB_PASSWORD', $password);
define('DB_NAME', $database);
define('DB_PORT', $port);

require_once 'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
require_once 'simple_html_dom.php';
require_once 'Sahibinden.php';
require_once 'Database.php';

$sahibinden = new Sahibinden();
$db = new Database();

$parent_id = filter_input(INPUT_GET, 'parent_id', FILTER_SANITIZE_NUMBER_INT);
$parent_id = !empty($parent_id) ? $parent_id : 0;

$data = $db->result('SELECT category_id, name FROM category WHERE parent_id = '.$parent_id.' ORDER BY name ASC');

if($data != NULL) {
    echo '<ul>';
    foreach ($data as $d) {
        echo '<li><a href="test.php?parent_id='.$d->category_id.'">'.$d->name.'</a></li>';
    }
    echo '</ul>';
}

