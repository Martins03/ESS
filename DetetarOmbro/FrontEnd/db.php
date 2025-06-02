<?php
function getDB() {
    $db = new PDO('mysql:host=localhost;dbname=ess_projeto;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}





