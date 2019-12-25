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

function createPath($db, $id, $except = null) {

    $data = $db->row('SELECT parent_id, name FROM category WHERE category_id = '.$id);

    if($data != NULL) {
        $name = $data->name;
        if($data->parent_id == 0) {
            return "$name > ";
        } else {
            if(!empty($except) && $except == $name)
                return createPath($db, $data->parent_id, $except)." ".$name;
        }
        return createPath($db, $data->parent_id, $except). " $name >";
    }

}

$data = $db->result('SELECT category_id, url, name, level FROM category WHERE sub_completed = 0 AND parent_id > 0 AND name IS NOT NULL ORDER BY category_id ASC LIMIT 7');

if($data != NULL) {
    foreach ($data as $d) {

        echo '<h3>'.createPath($db, $d->category_id).'</h3>';
        $count = 0;
        $success = 0;

        $level = $d->level + 2;

        $categories = $sahibinden->parser($d->url, $level);
        foreach ($categories as $url => $category) {
            $insert = $db->insert('category', array(
                'parent_id' => $d->category_id,
                'name' => $category,
                'url' => $url,
                'level' => $d->level + 1
            ));

            if($insert == true)
                $success++;

            $count++;

        }

        $db->update('category', array('sub_completed' => 1), array('category_id' => $d->category_id));

        echo 'Toplam: '.$count.', Başarılı: '.$success.'<hr>';

    }
}

